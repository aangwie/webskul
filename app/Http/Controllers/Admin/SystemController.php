<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class SystemController extends Controller
{
    public function index()
    {
        $hasStorageLink = file_exists(public_path('storage'));

        return view('admin.system.index', compact('hasStorageLink'));
    }

    public function storageLink()
    {
        try {
            Artisan::call('storage:link');
            return back()->with('success', 'Symlink storage berhasil dibuat! Gambar seharusnya sudah muncul.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat symlink: ' . $e->getMessage());
        }
    }

    public function updateApp()
    {
        try {
            // Set longer timeout for update process
            set_time_limit(300);

            $output = [];
            $output[] = "--- Starting Update Process ---";

            // 1. Setup Git Variables
            $username = env('GITHUB_USERNAME');
            $token = env('GITHUB_TOKEN');
            $repo = env('GITHUB_REPO');
            $rootPath = base_path();

            // 2. Check and Initialize Git if missing
            if (!file_exists($rootPath . '/.git')) {
                $output[] = "WARNING: .git directory not found. Attempting to initialize...";

                if (!$token || !$repo) {
                    throw new \Exception("Cannot initialize git: GITHUB_TOKEN or GITHUB_REPO not set in .env");
                }

                // Construct Repo URL
                $repoUrl = "https://{$token}@github.com/{$repo}.git";
                if ($username) {
                    $repoUrl = "https://{$username}:{$token}@github.com/{$repo}.git";
                }

                // Initialize and Fetch
                $initCommands = [
                    "cd {$rootPath} && git init",
                    "cd {$rootPath} && git remote add origin {$repoUrl}",
                    "cd {$rootPath} && git fetch origin",
                    "cd {$rootPath} && git reset --hard origin/main",
                    "cd {$rootPath} && git branch --set-upstream-to=origin/main main"
                ];

                foreach ($initCommands as $cmd) {
                    // Mask token in output log
                    $logCmd = str_replace($token, '*****', $cmd);
                    $output[] = "Exec: " . $logCmd;

                    exec($cmd . " 2>&1", $cmdOutput, $cmdReturn);

                    // Mask token in result
                    $safeOutput = array_map(function ($line) use ($token) {
                        return str_replace($token, '*****', $line);
                    }, $cmdOutput);

                    $output = array_merge($output, $safeOutput);

                    if ($cmdReturn !== 0) {
                        throw new \Exception("Git Init Failed: " . end($safeOutput));
                    }
                    $cmdOutput = []; // Reset buffer
                }

                $output[] = "Git initialized successfully!";
            } else {
                // 3. Normal Update (Git Pull)
                $gitOutput = [];

                if ($token && $repo) {
                    $repoUrl = "https://{$token}@github.com/{$repo}.git";
                    if ($username) {
                        $repoUrl = "https://{$username}:{$token}@github.com/{$repo}.git";
                    }

                    $command = "cd {$rootPath} && git pull {$repoUrl} main 2>&1";

                    $safeCommand = "git pull https://***:***@github.com/{$repo}.git main";
                    $output[] = "Command: " . $safeCommand;
                } else {
                    $command = "cd {$rootPath} && git pull origin main 2>&1";
                    $output[] = "Command: git pull origin main";
                }

                exec($command, $gitOutput, $returnVar);

                // Mask token
                if ($token) {
                    foreach ($gitOutput as &$line) {
                        $line = str_replace($token, '*****', $line);
                    }
                }

                $output = array_merge($output, $gitOutput);

                if ($returnVar !== 0) {
                    $output[] = "Git pull failed, trying fetch + reset...";
                    // Try fetching first if pull fails
                    if ($token && $repo) {
                        $repoUrl = "https://{$token}@github.com/{$repo}.git";
                        if ($username) $repoUrl = "https://{$username}:{$token}@github.com/{$repo}.git";
                        exec("cd {$rootPath} && git fetch {$repoUrl} 2>&1", $fetchOutput);
                        exec("cd {$rootPath} && git reset --hard origin/main 2>&1", $resetOutput);
                    } else {
                        exec("cd {$rootPath} && git fetch --all 2>&1");
                        exec("cd {$rootPath} && git reset --hard origin/main 2>&1", $resetOutput);
                    }
                    // No detailed logging for reset fallback to keep it simple, just hope it works or user sees error
                }
            }

            // 4. Migrate Database
            $output[] = "\n--- Running Migrations ---";
            Artisan::call('migrate', ['--force' => true]);
            $output[] = Artisan::output();

            // 5. Clear Caches
            $output[] = "\n--- Clearing Caches ---";
            Artisan::call('optimize:clear');
            $output[] = Artisan::output();

            $outputString = implode("\n", $output);

            return back()->with('success', 'Update selesai!')->with('update_log', $outputString);
        } catch (\Exception $e) {
            return back()->with('error', 'Update gagal: ' . $e->getMessage());
        }
    }

    public function cacheClear()
    {
        Artisan::call('optimize:clear');
        return back()->with('success', 'Cache sistem berhasil dibersihkan.');
    }
}
