<?php

namespace App\Http\Controllers;

use App\Models\DownloadLog;
use App\Services\VideoDownloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function __construct(
        private VideoDownloadService $downloadService,
    ) {}

    // ─────────────────────────────────────────────────────────────
    //  GET /history — Blade Page
    // ─────────────────────────────────────────────────────────────

    /**
     * Tampilkan halaman History dengan download logs milik user.
     */
    public function index(Request $request)
    {
        // History page is fully AJAX-driven via Alpine.js → /api/history
        return view('history');
    }

    // ─────────────────────────────────────────────────────────────
    //  GET /api/history — JSON for Alpine.js
    // ─────────────────────────────────────────────────────────────

    /**
     * API endpoint untuk Alpine.js — filtering, search, dan pagination.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $query = DownloadLog::where('user_id', Auth::id())
            ->where('status', 'sukses')
            ->latest();

        // Platform filter
        if ($request->filled('platform') && $request->platform !== 'Semua') {
            $query->forPlatform(strtolower($request->platform));
        }

        // Search by title
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $logs = $query->paginate(15);

        // Transform data — parse download_url JSON & expose re-download info
        $items = $logs->map(function (DownloadLog $log) {
            $downloadInfo = null;
            $canRedownload = false;

            // Parse download_url — bisa berupa JSON (yt-dlp route) atau URL langsung (source_extraction)
            if ($log->download_url) {
                $decoded = json_decode($log->download_url, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['original_url'])) {
                    // Format baru: JSON dengan original_url + format_id
                    $downloadInfo  = $decoded;
                    $canRedownload = true;
                } else {
                    // Format lama: URL langsung (source_extraction)
                    $downloadInfo  = ['direct_url' => $log->download_url];
                    $canRedownload = true;
                }
            } elseif ($log->original_url && !str_starts_with($log->original_url, 'private-extraction://')) {
                // Fallback: punya original_url tapi belum ada download_url → bisa re-process
                $downloadInfo = [
                    'original_url' => $log->original_url,
                    'format_id'    => 'best[ext=mp4]/best',
                    'ext'          => $log->file_extension ?? 'mp4',
                ];
                $canRedownload = true;
            }

            return [
                'id'                  => $log->id,
                'video_title'         => $log->video_title,
                'thumbnail_url'       => $log->thumbnail_url,
                'platform_name'       => $log->platform_name,
                'platform_icon'       => $log->platform_icon,
                'duration_string'     => $log->duration_string,
                'uploader'            => $log->uploader,
                'file_extension'      => $log->file_extension ?? 'mp4',
                'format_quality'      => $log->format_quality,
                'formatted_file_size' => $log->formatted_file_size,
                'created_at'          => $log->created_at,
                'can_redownload'      => $canRedownload,
                'download_info'       => $downloadInfo,
                'original_url'        => $log->original_url,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'per_page'     => $logs->perPage(),
                'total'        => $logs->total(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  POST /api/history/log — Log Per-Format Download
    // ─────────────────────────────────────────────────────────────

    /**
     * Dipanggil frontend saat user klik format download.
     * Membuat log entry dengan format yang benar (video ATAU audio).
     */
    public function logDownload(Request $request): JsonResponse
    {
        $request->validate([
            'original_url'  => 'required|string|max:2000',
            'platform_name' => 'required|string|max:50',
            'video_title'   => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:2000',
            'duration'      => 'nullable|integer',
            'duration_str'  => 'nullable|string|max:20',
            'uploader'      => 'nullable|string|max:255',
            'format_id'     => 'nullable|string|max:200',
            'ext'           => 'nullable|string|max:10',
            'quality'       => 'nullable|string|max:50',
            'filesize'      => 'nullable|integer',
        ]);

        DownloadLog::create([
            'user_id'         => Auth::id(),
            'original_url'    => $request->original_url,
            'platform_name'   => strtolower($request->platform_name),
            'video_title'     => $request->video_title,
            'thumbnail_url'   => $request->thumbnail_url,
            'duration'        => $request->duration,
            'duration_string' => $request->duration_str,
            'uploader'        => $request->uploader,
            'format_quality'  => $request->quality,
            'file_extension'  => $request->ext ?? 'mp4',
            'file_size'       => $request->filesize,
            'download_method' => 'yt-dlp',
            'status'          => 'sukses',
            'download_url'    => json_encode([
                'original_url' => $request->original_url,
                'format_id'    => $request->format_id ?? 'best[ext=mp4]/best',
                'ext'          => $request->ext ?? 'mp4',
                'expires_at'   => now()->addHours(6)->timestamp,
            ]),
        ]);

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    //  GET /api/history/{log}/redownload — Re-process & Download
    // ─────────────────────────────────────────────────────────────

    /**
     * Re-process video dari original_url menggunakan yt-dlp.
     * Mengembalikan URL download yang fresh (tidak expired).
     * Mendukung audio (m4a) maupun video.
     */
    public function redownload(DownloadLog $log): JsonResponse
    {
        // Otorisasi
        if ($log->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $originalUrl = $log->original_url;

        // Cek apakah ini private extraction (tidak bisa re-process)
        if (!$originalUrl || str_starts_with($originalUrl, 'private-extraction://')) {
            $decoded = $log->download_url ? json_decode($log->download_url, true) : null;
            if ($decoded && isset($decoded['direct_url'])) {
                return response()->json([
                    'success'      => true,
                    'method'       => 'direct',
                    'download_url' => '/video/download?' . http_build_query([
                        'url'   => $decoded['direct_url'],
                        'title' => $log->video_title ?? 'video',
                        'ext'   => $log->file_extension ?? 'mp4',
                    ]),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Video ini adalah hasil ekstraksi manual dan tidak dapat diunduh ulang secara otomatis.',
            ], 422);
        }

        // Parse stored format info
        $decoded  = $log->download_url ? json_decode($log->download_url, true) : null;
        $ext      = $log->file_extension ?? ($decoded['ext'] ?? 'mp4');
        $platform = $log->platform_name ?? 'other';

        // Tentukan apakah ini audio atau video berdasarkan extension yang tersimpan
        $isAudio  = in_array(strtolower($ext), ['m4a', 'mp3', 'ogg', 'wav', 'aac']);

        // Re-process via yt-dlp untuk dapat format URLs yang fresh
        $result = $this->downloadService->executeYtDlp($originalUrl, $platform);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat ulang video: ' . ($result['message'] ?? 'Tidak diketahui'),
            ], 422);
        }

        $metadata = $result['metadata'];
        $formats  = $this->downloadService->buildFormats($metadata, $platform);

        // Pilih format sesuai tipe (audio vs video)
        if ($isAudio) {
            $bestFmt = collect($formats)->firstWhere('type', 'audio')
                    ?? collect($formats)->first();
        } else {
            $bestFmt = collect($formats)->firstWhere('type', 'video')
                    ?? collect($formats)->first();
        }

        if (!$bestFmt) {
            return response()->json(['success' => false, 'message' => 'Format tidak ditemukan.'], 422);
        }

        $params = http_build_query([
            'original_url' => $originalUrl,
            'format_id'    => $bestFmt['format_id'],
            'url'          => $bestFmt['url'] ?? '',
            'title'        => $log->video_title ?? 'video',
            'ext'          => $bestFmt['ext'] ?? $ext,
        ]);

        return response()->json([
            'success'      => true,
            'method'       => 'reprocess',
            'download_url' => '/video/download?' . $params,
            'title'        => $log->video_title,
            'ext'          => $bestFmt['ext'] ?? $ext,
            'quality'      => $bestFmt['quality'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  DELETE /api/history/{log} — Hapus Entry
    // ─────────────────────────────────────────────────────────────

    /**
     * Hapus satu entry history. Hanya milik user sendiri.
     */
    public function destroy(DownloadLog $log): JsonResponse
    {
        if ($log->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat berhasil dihapus.',
        ]);
    }
}
