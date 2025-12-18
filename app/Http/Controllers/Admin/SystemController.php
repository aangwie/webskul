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
                // We split commands to handle potential intermediate failures gracefully or conditional steps
                $initCommands = [
                    "git init",
                    "git checkout -b main || git checkout main", // Force branch to be 'main'
                    "git remote remove origin || echo 'No origin to remove'", // Clean up just in case
                    "git remote add origin {$repoUrl}",
                ];

                // Execute Setup Commands
                foreach ($initCommands as $cmd) {
                    exec("cd {$rootPath} && " . $cmd . " 2>&1", $cmdOutput, $cmdReturn);
                    // Allow some commands to "fail" (like remove origin if not exists, or checkout -b if exists)
                    // But we should log them.
                    $output[] = "Exec: " . $cmd;
                    // We don't throw here immediately unless critical, but let's keep it simple:
                    // The critical ones are fetch and reset.
                    $cmdOutput = [];
                }

                // Now critical steps
                $fetchCmd = "git fetch origin";
                $output[] = "Exec: " . $fetchCmd;
                exec("cd {$rootPath} && {$fetchCmd} 2>&1", $fetchOutput, $fetchReturn);

                // Secure log for fetch
                $safeFetchOutput = array_map(function ($line) use ($token) {
                    return str_replace($token, '*****', $line);
                }, $fetchOutput);
                $output = array_merge($output, $safeFetchOutput);

                if ($fetchReturn !== 0) {
                    // Check for 403
                    $errorStr = implode("\n", $safeFetchOutput);
                    if (strpos($errorStr, '403') !== false) {
                        throw new \Exception("Git Auth Error (403). Please check your GITHUB_TOKEN and GITHUB_USERNAME in .env. The token may be invalid or expired.");
                    }
                    throw new \Exception("Git Fetch Failed: " . $errorStr);
                }

                // Reset and Upstream
                $resetCommands = [
                    "git reset --hard origin/main",
                    "git branch --set-upstream-to=origin/main main"
                ];

                foreach ($resetCommands as $cmd) {
                    exec("cd {$rootPath} && " . $cmd . " 2>&1", $cmdOutput, $cmdReturn);
                    $output = array_merge($output, $cmdOutput);
                    if ($cmdReturn !== 0) {
                        throw new \Exception("Git Config Failed ({$cmd}): " . end($cmdOutput));
                    }
                    $cmdOutput = [];
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
                    // Check if it's an Auth error
                    $errorStr = implode("\n", $gitOutput);
                    if (strpos($errorStr, '403') !== false) {
                        $output[] = "CRITICAL: Git Authorization Failed (403). Check your .env GITHUB_TOKEN.";
                    }

                    $output[] = "Git pull failed, trying fetch + reset...";

                    // Setup correct repo URL again for fetch
                    $fetchUrl = "origin";
                    if ($token && $repo) {
                        $fetchUrl = "https://{$token}@github.com/{$repo}.git";
                        if ($username) $fetchUrl = "https://{$username}:{$token}@github.com/{$repo}.git";
                    }

                    exec("cd {$rootPath} && git fetch {$fetchUrl} 2>&1", $fetchOutput);
                    exec("cd {$rootPath} && git reset --hard origin/main 2>&1", $resetOutput);

                    // Masking output again...
                    if ($token) {
                        $safeFetchAndReset = array_map(function ($line) use ($token) {
                            return str_replace($token, '*****', $line);
                        }, array_merge($fetchOutput, $resetOutput));
                        $output = array_merge($output, $safeFetchAndReset);
                    } else {
                        $output = array_merge($output, $fetchOutput, $resetOutput);
                    }
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
