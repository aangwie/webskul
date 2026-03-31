<?php

namespace App\Observers;

use App\Models\Activity;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ActivityObserver
{
    /**
     * Handle the Activity "created" event.
     */
    public function created(Activity $activity): void
    {
        $this->generateSitemap();
    }

    /**
     * Handle the Activity "updated" event.
     */
    public function updated(Activity $activity): void
    {
        $this->generateSitemap();
    }

    /**
     * Handle the Activity "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        $this->generateSitemap();
    }

    /**
     * Handle the Activity "restored" event.
     */
    public function restored(Activity $activity): void
    {
        $this->generateSitemap();
    }

    /**
     * Handle the Activity "force deleted" event.
     */
    public function forceDeleted(Activity $activity): void
    {
        $this->generateSitemap();
    }

    /**
     * Trigger sitemap generation
     */
    protected function generateSitemap()
    {
        try {
            Artisan::call('sitemap:generate');
        } catch (\Exception $e) {
            Log::error('Failed to generate sitemap: ' . $e->getMessage());
        }
    }
}
