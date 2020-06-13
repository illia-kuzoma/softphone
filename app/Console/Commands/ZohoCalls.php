<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use zcrmsdk\crm\crud\ZCRMModule;

class ZohoCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:calls';

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
        $this->calls();
    }

    public function calls(){
        $zcrmModuleIns = ZCRMModule::getInstance("Calls");
        $bulkAPIResponse=$zcrmModuleIns->getRecords();
        $recordsArray = $bulkAPIResponse->getData(); // $r
        print_r($recordsArray); echo "\n\n";
    }
}
