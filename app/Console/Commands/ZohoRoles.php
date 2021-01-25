<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class ZohoRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:roles';

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
        $this->getAllRoles();
    }


    public function getAllRoles(){
        $orgIns = ZCRMRestClient::getInstance()->getOrganizationInstance(); // to get the organization instance
        $response=$orgIns->getAllRoles();//to get the roles of the organization
        $roles=$response->getData();//to get the roles in form of array of ZCRMRole instances
        foreach ($roles as $role){
            echo $role->getName() ." \n";//to get the role name
            echo $role->getId() ." \n";//to get the role id
            $reportingrole= $role->getReportingTo();//to get the role id and name to whom user of this role will report to
            if($reportingrole!=null){
                echo $reportingrole->getId() ." \n";
                echo $reportingrole->getName() ." \n";
            }
            echo $role->getDisplayLabel()." \n";//to get the display label of the role
            echo $role->isAdminRole()." \n";//to check whether it is the administrator role
            echo "\n\n";
        }
    }
}
