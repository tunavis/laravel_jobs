<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Job;
use Carbon\Carbon;


class EndDateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job-check-status';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End date status';

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
     * @return int
     */
    public function handle()
    {
        $current_date = Carbon::now()->format('Y-m-d');
        $job = Job::whereDate('end_date', '<', $current_date)->update([
            'status' => 'inactive',
        ]);
        $this->info('status update Successfully.');
    }
}
