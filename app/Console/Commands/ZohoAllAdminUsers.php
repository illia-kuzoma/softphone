<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class ZohoAllAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:all-admin-users';

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
        $this->getAllAdminUsers();
    }

    public function getAllAdminUsers(){
        $orgIns = $rest=ZCRMRestClient::getInstance()->getOrganizationInstance(); // to get the organization instance
        /* For VERSION <=2.0.6 $response=$orgIns->getAllAdminUsers();//to get all the admins*/
        $param_map=array("page"=>"1","per_page"=>"200"); // key-value pair containing all the parameters - optional
        $header_map = array("if-modified-since"=>"2019-11-10T15:26:49+05:30"); // key-value pair containing all the headers - optional
        $response = $orgIns->getAllAdminUsers($param_map,$header_map); // to get all the administrators
        $userInstances=$response->getData();//to get the array of users in form of ZCRMUser instances
        foreach ($userInstances as $userInstance){
            echo $userInstance->getId()."\n";//to get the user id
            echo $userInstance->getCountry()."\n";//to get the country of the user
            $roleInstance=$userInstance->getRole();//to get the role of the user in form of ZCRMRole instance
            echo $roleInstance->getId()."\n";//to get the role id
            echo $roleInstance->getName()."\n";//to get the role name
            $customizeInstance=$userInstance->getCustomizeInfo();//to get the customization information of the user in for of the ZCRMUserCustomizeInfo form
            if($customizeInstance!=null)
            {
                echo $customizeInstance->getNotesDesc()."\n";//to get the note description
                echo $customizeInstance->getUnpinRecentItem()."\n";//to get the unpinned recent items
                echo $customizeInstance->isToShowRightPanel()."\n";//to check whether the right panel is shown
                echo $customizeInstance->isBcView()."\n";//to check whether the business card view is enabled
                echo $customizeInstance->isToShowHome()."\n";//to check whether the home is shown
                echo $customizeInstance->isToShowDetailView()."\n";//to check whether the detail view is shows
            }
            echo $userInstance->getCity()."\n";//to get the city of the user
            echo $userInstance->getSignature()."\n";//to get the signature of the user
            echo $userInstance->getNameFormat()."\n";// to get the name format of the user
            echo $userInstance->getLanguage()."\n";//to get the language of the user
            echo $userInstance->getLocale()."\n";//to get the locale of the user
            echo $userInstance->isPersonalAccount()."\n";//to check whther this is a personal account
            echo $userInstance->getDefaultTabGroup()."\n";//to get the default tab group
            echo $userInstance->getAlias()."\n";//to get the alias of the user
            echo $userInstance->getStreet()."\n";//to get the street name of the user
            $themeInstance=$userInstance->getTheme();//to get the theme of the user in form of the ZCRMUserTheme
            if($themeInstance!=null)
            {
                echo $themeInstance->getNormalTabFontColor()."\n";//to get the normal tab font color
                echo $themeInstance->getNormalTabBackground()."\n";//to get the normal tab background
                echo $themeInstance->getSelectedTabFontColor()."\n";//to get the selected tab font color
                echo $themeInstance->getSelectedTabBackground()."\n";//to get the selected tab background
            }
            echo $userInstance->getState()."\n";//to get the state of the user
            echo $userInstance->getCountryLocale()."\n";//to get the country locale of the user
            echo $userInstance->getFax()."\n";//to get the fax number of the user
            echo $userInstance->getFirstName()."\n";//to get the first name of the user
            echo $userInstance->getEmail()."\n";//to get the email id of the user
            echo $userInstance->getZip()."\n";//to get the zip code of the user
            echo $userInstance->getDecimalSeparator()."\n";//to get the decimal separator
            echo $userInstance->getWebsite()."\n";//to get the website of the user
            echo $userInstance->getTimeFormat()."\n";//to get the time format of the user
            $profile= $userInstance->getProfile();//to get the user's profile in form of ZCRMProfile
            echo $profile->getId()."\n";//to get the profile id
            echo $profile->getName()."\n";//to get the name of the profile
            echo $userInstance->getMobile()."\n";//to get the mobile number of the user
            echo $userInstance->getLastName()."\n";//to get the last name of the user
            echo $userInstance->getTimeZone()."\n";//to get the time zone of the user
            echo $userInstance->getZuid()."\n";//to get the zoho user id of the user
            echo $userInstance->isConfirm()."\n";//to check whether it is a confirmed user
            echo $userInstance->getFullName()."\n";//to get the full name of the user
            echo $userInstance->getPhone()."\n";//to get the phone number of the user
            echo $userInstance->getDob()."\n";//to get the date of birth of the user
            echo $userInstance->getDateFormat()."\n";//to get the date format
            echo $userInstance->getStatus()."\n";//to get the status of the user
            echo "___________\n"."\n";
        }
    }
}
