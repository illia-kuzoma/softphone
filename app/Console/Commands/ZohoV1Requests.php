<?php

namespace App\Console\Commands;

use App\Zoho\AuthByPassword;
use Illuminate\Console\Command;

class ZohoV1Requests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:v1';

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
        $zo = new AuthByPassword();
        $res = $zo->sequentialUnattendedCallsCount();
        var_dump($res);
    }
}
