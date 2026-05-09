<?php

namespace App\Http\Controllers;

use App\Models\DownloadLog;
use App\Services\VideoDownloadService;
use App\Services\VideoExtractorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use GuzzleHttp\Client;

class VideoController extends Controller
{
    public function __construct(
        private VideoDownloadService $downloadService,
        private VideoExtractorService $extractorService,
    ) {}

    // ─────────────────────────────────────────────────────────────
    //  POST /video/process — Omni-Input URL Processing
    // ─────────────────────────────────────────────────────────────

    public function processUrl(Request $request): JsonResponse
    {
        $request->validate([
            'url'      => 'required|url',
            'platform' => 'nullable|string|in:youtube,tiktok,facebook,instagram',
        ]);
        $url = trim($request->input('url'));

        // 1. Deteksi Platform — prioritaskan pilihan user, fallback ke auto-detect
        $platform = $request->input('platform')
            ?: ($this->downloadService->detectPlatform($url) ?? 'other');

        // 2. Jalankan yt-dlp via Service (dengan opsi platform-aware)
        $result = $this->downloadService->executeYtDlp($url, $platform);

        // 3. Tangani Kegagalan
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal memproses video.',
            ], 422);
        }

        // 4. Ekstrak Data dari Metadata
        $metadata = $result['metadata'];
        $videoData = $this->downloadService->buildVideoData($metadata, $url);
        $formats   = $this->downloadService->buildFormats($metadata, $platform);

        // 5. Return Response (format sesuai Alpine.js frontend)
        return response()->json([
            'success'  => true,
            'platform' => $platform,
            'video'    => $videoData,
            'formats'  => $formats,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  POST /video/extract-private — HTML Source Code Extraction
    // ─────────────────────────────────────────────────────────────

    public function extractPrivate(Request $request): JsonResponse
    {
        // ── Rate Limiting (5 requests per minute per IP) ────────
        $key = 'extract-private:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'error'   => 'rate_limited',
                'message' => "Terlalu banyak permintaan. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }
        RateLimiter::hit($key, 60);

        // ── Validasi Request ────────────────────────────────────
        $request->validate([
            'html'     => 'required|string|min:100',
            'platform' => 'nullable|string|in:facebook,instagram,tiktok',
        ]);

        $html     = $request->input('html');
        $platform = $request->input('platform', 'facebook');

        // ── Validasi Ukuran HTML ────────────────────────────────
        $sizeCheck = $this->extractorService->validateHtmlSize($html);
        if (!$sizeCheck['valid']) {
            return response()->json([
                'success' => false,
                'error'   => 'invalid_html_size',
                'message' => $sizeCheck['message'],
            ], 422);
        }

        // ── Sanitasi: strip <script> execution attempts ─────────
        // Kita hanya parse regex, tidak exec — tapi log jika ada XSS attempt
        if (preg_match('/<script[^>]*>.*?(document\.cookie|eval\s*\(|window\.location)/is', $html)) {
            Log::warning('Possible XSS attempt in extract-private', [
                'ip'       => $request->ip(),
                'platform' => $platform,
            ]);
        }

        // ── Eksekusi Extraction (platform-aware) ────────────────
        $startTime = microtime(true);
        $videos = $this->extractorService->extractFromHtml($html, $platform);
        $elapsed = round((microtime(true) - $startTime) * 1000, 1);

        Log::info('Private extraction completed', [
            'platform'   => $platform,
            'html_size'  => strlen($html),
            'found'      => count($videos),
            'elapsed_ms' => $elapsed,
            'ip'         => $request->ip(),
        ]);

        if (empty($videos)) {
            return response()->json([
                'success' => false,
                'error'   => 'no_video_found',
                'message' => $this->getNoVideoMessage($platform),
            ], 404);
        }

        // ── Log ke database jika login ──────────────────────────
        if (Auth::check()) {
            try {
                DownloadLog::create([
                    'user_id'         => Auth::id(),
                    'original_url'    => 'private-extraction://' . $platform,
                    'platform_name'   => $platform,
                    'video_title'     => 'Private Video (' . ucfirst($platform) . ')',
                    'format_quality'  => $videos[0]['quality'] ?? 'unknown',
                    'download_method' => 'source_extraction',
                    'download_url'    => $videos[0]['url'] ?? null,
                    'status'          => 'sukses',
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to log private extraction', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // ── Response ────────────────────────────────────────────
        return response()->json([
            'success'    => true,
            'platform'   => $platform,
            'method'     => 'source_code_extraction',
            'videos'     => $videos,
            'count'      => count($videos),
            'elapsed_ms' => $elapsed,
            'message'    => 'Berhasil menemukan ' . count($videos) . ' link video.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  GET /video/download — Proxy Download Stream
    // ─────────────────────────────────────────────────────────────

    public function proxyDownload(Request $request)
    {
        $request->validate([
            'url'          => 'nullable|url',
            'original_url' => 'nullable|url',
            'format_id'    => 'nullable|string',
            'title'        => 'nullable|string',
            'ext'          => 'nullable|string',
        ]);

        $title    = $request->query('title', 'video_' . time());
        $title    = preg_replace('/[^A-Za-z0-9_\- ]/', '', $title);
        $ext      = $request->query('ext', 'mp4');
        $filename = $title . '.' . $ext;

        $originalUrl = $request->query('original_url');
        $formatId    = $request->query('format_id');
        $directUrl   = $request->query('url');

        return response()->streamDownload(function () use ($originalUrl, $formatId, $directUrl) {
            if ($originalUrl && $formatId) {
                $os         = PHP_OS_FAMILY;
                $binaryName = $os === 'Windows' ? 'yt-dlp.exe' : 'yt-dlp';
                $ytDlpPath  = storage_path('app/bin/' . $binaryName);
                $tmpDir     = storage_path('app/tmp');

                // Pastikan direktori tmp ada dan writable
                if (!is_dir($tmpDir)) {
                    @mkdir($tmpDir, 0755, true);
                }

                putenv('TEMP=' . $tmpDir);
                putenv('TMP=' . $tmpDir);

                $commandBase = escapeshellarg($ytDlpPath) . ' -f ' . escapeshellarg($formatId) . ' ' . escapeshellarg($originalUrl);

                $isCombo = str_contains($formatId, '+');

                if ($isCombo) {
                    // Tipe B: Unduh Lokal & Jahit via ffmpeg
                    $downloadDir = storage_path('app/public/downloads');
                    if (!file_exists($downloadDir)) {
                        @mkdir($downloadDir, 0755, true);
                    }
                    $tempId   = uniqid('vid_');
                    $tempFile = $downloadDir . '/' . $tempId . '.mp4';

                    $command = $commandBase . ' --merge-output-format mp4 -o ' . escapeshellarg($tempFile);
                    exec($command . ' 2>&1', $output, $returnVar);

                    if ($returnVar === 0 && file_exists($tempFile)) {
                        readfile($tempFile);
                        unlink($tempFile);
                        return;
                    }

                    \Log::error("Failed to merge combo video: " . implode("\n", $output));
                    echo "Gagal memproses video beresolusi tinggi (FFmpeg merge failed).";
                } else {
                    // Tipe A: Stream Langsung
                    $command = $commandBase . ' -o -';
                    passthru($command);
                }
            } elseif ($directUrl) {
                // Fallback Guzzle untuk URL direct (termasuk private video extraction)
                try {
                    $client   = new Client();
                    $response = $client->request('GET', $directUrl, [
                        'stream'  => true,
                        'verify'  => false,
                        'timeout' => 300,
                        'headers' => [
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                            'Referer'    => $this->guessReferer($directUrl),
                        ],
                    ]);

                    $body = $response->getBody();
                    while (!$body->eof()) {
                        echo $body->read(8192);
                        flush();
                    }
                } catch (\Exception $e) {
                    Log::error('Proxy download failed', [
                        'url'   => $directUrl,
                        'error' => $e->getMessage(),
                    ]);
                    echo "\nError downloading media.";
                }
            } else {
                echo "\nNo valid URL provided.";
            }
        }, $filename);
    }

    // ─────────────────────────────────────────────────────────────
    //  Private Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Pesan error yang lebih informatif per-platform.
     */
    private function getNoVideoMessage(string $platform): string
    {
        return match ($platform) {
            'facebook'  => 'Tidak ditemukan URL video dalam source code Facebook. '
                         . 'Pastikan video sedang diputar saat Anda menyalin source code (Ctrl+U).',
            'instagram' => 'Tidak ditemukan URL video dalam source code Instagram. '
                         . 'Coba buka video/reel di tab baru, putar dulu, lalu salin source code.',
            'tiktok'    => 'Tidak ditemukan URL video dalam source code TikTok. '
                         . 'Pastikan halaman sudah termuat sepenuhnya sebelum menyalin source code.',
            default     => 'Tidak ditemukan URL video .mp4 dalam source code yang diberikan. '
                         . 'Pastikan Anda menyalin seluruh source code halaman.',
        };
    }

    /**
     * Tebak Referer header dari URL video CDN untuk menghindari 403.
     */
    private function guessReferer(string $url): string
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');

        if (str_contains($host, 'fbcdn') || str_contains($host, 'facebook')) {
            return 'https://www.facebook.com/';
        }
        if (str_contains($host, 'cdninstagram') || str_contains($host, 'instagram')) {
            return 'https://www.instagram.com/';
        }
        if (str_contains($host, 'tiktok') || str_contains($host, 'musical.ly')) {
            return 'https://www.tiktok.com/';
        }

        return 'https://www.google.com/';
    }
}
