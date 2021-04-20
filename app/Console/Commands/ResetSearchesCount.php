<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Search;

class ResetSearchesCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily count reset of searches';

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
     $count_reset = Search::where('daily_count','>',0)->update(['daily_count'=>0]);
    }
}
