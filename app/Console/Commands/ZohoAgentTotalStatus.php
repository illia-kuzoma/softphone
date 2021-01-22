<?php

namespace App\Console\Commands;

use App\Models\ReportAgentTotalStatus;
use Illuminate\Console\Command;

class ZohoAgentTotalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:total-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update total agent statuses';

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
        (new ReportAgentTotalStatus())->fillTable();
    }
}
