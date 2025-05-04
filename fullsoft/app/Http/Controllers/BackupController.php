<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Spatie\DbDumper\Databases\MySql;


class BackupController extends Controller
{
    public function index()
    {
        // Use the 'backups' disk configured in config/filesystems.php
        $disk = Storage::disk('backups');

        // Ensure the directory exists
        if (!$disk->exists('')) {
            $disk->makeDirectory('');
        }

        $files = collect($disk->allFiles())
            ->filter(fn($file) => Str::endsWith($file, ['.sql', '.zip']))
            ->map(fn($file) => [
                'name'          => basename($file),
                'path'          => $file,
                'size'          => $disk->size($file),
                'modified_at'   => Carbon::createFromTimestamp($disk->lastModified($file)),
            ])
            ->sortByDesc('modified_at');

        return view('backup', [
            'backups' => $files,
            'error' => session('error')
        ]);
    }

    public function create(Request $request)
    {
        try {
            // Get DB connection config
            $dbConfig = config('database.connections.' . config('database.default'));

            $username = $dbConfig['username'];
            $password = $dbConfig['password'];
            $database = $dbConfig['database'];
            $host     = $dbConfig['host'] ?? '127.0.0.1';

            // Set up paths
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup-{$timestamp}.sql";
            $storagePath = storage_path("app/backups/{$filename}");

            // Ensure the backup directory exists
            if (!file_exists(dirname($storagePath))) {
                mkdir(dirname($storagePath), 0775, true);
            }

            // Optional: Add mysqldump binary path if needed
            $mysqldump = 'C:\Program Files\MySQL\MySQL Server 9.1\bin\mysqldump.exe'; // or full path like 'C:\\xampp\\mysql\\bin\\mysqldump.exe'

            // Escape password safely (you can also use `--password='...'` if needed)
            $command = "\"{$mysqldump}\" -h {$host} -u {$username} --password={$password} {$database} > \"{$storagePath}\"";

            Log::info("Running backup command: {$command}");

            // Run it
            $output = shell_exec($command);

            // Check if file was created
            if (!file_exists($storagePath)) {
                Log::error("Backup file was not created. Output: {$output}");
                return redirect()->route('backup')->with('error', 'Backup failed. SQL file not created.');
            }

            return redirect()
                ->route('backup')
                ->with('status', 'Backup completed and saved as ' . $filename);

        } catch (\Exception $e) {
            Log::error("Backup exception: " . $e->getMessage());
            return redirect()->route('backup')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }


    public function download($filename)
    {
        $disk = Storage::disk('backups');

        if (!$disk->exists($filename)) {
            abort(404);
        }

        $fullPath = $disk->path($filename);
        return response()->download($fullPath);
    }
}
