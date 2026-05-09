<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class VideoDownloadService
{
    private string $ytDlpPath;

    public function __construct()
    {
        $binaryName     = PHP_OS_FAMILY === 'Windows' ? 'yt-dlp.exe' : 'yt-dlp';
        $this->ytDlpPath = storage_path('app/bin/' . $binaryName);
    }

    // ─────────────────────────────────────────────────────────────
    //  Platform Detection
    // ─────────────────────────────────────────────────────────────

    public function detectPlatform(string $url): ?string
    {
        $url = strtolower($url);
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) return 'youtube';
        if (str_contains($url, 'tiktok.com'))                                      return 'tiktok';
        if (str_contains($url, 'facebook.com') || str_contains($url, 'fb.watch') || str_contains($url, 'fb.com')) return 'facebook';
        if (str_contains($url, 'instagram.com') || str_contains($url, 'instagr.am')) return 'instagram';
        return null;
    }

    // ─────────────────────────────────────────────────────────────
    //  yt-dlp Execution
    // ─────────────────────────────────────────────────────────────

    public function isBinaryAvailable(): bool
    {
        return file_exists($this->ytDlpPath);
    }

    /**
     * Eksekusi yt-dlp --dump-json dengan opsi platform-aware.
     */
    public function executeYtDlp(string $url, string $platform = 'other'): array
    {
        if (!$this->isBinaryAvailable()) {
            Log::error('yt-dlp tidak ditemukan di: ' . $this->ytDlpPath);
            return ['success' => false, 'error' => 'binary_not_found', 'message' => 'Downloader engine tidak ditemukan di server.'];
        }

        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0755, true);

        $args = [$this->ytDlpPath, '--dump-json', '--no-warnings', '--no-playlist'];

        // TikTok: spoof User-Agent untuk kurangi pemblokiran bot
        if ($platform === 'tiktok') {
            array_push($args,
                '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                '--add-header', 'Referer:https://www.tiktok.com/'
            );
        }

        $args[] = $url;

        $process = new Process($args, null, array_merge(getenv(), [
            'TEMP' => $tmpDir, 'TMP' => $tmpDir, 'TMPDIR' => $tmpDir, 'HOME' => $tmpDir,
        ]));
        $process->setTimeout(120);

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                $stderr = $process->getErrorOutput();

                if ($this->isPrivateVideoError($stderr, $platform)) {
                    return [
                        'success'    => false,
                        'error'      => 'private_video',
                        'is_private' => true,
                        'message'    => 'Video ini bersifat privat atau dibatasi dan saat ini tidak didukung untuk diunduh.',
                    ];
                }

                if ($platform === 'tiktok' && $this->isTikTokBlocked($stderr)) {
                    return [
                        'success'    => false,
                        'error'      => 'private_video',
                        'is_private' => true,
                        'message'    => 'Akses otomatis diblokir oleh TikTok. Video ini mungkin privat atau dibatasi.',
                    ];
                }

                Log::warning('yt-dlp gagal', ['url' => $url, 'platform' => $platform, 'stderr' => $stderr, 'code' => $process->getExitCode()]);

                return ['success' => false, 'error' => 'download_failed', 'message' => 'Gagal memproses video. Pastikan URL valid dan video bersifat publik.'];
            }

            $metadata = json_decode($process->getOutput(), true);
            if (!$metadata) {
                return ['success' => false, 'error' => 'parse_error', 'message' => 'Gagal membaca metadata video.'];
            }

            return ['success' => true, 'metadata' => $metadata];

        } catch (\Exception $e) {
            Log::error('Exception yt-dlp', ['url' => $url, 'message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'server_error', 'message' => 'Terjadi kesalahan internal. Silakan coba lagi.'];
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  Format Builder — Platform-Specific
    // ─────────────────────────────────────────────────────────────

    /**
     * Entry point: pilih strategi format berdasarkan platform.
     */
    public function buildFormats(array $metadata, string $platform): array
    {
        if ($platform === 'youtube') {
            return $this->extractYouTubeFormats($metadata);
        }
        return $this->extractSimpleFormats($metadata, $platform);
    }

    /**
     * YouTube: parse DASH streams H.264 + audio secara terpisah.
     * Menghasilkan tombol per resolusi (1080p, 720p, 480p) + Audio Only.
     */
    private function extractYouTubeFormats(array $metadata): array
    {
        $formats  = [];
        $duration = $metadata['duration'] ?? 0;

        // Cari stream H.264 (avc1) untuk resolusi spesifik
        if (isset($metadata['formats']) && is_array($metadata['formats'])) {
            $targetHeights = [1080, 720, 480, 360];

            foreach ($targetHeights as $targetH) {
                foreach ($metadata['formats'] as $fmt) {
                    $vcodec   = strtolower($fmt['vcodec'] ?? 'none');
                    $height   = (int)($fmt['height'] ?? 0);
                    $isH264   = str_starts_with($vcodec, 'avc1') || str_starts_with($vcodec, 'h264');
                    $hasVideo = $vcodec !== 'none' && $isH264;

                    if ($hasVideo && $height === $targetH) {
                        $filesize = $fmt['filesize'] ?? $fmt['filesize_approx'] ?? null;
                        if (!$filesize && isset($fmt['tbr']) && $duration > 0) {
                            $filesize = (int)(($fmt['tbr'] * 1000 / 8) * $duration);
                        }

                        $formats[] = [
                            'format_id' => $fmt['format_id'] . '+140', // video DASH + AAC audio
                            'quality'   => $targetH . 'p',
                            'ext'       => 'mp4',
                            'filesize'  => $filesize,
                            'url'       => '',  // proxy akan handle via format_id
                            'type'      => 'video',
                        ];
                        break; // Sudah dapat resolusi ini, lanjut ke berikutnya
                    }
                }
            }
        }

        // Fallback: jika tidak ada H.264 yang ditemukan, gunakan format selectors
        if (empty($formats)) {
            $formats = [
                ['format_id' => 'bestvideo[height<=1080][vcodec^=avc1]+bestaudio[ext=m4a]/best[ext=mp4]/best', 'quality' => '1080p',  'ext' => 'mp4', 'filesize' => null, 'url' => '', 'type' => 'video'],
                ['format_id' => 'bestvideo[height<=720][vcodec^=avc1]+bestaudio[ext=m4a]/best[height<=720]/best',  'quality' => '720p',   'ext' => 'mp4', 'filesize' => null, 'url' => '', 'type' => 'video'],
                ['format_id' => 'bestvideo[height<=480][vcodec^=avc1]+bestaudio[ext=m4a]/best[height<=480]/best',  'quality' => '480p',   'ext' => 'mp4', 'filesize' => null, 'url' => '', 'type' => 'video'],
            ];
        }

        // Audio Only
        $audioFilesize = $duration > 0 ? (int)(128 * 1000 / 8 * $duration) : null;
        $formats[] = [
            'format_id' => '140',
            'quality'   => 'Audio Only',
            'ext'       => 'm4a',
            'filesize'  => $audioFilesize,
            'url'       => '',
            'type'      => 'audio',
        ];

        return $formats;
    }

    /**
     * Simple format untuk TikTok / Facebook / Instagram.
     * Tidak parse formats[] — gunakan top-level metadata URL (best combined stream).
     * Hanya 2 tombol: Video (best) + Audio Only.
     */
    private function extractSimpleFormats(array $metadata, string $platform): array
    {
        $duration  = $metadata['duration'] ?? 0;
        $directUrl = $metadata['url'] ?? '';

        // Estimasi filesize video dari bitrate
        $videoFilesize = $metadata['filesize'] ?? $metadata['filesize_approx'] ?? null;
        if (!$videoFilesize && isset($metadata['tbr']) && $duration > 0) {
            $videoFilesize = (int)($metadata['tbr'] * 1000 / 8 * $duration);
        }

        $audioFilesize = $duration > 0 ? (int)(128 * 1000 / 8 * $duration) : null;

        return [
            [
                'format_id' => 'best[ext=mp4]/best',
                'quality'   => 'Video',
                'ext'       => 'mp4',
                'filesize'  => $videoFilesize,
                'url'       => $directUrl, // CDN direct URL sebagai fallback
                'type'      => 'video',
            ],
            [
                'format_id' => 'bestaudio[ext=m4a]/bestaudio/best',
                'quality'   => 'Audio Only',
                'ext'       => 'm4a',
                'filesize'  => $audioFilesize,
                'url'       => '',
                'type'      => 'audio',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  Video Data Builder
    // ─────────────────────────────────────────────────────────────

    /**
     * Bangun data ringkas video dengan fallback thumbnail dari thumbnails[].
     */
    public function buildVideoData(array $metadata, string $url): array
    {
        // Pilih thumbnail terbaik
        $thumbnail = $metadata['thumbnail'] ?? null;

        if (!$thumbnail && !empty($metadata['thumbnails'])) {
            $thumbs = array_filter($metadata['thumbnails'], fn($t) => !empty($t['url']));
            if (!empty($thumbs)) {
                usort($thumbs, fn($a, $b) => ($b['width'] ?? 0) <=> ($a['width'] ?? 0));
                $thumbnail = reset($thumbs)['url'];
            }
        }

        return [
            'id'           => $metadata['id'] ?? null,
            'title'        => $metadata['title'] ?? 'Untitled',
            'thumbnail'    => $thumbnail,
            'duration'     => $metadata['duration'] ?? 0,
            'duration_str' => $metadata['duration_string'] ?? '0:00',
            'uploader'     => $metadata['uploader'] ?? ($metadata['channel'] ?? 'Unknown'),
            'view_count'   => $metadata['view_count'] ?? 0,
            'webpage_url'  => $metadata['webpage_url'] ?? $url,
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  Private Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Indikator private VIDEO yang ketat — hanya login-wall sesungguhnya.
     * JANGAN masukkan error generik seperti '403' atau 'not available' di sini kecuali untuk platform tertentu.
     */
    private function isPrivateVideoError(string $stderr, string $platform = 'other'): bool
    {
        $indicators = [
            'Private video',
            'This video is private',
            'Sign in to confirm your age',
            'This content is age-restricted',
            'members-only',
            'Login required',
            'login required',
            'requires authentication',
            'HTTP Error 401',
        ];

        // Jika platform adalah facebook atau instagram, kita lebih agresif memicu modal private
        // karena seringkali ini akibat video diprivat namun yt-dlp hanya memberikan pesan error generik.
        if (in_array($platform, ['facebook', 'instagram'])) {
            $indicators = array_merge($indicators, [
                'Cannot parse data',
                'Video unavailable',
                'HTTP Error 404',
                'HTTP Error 403',
                'Unsupported URL',
            ]);
        }

        foreach ($indicators as $ind) {
            if (str_contains($stderr, $ind)) return true;
        }
        return false;
    }

    /**
     * Deteksi pemblokiran TikTok (bot-block / geo-block).
     * Sengaja TIDAK mengandung 'HTTP Error 403' karena YouTube juga bisa return 403.
     */
    private function isTikTokBlocked(string $stderr): bool
    {
        // Hanya cek jika dipanggil untuk TikTok saja (sudah difilter di caller)
        $indicators = [
            'Please wait',
            'CAPTCHA',
            'verify you are human',
            'Douyin',
            'is not available in your country',
        ];

        foreach ($indicators as $ind) {
            if (str_contains($stderr, $ind)) return true;
        }
        return false;
    }
}
