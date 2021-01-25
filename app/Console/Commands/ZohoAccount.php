<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use zcrmsdk\crm\crud\ZCRMModule;

class ZohoAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:account {id}';

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
        $apiResponse=ZCRMModule::getInstance('Accounts')->getRecord($this->argument('id')); // 410405000001519001 - Lead Id
        $record=$apiResponse->getData();
        echo $record->getEntityId();
        echo $record->getModuleApiName();
        echo $record->getLookupLabel();
        echo $record->getCreatedBy()->getId();
        echo $record->getModifiedBy()->getId();
        echo $record->getOwner()->getId();
        echo $record->getCreatedTime();
        echo $record->getModifiedTime();
        $map=$record->getData();
        print_r($map);
    }
}
