<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Activity;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create('/activities')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create('/information')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
            ->add(Url::create('/pmb')->setPriority(0.7)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));

        // Add all published activities
        $activities = Activity::where('is_published', true)->get();
        foreach ($activities as $activity) {
            $sitemap->add(
                Url::create("/activities/{$activity->slug}")
                    ->setLastModificationDate($activity->updated_at)
                    ->setPriority(0.9)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');
    }
}
