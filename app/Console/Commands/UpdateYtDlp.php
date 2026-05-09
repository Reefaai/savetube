<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class UpdateYtDlp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yt-dlp:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download or update the yt-dlp binary to the latest version.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for yt-dlp release...');

        $os = PHP_OS_FAMILY;
        $binaryName = $os === 'Windows' ? 'yt-dlp.exe' : 'yt-dlp';
        $url = "https://github.com/yt-dlp/yt-dlp/releases/latest/download/{$binaryName}";

        $binPath = storage_path('app/bin');
        if (!File::exists($binPath)) {
            File::makeDirectory($binPath, 0755, true);
        }

        $fullPath = $binPath . DIRECTORY_SEPARATOR . $binaryName;

        $this->info("Downloading {$binaryName} from GitHub...");

        try {
            $response = Http::withOptions(['verify' => false])->timeout(300)->sink($fullPath)->get($url);

            if ($response->successful()) {
                if ($os !== 'Windows') {
                    chmod($fullPath, 0755);
                }
                $this->info('Successfully downloaded and updated yt-dlp!');
            } else {
                $this->error('Failed to download yt-dlp. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('Exception occurred: ' . $e->getMessage());
        }
    }
}
