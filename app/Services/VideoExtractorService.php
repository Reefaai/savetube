<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VideoExtractorService
{
    // ─────────────────────────────────────────────────────────────
    //  Public API — Platform-Aware Extraction
    // ─────────────────────────────────────────────────────────────

    /**
     * Entry point: route ke extractor berdasarkan platform.
     *
     * @return array<int, array{quality: string, url: string}>
     */
    public function extractFromHtml(string $html, string $platform = 'facebook'): array
    {
        $results = match ($platform) {
            'facebook'  => $this->extractFacebook($html),
            'instagram' => $this->extractInstagram($html),
            'tiktok'    => $this->extractTikTok($html),
            default     => $this->extractGeneric($html),
        };

        // Deduplicate berdasarkan URL
        $seen = [];
        $unique = [];
        foreach ($results as $item) {
            $normalized = $this->normalizeUrl($item['url']);
            if (!isset($seen[$normalized])) {
                $seen[$normalized] = true;
                $unique[] = $item;
            }
        }

        Log::info('VideoExtractor: extraction complete', [
            'platform'    => $platform,
            'found_count' => count($unique),
        ]);

        return $unique;
    }

    // ─────────────────────────────────────────────────────────────
    //  Facebook Extractor
    // ─────────────────────────────────────────────────────────────

    /**
     * Ekstrak URL video dari HTML source Facebook.
     * Mendukung format Facebook 2024+ maupun format lama.
     */
    private function extractFacebook(string $html): array
    {
        $results = [];

        // ── 1. Facebook 2024+ JSON embedded ─────────────────────
        // Format modern: "playable_url_quality_hd":"https://..."
        $patterns2024 = [
            'HD' => [
                '/\\\\?["\'"]?playable_url_quality_hd\\\\?["\'"]?\s*:\s*\\\\?["\'"]?(.*?)\\\\?["\']/i',
                '/\\\\?["\'"]?browser_native_hd_url\\\\?["\'"]?\s*:\s*\\\\?["\'"]?(.*?)\\\\?["\']/i',
            ],
            'SD' => [
                '/\\\\?["\'"]?playable_url\\\\?["\'"]?\s*:\s*\\\\?["\'"]?(.*?)\\\\?["\']/i',
                '/\\\\?["\'"]?browser_native_sd_url\\\\?["\'"]?\s*:\s*\\\\?["\'"]?(.*?)\\\\?["\']/i',
                '/\\\\?["\'"]?browser_native_url\\\\?["\'"]?\s*:\s*\\\\?["\'"]?(.*?)\\\\?["\']/i',
            ],
        ];

        foreach ($patterns2024 as $quality => $regexList) {
            foreach ($regexList as $pattern) {
                if (preg_match($pattern, $html, $match)) {
                    $cleanUrl = $this->decodeUrl($match[1]);
                    if ($this->isValidVideoUrl($cleanUrl)) {
                        $results[$quality] = [
                            'quality' => $quality,
                            'url'     => $cleanUrl,
                        ];
                        break; // Sudah dapat untuk kualitas ini
                    }
                }
            }
        }

        // ── 2. Legacy Facebook format (hd_src / sd_src) ─────────
        if (empty($results)) {
            $legacyPatterns = [
                'HD' => [
                    '/\\\\?["\'"]?hd_src\\\\?["\'"]?\s*:\s*\\\\?["\'"]?(.*?)\\\\?["\']/i',
                    '/\\\\?["\'"]?hd_src_no_ratelimit\\\\?["\'"]?\s*:\s*\\\\?["\'"]?(.*?)\\\\?["\']/i',
                ],
                'SD' => [
                    '/\\\\?["\'"]?sd_src\\\\?["\'"]?\s*:\s*\\\\?["\'"]?(.*?)\\\\?["\']/i',
                    '/\\\\?["\'"]?sd_src_no_ratelimit\\\\?["\'"]?\s*:\s*\\\\?["\'"]?(.*?)\\\\?["\']/i',
                ],
            ];

            foreach ($legacyPatterns as $quality => $regexList) {
                foreach ($regexList as $pattern) {
                    if (preg_match($pattern, $html, $match)) {
                        $cleanUrl = $this->decodeUrl($match[1]);
                        if ($this->isValidVideoUrl($cleanUrl)) {
                            $results[$quality] = [
                                'quality' => $quality,
                                'url'     => $cleanUrl,
                            ];
                            break;
                        }
                    }
                }
            }
        }

        // ── 3. Fallback: cari URL .mp4 generik ──────────────────
        if (empty($results)) {
            $results = $this->extractMp4Fallback($html);
        }

        return array_values($results);
    }

    // ─────────────────────────────────────────────────────────────
    //  Instagram Extractor
    // ─────────────────────────────────────────────────────────────

    /**
     * Ekstrak URL video dari HTML source Instagram.
     * Instagram menyimpan video URL di dalam JSON embedded di <script> tags
     * dan juga di meta og:video tags.
     */
    private function extractInstagram(string $html): array
    {
        $results = [];

        // ── 1. Meta OG Tags ─────────────────────────────────────
        // <meta property="og:video" content="https://...">
        // <meta property="og:video:secure_url" content="https://...">
        $ogPatterns = [
            'Video' => [
                '/<meta\s+property=["\']og:video:secure_url["\']\s+content=["\'](.*?)["\']/i',
                '/<meta\s+property=["\']og:video["\']\s+content=["\'](.*?)["\']/i',
                '/<meta\s+content=["\'](.*?)["\']\s+property=["\']og:video:secure_url["\']/i',
                '/<meta\s+content=["\'](.*?)["\']\s+property=["\']og:video["\']/i',
            ],
        ];

        foreach ($ogPatterns as $quality => $regexList) {
            foreach ($regexList as $pattern) {
                if (preg_match($pattern, $html, $match)) {
                    $cleanUrl = $this->decodeUrl($match[1]);
                    if ($this->isValidVideoUrl($cleanUrl)) {
                        $results[$quality] = [
                            'quality' => $quality,
                            'url'     => $cleanUrl,
                        ];
                        break;
                    }
                }
            }
        }

        // ── 2. Instagram JSON embedded (video_url / video_versions) ───
        // Format: "video_url":"https://scontent..."
        $jsonPatterns = [
            'HD' => [
                '/["\']video_url["\']\s*:\s*["\'](https?:[^"\']+)["\']/i',
                '/["\']video_url["\']\s*:\s*\\\\?["\'](https?:[^"\'\\\\]+)\\\\?["\']/i',
            ],
        ];

        foreach ($jsonPatterns as $quality => $regexList) {
            foreach ($regexList as $pattern) {
                if (preg_match($pattern, $html, $match)) {
                    $cleanUrl = $this->decodeUrl($match[1]);
                    if ($this->isValidVideoUrl($cleanUrl)) {
                        $results[$quality] = [
                            'quality' => $quality,
                            'url'     => $cleanUrl,
                        ];
                        break;
                    }
                }
            }
        }

        // ── 3. Instagram video_versions array (multi-quality) ───
        // "video_versions":[{"width":640,"height":1138,"url":"https://...","type":101}]
        if (preg_match_all('/"video_versions"\s*:\s*\[(.*?)\]/s', $html, $versionBlocks)) {
            foreach ($versionBlocks[1] as $block) {
                // Parse individual entries
                if (preg_match_all('/"url"\s*:\s*"(https?:[^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $block, $urlMatches)) {
                    foreach ($urlMatches[1] as $idx => $rawUrl) {
                        $cleanUrl = $this->decodeUrl($rawUrl);
                        if ($this->isValidVideoUrl($cleanUrl)) {
                            $label = $idx === 0 ? 'HD' : 'SD';
                            if (!isset($results[$label])) {
                                $results[$label] = [
                                    'quality' => $label,
                                    'url'     => $cleanUrl,
                                ];
                            }
                        }
                    }
                }
            }
        }

        // ── 4. Instagram Reels / Stories specific ────────────────
        // "video_dash_manifest":"..." or contentUrl in JSON-LD
        if (empty($results)) {
            // JSON-LD contentUrl
            if (preg_match('/"contentUrl"\s*:\s*"(https?:[^"]+)"/i', $html, $match)) {
                $cleanUrl = $this->decodeUrl($match[1]);
                if ($this->isValidVideoUrl($cleanUrl)) {
                    $results['Video'] = [
                        'quality' => 'Video',
                        'url'     => $cleanUrl,
                    ];
                }
            }
        }

        // ── 5. Fallback .mp4 ─────────────────────────────────────
        if (empty($results)) {
            // Instagram CDN URLs biasanya mengandung scontent
            $igCdnPattern = '/https?:\/\/(?:scontent|video)[^"\'\s<>\\\\]*\.mp4[^"\'\s<>]*/i';
            if (preg_match_all($igCdnPattern, $html, $matches)) {
                $uniqueUrls = array_unique($matches[0]);
                $uniqueUrls = array_slice($uniqueUrls, 0, 3);
                foreach ($uniqueUrls as $index => $rawUrl) {
                    $clean = $this->decodeUrl($rawUrl);
                    if ($this->isValidVideoUrl($clean)) {
                        $label = $index === 0 ? 'Video' : 'Source ' . ($index + 1);
                        $results['ig_' . $index] = [
                            'quality' => $label,
                            'url'     => $clean,
                        ];
                    }
                }
            }
        }

        // ── 6. Final fallback: generic .mp4 ─────────────────────
        if (empty($results)) {
            $results = $this->extractMp4Fallback($html);
        }

        return array_values($results);
    }

    // ─────────────────────────────────────────────────────────────
    //  TikTok Extractor
    // ─────────────────────────────────────────────────────────────

    /**
     * Ekstrak URL video dari HTML source TikTok.
     * TikTok menyimpan video URL di dalam __UNIVERSAL_DATA_FOR_REHYDRATION__
     * dan juga di SIGI_STATE / __NEXT_DATA__ (format lama).
     */
    private function extractTikTok(string $html): array
    {
        $results = [];

        // ── 1. Meta OG Tags ─────────────────────────────────────
        $ogPatterns = [
            '/<meta\s+property=["\']og:video:secure_url["\']\s+content=["\'](.*?)["\']/i',
            '/<meta\s+property=["\']og:video["\']\s+content=["\'](.*?)["\']/i',
            '/<meta\s+content=["\'](.*?)["\']\s+property=["\']og:video:secure_url["\']/i',
            '/<meta\s+content=["\'](.*?)["\']\s+property=["\']og:video["\']/i',
        ];

        foreach ($ogPatterns as $pattern) {
            if (preg_match($pattern, $html, $match)) {
                $cleanUrl = $this->decodeUrl($match[1]);
                if ($this->isValidVideoUrl($cleanUrl)) {
                    $results['Video'] = [
                        'quality' => 'Video',
                        'url'     => $cleanUrl,
                    ];
                    break;
                }
            }
        }

        // ── 2. UNIVERSAL_DATA / playAddr / downloadAddr ─────────
        $jsonKeyPatterns = [
            'HD (No Watermark)' => [
                '/["\']downloadAddr["\']\s*:\s*["\'](https?:[^"\'\\\\]+)["\']/i',
                '/["\']download_addr["\']\s*:\s*\\\\?["\'](https?:[^"\'\\\\]+)\\\\?["\']/i',
            ],
            'Video' => [
                '/["\']playAddr["\']\s*:\s*["\'](https?:[^"\'\\\\]+)["\']/i',
                '/["\']play_addr["\']\s*:\s*\\\\?["\'](https?:[^"\'\\\\]+)\\\\?["\']/i',
            ],
        ];

        foreach ($jsonKeyPatterns as $quality => $regexList) {
            foreach ($regexList as $pattern) {
                if (preg_match($pattern, $html, $match)) {
                    $cleanUrl = $this->decodeUrl($match[1]);
                    if ($this->isValidVideoUrl($cleanUrl)) {
                        if (!isset($results[$quality])) {
                            $results[$quality] = [
                                'quality' => $quality,
                                'url'     => $cleanUrl,
                            ];
                        }
                        break;
                    }
                }
            }
        }

        // ── 3. TikTok video URL from bitrateInfo ────────────────
        if (preg_match_all('/"bitrateInfo"\s*:\s*\[(.*?)\]/s', $html, $bitrateBlocks)) {
            foreach ($bitrateBlocks[1] as $block) {
                if (preg_match_all('/"PlayAddr"\s*:\s*\{[^}]*"UrlList"\s*:\s*\["(https?:[^"]+)"/s', $block, $urlMatches)) {
                    foreach ($urlMatches[1] as $rawUrl) {
                        $cleanUrl = $this->decodeUrl($rawUrl);
                        if ($this->isValidVideoUrl($cleanUrl) && !isset($results['Video'])) {
                            $results['Video'] = [
                                'quality' => 'Video',
                                'url'     => $cleanUrl,
                            ];
                        }
                    }
                }
            }
        }

        // ── 4. TikTok CDN patterns ──────────────────────────────
        if (empty($results)) {
            // TikTok CDN URLs: v16-webapp.tiktok.com, v19-webapp.tiktok.com, etc.
            $tikTokCdn = '/https?:\/\/[^\s"\'<>]*(?:tiktokcdn|tiktok)[^\s"\'<>]*\.mp4[^\s"\'<>]*/i';
            if (preg_match_all($tikTokCdn, $html, $matches)) {
                $uniqueUrls = array_unique($matches[0]);
                $uniqueUrls = array_slice($uniqueUrls, 0, 3);
                foreach ($uniqueUrls as $index => $rawUrl) {
                    $clean = $this->decodeUrl($rawUrl);
                    if ($this->isValidVideoUrl($clean)) {
                        $label = $index === 0 ? 'Video' : 'Source ' . ($index + 1);
                        $results['tt_' . $index] = [
                            'quality' => $label,
                            'url'     => $clean,
                        ];
                    }
                }
            }
        }

        // ── 5. Final fallback ────────────────────────────────────
        if (empty($results)) {
            $results = $this->extractMp4Fallback($html);
        }

        return array_values($results);
    }

    // ─────────────────────────────────────────────────────────────
    //  Generic Extractor (Fallback)
    // ─────────────────────────────────────────────────────────────

    /**
     * Ekstrak generik: coba semua platform-specific patterns,
     * lalu fallback ke .mp4 search.
     */
    private function extractGeneric(string $html): array
    {
        // Coba semua extractors secara berurutan
        $results = $this->extractFacebook($html);
        if (!empty($results)) return $results;

        $results = $this->extractInstagram($html);
        if (!empty($results)) return $results;

        $results = $this->extractTikTok($html);
        if (!empty($results)) return $results;

        return $this->extractMp4Fallback($html);
    }

    // ─────────────────────────────────────────────────────────────
    //  Shared: .mp4 Fallback
    // ─────────────────────────────────────────────────────────────

    /**
     * Cari URL .mp4 generik sebagai last resort.
     */
    private function extractMp4Fallback(string $html): array
    {
        $results = [];
        $genericPattern = '/https?:\/\/[^\s"\'<>\\\\]+\.mp4[^\s"\'<>]*/i';

        if (preg_match_all($genericPattern, $html, $matches)) {
            $uniqueUrls = array_unique($matches[0]);
            $uniqueUrls = array_slice($uniqueUrls, 0, 5);

            foreach ($uniqueUrls as $index => $rawUrl) {
                $clean = $this->decodeUrl($rawUrl);
                if ($this->isValidVideoUrl($clean)) {
                    $results['source_' . ($index + 1)] = [
                        'quality' => 'Source ' . ($index + 1),
                        'url'     => $clean,
                    ];
                }
            }
        }

        return array_values($results);
    }

    // ─────────────────────────────────────────────────────────────
    //  URL Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Decode unicode escape sequences, HTML entities, dan backslash dari URL.
     */
    public function decodeUrl(string $url): string
    {
        // Decode \uXXXX sequences (JSON-style unicode)
        $decoded = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($m) {
            return mb_convert_encoding(pack('H*', $m[1]), 'UTF-8', 'UCS-2BE');
        }, $url);

        // Decode HTML entities (&amp; → &)
        $decoded = html_entity_decode($decoded ?? $url, ENT_QUOTES, 'UTF-8');

        // Bersihkan backslash escape (\" → ", \/ → /)
        $decoded = str_replace(['\"', '\\/'], ['"', '/'], $decoded);

        // Trim trailing junk (tanda kutip, spasi, dsb)
        $decoded = rtrim($decoded, '"\'\\');

        return $decoded;
    }

    /**
     * Normalize URL untuk deduplication (hapus tracking params yang tidak penting).
     */
    private function normalizeUrl(string $url): string
    {
        $parsed = parse_url($url);
        $base   = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '') . ($parsed['path'] ?? '');
        return $base;
    }

    /**
     * Validasi URL adalah video yang bisa diunduh.
     */
    private function isValidVideoUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Harus berupa URL http/https yang cukup panjang
        if (strlen($url) < 20) {
            return false;
        }

        // Harus HTTPS atau HTTP
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'])) {
            return false;
        }

        // Tidak boleh URL thumbnail/gambar
        $path = strtolower(parse_url($url, PHP_URL_PATH) ?? '');
        $imageExts = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg', '.ico', '.bmp'];
        foreach ($imageExts as $ext) {
            if (str_contains($path, $ext)) {
                return false;
            }
        }

        return true;
    }

    // ─────────────────────────────────────────────────────────────
    //  Utility: HTML Size Validation
    // ─────────────────────────────────────────────────────────────

    /**
     * Validasi ukuran HTML source code.
     * Menghindari payload terlalu kecil (bukan source code) atau terlalu besar (DoS).
     *
     * @return array{valid: bool, message: string}
     */
    public function validateHtmlSize(string $html): array
    {
        $size = strlen($html);

        // Minimum: source code asli biasanya > 500 byte
        if ($size < 500) {
            return [
                'valid'   => false,
                'message' => 'Source code terlalu pendek. Pastikan Anda menyalin seluruh halaman (Ctrl+A).',
            ];
        }

        // Maximum: 10 MB — lebih dari ini kemungkinan abuse
        $maxSize = 10 * 1024 * 1024;
        if ($size > $maxSize) {
            return [
                'valid'   => false,
                'message' => 'Source code terlalu besar (maks 10 MB). Pastikan Anda hanya menyalin source code halaman.',
            ];
        }

        return ['valid' => true, 'message' => ''];
    }
}
