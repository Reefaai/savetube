<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadLog extends Model
{
    /**
     * Accessors to append to JSON serialization.
     *
     * @var list<string>
     */
    protected $appends = [
        'formatted_file_size',
        'platform_icon',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'original_url',
        'platform_name',
        'video_title',
        'status',
        'thumbnail_url',
        'duration',
        'duration_string',
        'uploader',
        'format_quality',
        'file_extension',
        'file_size',
        'download_method',
        'download_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration'  => 'integer',
            'file_size' => 'integer',
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  Relationships
    // ─────────────────────────────────────────────────────────────

    /**
     * Get the user that owns the download log.
     * Nullable — guest downloads don't have a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─────────────────────────────────────────────────────────────
    //  Query Scopes
    // ─────────────────────────────────────────────────────────────

    /**
     * Filter by platform name.
     */
    public function scopeForPlatform(Builder $query, string $platform): Builder
    {
        return $query->where('platform_name', strtolower($platform));
    }

    /**
     * Search by video title.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('video_title', 'like', '%' . $term . '%');
    }

    // ─────────────────────────────────────────────────────────────
    //  Accessors
    // ─────────────────────────────────────────────────────────────

    /**
     * Format file size dari bytes ke string yang mudah dibaca.
     * Contoh: 152043520 → "145 MB"
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '-';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, $i > 1 ? 1 : 0) . ' ' . $units[$i];
    }

    /**
     * Icon Material Symbols berdasarkan platform.
     */
    public function getPlatformIconAttribute(): string
    {
        return match ($this->platform_name) {
            'youtube'   => 'play_circle',
            'tiktok'    => 'music_note',
            'facebook'  => 'public',
            'instagram' => 'photo_camera',
            default     => 'link',
        };
    }
}
