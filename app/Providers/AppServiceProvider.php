<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Helper for Shared Hosting
        // If public_html exists (sibling to base_path), use it as public path
        $sharedPublic = base_path('../public_html');
        if (file_exists($sharedPublic) && is_dir($sharedPublic)) {
            $this->app->bind('path.public', function () use ($sharedPublic) {
                return $sharedPublic;
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['app.locale' => 'id']);
        \Carbon\Carbon::setLocale('id');

        // Default values for global view variables
        $school = null;
        $is_pmb_open = false;
        $themeName = 'default';
        $themeColors = [
            'default' => [
                'primary' => '#1e3a5f',
                'primary_light' => '#2c4f7c',
                'primary_dark' => '#0f2340',
                'accent_gold' => '#d4af37',
                'body_bg' => '#f8f9fa',
                'nav_bg' => 'linear-gradient(135deg, #1e3a5f 0%, #0f2340 100%)',
            ],
            'maroon' => [
                'primary' => '#800000',
                'primary_light' => '#a52a2a',
                'primary_dark' => '#500000',
                'accent_gold' => '#ffd700',
                'body_bg' => '#fff5f5',
                'nav_bg' => 'linear-gradient(135deg, #800000 0%, #500000 100%)',
            ],
            'emerald' => [
                'primary' => '#10b981',
                'primary_light' => '#34d399',
                'primary_dark' => '#047857',
                'accent_gold' => '#fbbf24',
                'body_bg' => '#f0fdf4',
                'nav_bg' => 'linear-gradient(135deg, #10b981 0%, #047857 100%)',
            ],
        ];

        // Attempt to load from database if tables exist
        try {
            // Check for critical tables to avoid migration crashes
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $school = \App\Models\SchoolProfile::first();
                $is_pmb_open = \App\Models\Setting::isPmbOpen();
                $themeName = \App\Models\Setting::get('system_theme', 'default');

                // Global SMTP Configuration Override
                $smtp = \App\Models\Setting::getSmtpSettings();
                if ($smtp['mail_mailer'] && $smtp['mail_mailer'] !== 'log') {
                    config([
                        'mail.default' => $smtp['mail_mailer'],
                        'mail.mailers.smtp.host' => $smtp['mail_host'],
                        'mail.mailers.smtp.port' => $smtp['mail_port'],
                        'mail.mailers.smtp.username' => $smtp['mail_username'],
                        'mail.mailers.smtp.password' => $smtp['mail_password'],
                        'mail.mailers.smtp.encryption' => $smtp['mail_encryption'] === 'null' ? null : $smtp['mail_encryption'],
                        'mail.from.address' => $smtp['mail_from_address'],
                        'mail.from.name' => $smtp['mail_from_name'],
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if database is not ready or settings table missing
        }

        // Share globally with views
        $activeTheme = $themeColors[$themeName] ?? $themeColors['default'];
        \Illuminate\Support\Facades\View::share('school', $school);
        \Illuminate\Support\Facades\View::share('is_pmb_open', $is_pmb_open);
        \Illuminate\Support\Facades\View::share('active_theme', $activeTheme);
        \Illuminate\Support\Facades\View::share('theme_name', $themeName);

        // View Composer for Admin Layout - Complaint Badge
        \Illuminate\Support\Facades\View::composer('admin.layouts.app', function ($view) {
            $unrespondedComplaintsCount = 0;
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('public_complaints')) {
                    $unrespondedComplaintsCount = \App\Models\PublicComplaint::where('status', 'pending')->count();
                }
            } catch (\Exception $e) {
                // Ignore if table doesn't exist yet
            }
            $view->with('unrespondedComplaintsCount', $unrespondedComplaintsCount);
        });
    }
}
