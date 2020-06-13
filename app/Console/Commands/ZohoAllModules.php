<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class ZohoAllModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:all-modules';

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
        $this->getAllModules();
    }

    public function getAllModules(){
        $rest=ZCRMRestClient::getInstance();//to get the rest client
        $modules=$rest->getAllModules()->getData();//to get the the modules in form of ZCRMModule instances array
        foreach ($modules as $module){
            echo 'getModuleName='.$module->getModuleName()."\n";;//to get the name of the module
            echo 'getSingularLabel='.$module->getSingularLabel()."\n";;//to get the singular label of the module
            echo 'getPluralLabel='.$module->getPluralLabel()."\n";;//to get the plural label of the module
            echo 'getBusinessCardFieldLimit='.$module->getBusinessCardFieldLimit()."\n";;//to get the business card field limit of the module
            echo 'getAPIName='.$module->getAPIName()."\n";;//to get the api name of the module
            echo 'isCreatable='.$module->isCreatable()."\n";;//to check wther the module is creatable
            echo 'isConvertable='.$module->isConvertable()."\n";;//to check wther the module is Convertable
            echo 'isEditable='.$module->isEditable()."\n";;//to check wther the module is editable
            echo 'isDeletable='.$module->isDeletable()."\n";;//to check wther the module is deletable
            echo 'getWebLink='.$module->getWebLink()."\n";;//to get the weblink
            $user= $module->getModifiedBy();//to get the user who modified the module in form of ZCRMUser instance
            if($user!=null){
                $mUid = $user->getId();//to get the user id
                $nuid = $user->getName();//to get the user name
                'getModifiedBy='.$mUid." ".$nuid."\n";
            }
            echo "getModifiedTime=".$module->getModifiedTime()."\n";;//to get the modified time of the module in iso 8601 format
            echo "isViewable=".$module->isViewable()."\n";;//to check whether the module is viewable
            echo "isApiSupported=".$module->isApiSupported()."\n";;//to check whether the module is api supported
            echo "isCustomModule=".$module->isCustomModule()."\n";;//to check whether it is a custom module
            echo "isScoringSupported=".$module->isScoringSupported()."\n";;//to check whether the scoring is supported
            echo "getId=".$module->getId()."\n";;//to get the module id
            $BusinessCardFields= $module->getBusinessCardFields();//to get the business card fields of the module
            foreach($BusinessCardFields as $BusinessCardField){
                echo $BusinessCardField;
            }
            $profiles= $module->getAllProfiles();//to get the profiles of the module in form of ZCRMProfile array instances
            echo 'getAllProfiles:'."\n";

            foreach($profiles as $profile){
                echo  " getId=".$profile->getId()."\n";//to get the profile id
                echo  " getName=".$profile->getName()."\n";//to get the profile name
            }
            echo 'isGlobalSearchSupported='.$module->isGlobalSearchSupported()."\n";;//to check whether the module is global search supported
            echo 'getSequenceNumber='.$module->getSequenceNumber()."\n";;//to get the sequence number of the module
            echo "\n\n";
        }
        echo "modules cnt = " . count($modules);
    }
}
