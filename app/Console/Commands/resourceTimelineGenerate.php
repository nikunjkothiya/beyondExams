<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class resourceTimelineGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resourceTimeline:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate latest resource timeline for active users';

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
        $controller = app()->make('App\Http\Controllers\ResourceController');
        app()->call([$controller, 'generate_resource_timeline']);

        $this->info('resourceTimeline:generate Cummand Run successfully!');
    }
}
