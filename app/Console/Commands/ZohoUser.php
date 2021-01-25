<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class ZohoUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:user';

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

        $responseIns=$orgIns->getUser("102325000000760261");//to get the user
        $userInstance=$responseIns->getData();//to get the user data in form ZCRMUser instance

        echo $userInstance->getId();//to get the user id
        echo $userInstance->getCountry();//to get the country of the user
        $roleInstance=$userInstance->getRole();//to get the role of the user in form of ZCRMRole instance
        echo $roleInstance->getId();//to get the role id
        echo $roleInstance->getName();//to get the role name
        $customizeInstance=$userInstance->getCustomizeInfo();//to get the customization information of the user in for of the ZCRMUserCustomizeInfo form
        if($customizeInstance!=null)
        {
            echo $customizeInstance->getNotesDesc();//to get the note description
            echo $customizeInstance->getUnpinRecentItem();//to get the unpinned recent items
            echo $customizeInstance->isToShowRightPanel();//to check whether the right panel is shown
            echo $customizeInstance->isBcView();//to check whether the business card view is enabled
            echo $customizeInstance->isToShowHome();//to check whether the home is shown
            echo $customizeInstance->isToShowDetailView();//to check whether the detail view is shows
        }
        echo $userInstance->getCity();//to get the city of the user
        echo $userInstance->getSignature();//to get the signature of the user
        echo $userInstance->getNameFormat();// to get the name format of the user
        echo $userInstance->getLanguage();//to get the language of the user
        echo $userInstance->getLocale();//to get the locale of the user
        echo $userInstance->isPersonalAccount();//to check whther this is a personal account
        echo $userInstance->getDefaultTabGroup();//to get the default tab group
        echo $userInstance->getAlias();//to get the alias of the user
        echo $userInstance->getStreet();//to get the street name of the user
        $themeInstance=$userInstance->getTheme();//to get the theme of the user in form of the ZCRMUserTheme
        if($themeInstance!=null)
        {
            echo $themeInstance->getNormalTabFontColor();//to get the normal tab font color
            echo $themeInstance->getNormalTabBackground();//to get the normal tab background
            echo $themeInstance->getSelectedTabFontColor();//to get the selected tab font color
            echo $themeInstance->getSelectedTabBackground();//to get the selected tab background
        }
        echo $userInstance->getState();//to get the state of the user
        echo $userInstance->getCountryLocale();//to get the country locale of the user
        echo $userInstance->getFax();//to get the fax number of the user
        echo $userInstance->getFirstName();//to get the first name of the user
        echo $userInstance->getEmail();//to get the email id of the user
        echo $userInstance->getZip();//to get the zip code of the user
        echo $userInstance->getDecimalSeparator();//to get the decimal separator
        echo $userInstance->getWebsite();//to get the website of the user
        echo $userInstance->getTimeFormat();//to get the time format of the user
        $profile= $userInstance->getProfile();//to get the user's profile in form of ZCRMProfile
        echo $profile->getId();//to get the profile id
        echo $profile->getName();//to get the name of the profile
        echo $userInstance->getMobile();//to get the mobile number of the user
        echo $userInstance->getLastName();//to get the last name of the user
        echo $userInstance->getTimeZone();//to get the time zone of the user
        echo $userInstance->getZuid();//to get the zoho user id of the user
        echo $userInstance->isConfirm();//to check whether it is a confirmed user
        echo $userInstance->getFullName();//to get the full name of the user
        echo $userInstance->getPhone();//to get the phone number of the user
        echo $userInstance->getDob();//to get the date of birth of the user
        echo $userInstance->getDateFormat();//to get the date format
        echo $userInstance->getStatus();//to get the status of the user
        echo "HTTP Status Code:".$responseIns->getHttpStatusCode(); //To get http response code
        echo "Status:".$responseIns->getStatus(); //To get response status
        echo "Message:".$responseIns->getMessage(); //To get response message
        echo "Code:".$responseIns->getCode(); //To get status code
        echo "Details:".json_encode($responseIns->getDetails());
    }
}
