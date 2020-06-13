<?php

namespace App\Console\Commands;

use App\Zoho\V1\Team;
use Illuminate\Console\Command;

class ZohoTeam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:team {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        print_r((new Team())->getTeamDataArr($this->argument('id')));
    }
}
