<?php

namespace App\Console\Commands;

use App\Models\ReportAgentStatusesGroup;
use Illuminate\Console\Command;

class UpdateAgentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:agent-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command updates agent statuses for inner table';

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
        $o_asg = new ReportAgentStatusesGroup();
        $o_asg->fillTable();
    }
}
