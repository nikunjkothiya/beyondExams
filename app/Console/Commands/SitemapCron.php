<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SitemapCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate latest sitemap files and index.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Sitemap cron job triggered");
        $controller = app()->make('App\Http\Controllers\UtilController');
        app()->call([$controller, 'generate_latest_sitemap']);

        $this->info('Sitemap:Cron Cummand Run successfully!');
    }
}
