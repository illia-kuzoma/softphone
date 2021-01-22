<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class ZohoProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:profile';

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
        $orgIns = ZCRMRestClient::getInstance()->getOrganizationInstance(); // to get the organization instance
        $response=$orgIns->getProfile("102325000110065693");//to get the profile
        $profile=$response->getData();//to get the profile in form of the ZCRMProfile instance
        echo $profile->getId();//to get the id of the profile
        echo $profile->getName();//to get the name of the profile
        echo $profile->isDefaultProfile();//to check whether the profile is default
        echo $profile->getCreatedTime();//to get the created time of the profile
        echo $profile->getModifiedTime();//to get the modified time of the profile
        $userInstance=$profile->getModifiedBy();//to get the user who modified the profile
        if($userInstance!=NULL){
            echo $userInstance->getId();//to get the user id
            echo $userInstance->getName();//to get the user name
        }
        echo $profile->getDescription();//to get the profile description
        $userInstance=$profile->getCreatedBy();//to get the user who created the profile
        if($userInstance!=NULL){
            echo $userInstance->getId();//to get the profile id
            echo $userInstance->getName();//to get the profile name
        }
        echo $profile->getCategory();//to get the category of the profile
        $permissions=$profile->getPermissionList();//to get the permissions of the profile
        foreach ($permissions as $permission){
            echo $permission->getDisplayLabel();//to get the display labnel of the permission
            echo $permission->getModule();//to get the module name of the permission
            echo $permission->getId();//to get the id of the permission
            echo $permission->getName();//to get the name of the permission
            echo $permission->isEnabled();//to check whether the permission is enabled
        }
        $sections=$profile->getSectionsList();//to get the section list of the profile
        foreach($sections as $section){
            echo $section->getName();//to get the name of the section
            $profilecategories=$section->getCategories();//to get the categories of the profile sections
            foreach ($profilecategories as $profilecategory){
                echo $profilecategory->getName();//to get the name of the category
                echo $profilecategory->getModule();//to get the module name to which the category belongs
                echo $profilecategory->getDisplayLabel();//to get the display label of the category
                $permissionIds= $profilecategory->getPermissionIds();//to get the permission ids of the profile section categories
                foreach ($permissionIds as $permissionId){
                    echo $permissionId;//to get the permission id
                }
            }
        }
    }
}
