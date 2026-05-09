<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('download_logs', function (Blueprint $table) {
            $table->id();

            // Relasi ke users — nullable untuk guest
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // URL & platform
            $table->text('original_url');
            $table->string('platform_name', 50);          // youtube, tiktok, facebook

            // Video metadata (dari yt-dlp --dump-json)
            $table->string('video_title', 500)->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->unsignedInteger('duration')->nullable();          // detik
            $table->string('duration_string', 20)->nullable();        // "12:34"
            $table->string('uploader', 255)->nullable();              // channel/akun

            // Download info
            $table->string('format_quality', 50)->nullable();         // "1080p", "HD"
            $table->string('file_extension', 10)->default('mp4');     // mp4, mp3, webm
            $table->unsignedBigInteger('file_size')->nullable();      // bytes
            $table->string('download_method', 30)->default('yt-dlp'); // yt-dlp | source_extraction
            $table->text('download_url')->nullable();                 // direct link

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('platform_name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_logs');
    }
};
