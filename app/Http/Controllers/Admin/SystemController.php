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
        $log = [];
        try {
            $log[] = "Mencoba Artisan: storage:link...";
            Artisan::call('storage:link');
            $log[] = "✓ Berhasil via Artisan.";
            return back()->with('success', 'Symlink storage berhasil dibuat (Artisan)!')->with('storage_log', implode("\n", $log));
        } catch (\Exception $e) {
            $log[] = "✗ Gagal via Artisan: " . $e->getMessage();

            // Fallback for Shared Hosting (Manual Symlink)
            try {
                $target = storage_path('app/public');
                $links = [
                    public_path('storage'), // Default
                ];

                // Detect public_html sibling (Common Shared Hosting)
                // Case: /home/user/laravel_smp (root) and /home/user/public_html
                $basePath = base_path();
                $parentDir = dirname($basePath);
                $publicHtmlPath = $parentDir . DIRECTORY_SEPARATOR . 'public_html';

                if (is_dir($publicHtmlPath)) {
                    $log[] = "Terdeteksi folder public_html di: " . $publicHtmlPath;
                    $links[] = $publicHtmlPath . DIRECTORY_SEPARATOR . 'storage';
                }

                foreach ($links as $link) {
                    $log[] = "Mencoba membuat symlink di: " . $link;
                    if (file_exists($link)) {
                        $log[] = "- File/link sudah ada, mencoba menghapus...";
                        @unlink($link);
                    }

                    if (symlink($target, $link)) {
                        $log[] = "✓ Berhasil membuat symlink di: " . $link;
                    } else {
                        $log[] = "✗ Gagal membuat symlink di: " . $link;
                    }
                }

                return back()->with('success', 'Proses symlink selesai! Cek log di bawah.')->with('storage_log', implode("\n", $log));
            } catch (\Exception $ex) {
                $log[] = "✗ Gagal Total: " . $ex->getMessage();
                return back()->with('error', 'Gagal membuat symlink!')->with('storage_log', implode("\n", $log));
            }
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
                        if ($username)
                            $fetchUrl = "https://{$username}:{$token}@github.com/{$repo}.git";
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

    public function updateTheme(Request $request)
    {
        try {
            $request->validate([
                'theme' => 'required|in:default,maroon,emerald',
            ]);

            \App\Models\Setting::set('system_theme', $request->theme);

            return back()->with('success', 'Tema berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update tema: ' . $e->getMessage());
        }
    }

    public function composerDumpAutoload()
    {
        try {
            set_time_limit(300);
            $output = [];
            $rootPath = base_path();
            $composerPhar = $rootPath . '/composer.phar';

            $output[] = "--- Fixing Package Autoload ---";

            // Step 1: Try global composer first
            $command = "cd {$rootPath} && composer dump-autoload -o 2>&1";
            exec($command, $composerOutput, $returnVar);

            if ($returnVar === 0) {
                $output[] = "✓ Global composer found and executed.";
                $output = array_merge($output, $composerOutput);
            } else {
                $output[] = "✗ Global composer not available.";

                // Step 2: Check if composer.phar exists, if not download it
                if (!file_exists($composerPhar)) {
                    $output[] = "\n--- Downloading composer.phar ---";

                    // Download composer installer
                    $installerUrl = 'https://getcomposer.org/composer-stable.phar';
                    $downloaded = @file_get_contents($installerUrl);

                    if ($downloaded === false) {
                        // Try with curl if file_get_contents fails
                        $output[] = "Trying with cURL...";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $installerUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
                        $downloaded = curl_exec($ch);
                        $curlError = curl_error($ch);
                        curl_close($ch);

                        if ($downloaded === false || empty($downloaded)) {
                            $output[] = "✗ Failed to download composer.phar: " . $curlError;
                            $output[] = "\n--- Fallback: Running Package Discovery Only ---";

                            // Fallback to just running package:discover
                            Artisan::call('package:discover');
                            $output[] = Artisan::output();

                            Artisan::call('optimize:clear');
                            $output[] = Artisan::output();

                            $outputString = implode("\n", $output);
                            return back()->with('warning', 'Composer tidak tersedia. Hanya package discovery yang dijalankan.')->with('composer_log', $outputString);
                        }
                    }

                    // Save composer.phar
                    if (file_put_contents($composerPhar, $downloaded) === false) {
                        throw new \Exception("Failed to save composer.phar to disk.");
                    }

                    $output[] = "✓ composer.phar downloaded successfully!";
                }

                // Step 3: Run composer.phar with COMPOSER_HOME set
                $output[] = "\n--- Running composer.phar dump-autoload ---";
                $composerHome = $rootPath . '/storage/composer';

                // Create composer home directory if not exists
                if (!is_dir($composerHome)) {
                    mkdir($composerHome, 0755, true);
                }

                // Set environment variables and run composer
                $pharCmd = "cd {$rootPath} && COMPOSER_HOME={$composerHome} HOME={$composerHome} php composer.phar dump-autoload -o 2>&1";
                exec($pharCmd, $pharOutput, $pharReturn);
                $output = array_merge($output, $pharOutput);

                if ($pharReturn !== 0) {
                    $output[] = "✗ composer.phar failed. Trying artisan package:discover...";
                }
            }

            // Step 4: Always run package:discover
            $output[] = "\n--- Running Package Discovery ---";
            Artisan::call('package:discover');
            $output[] = Artisan::output();

            // Step 5: Clear all caches
            $output[] = "\n--- Clearing Caches ---";
            Artisan::call('optimize:clear');
            $output[] = Artisan::output();

            $output[] = "\n✓ Proses selesai! Silakan coba akses halaman yang error lagi.";

            $outputString = implode("\n", $output);
            return back()->with('success', 'Package autoload berhasil diperbaiki!')->with('composer_log', $outputString);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
