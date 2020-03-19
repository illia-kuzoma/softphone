<?php

namespace App\Console\Commands;

use App\Zoho\Auth;
use App\Zoho\Config;
use Illuminate\Console\Command;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\exception\ZCRMException;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

/**
 * Class ZohoRequest for testing 2nd version SDK.
 * @package App\Console\Commands
 */
class ZohoV2Request extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:v2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data from zoho servers.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        new Config([
            "redirect_uri"=>Auth::redirect_uri,
            "currentUserEmail"=>Auth::userEmail
        ]);
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
    }
    public function getRecord()
    {
        $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance("Contacts"); // To get module instance
        $response = $moduleIns->getRecord("1602133000115995014"); // To get module records
        $record = $response->getData(); // To get response data
        try {

            echo "\n\n";
            echo $record->getEntityId(); // To get record id
            echo $record->getModuleApiName(); // To get module api name
            echo $record->getLookupLabel(); // To get lookup object name
            $createdBy = $record->getCreatedBy();
            echo $createdBy->getId(); // To get user_id who created the record
            echo $createdBy->getName(); // To get user name who created the record
            $modifiedBy = $record->getModifiedBy();
            echo $modifiedBy->getId(); // To get user_id who modified the record
            echo $modifiedBy->getName(); // To get user name who modified the record
            $owner = $record->getOwner();
            echo $owner->getId(); // To get record owner_id
            echo $owner->getName(); // To get record owner name
            echo $record->getCreatedTime(); // To get record created time
            echo $record->getModifiedTime(); // To get record modified time
            echo $record->getLastActivityTime(); // To get last activity time(latest modify/view time)
            echo $record->getFieldValue("FieldApiName"); // To get particular field value
            $map = $record->getData(); // To get record data as map
            foreach ($map as $key => $value) {
                if ($value instanceof ZCRMRecord) // If value is ZCRMRecord object
                {
                    echo $value->getEntityId(); // to get the record id
                    echo $value->getModuleApiName(); // to get the api name of the module
                    echo $value->getLookupLabel(); // to get the lookup label of the record
                } else // If value is not ZCRMRecord object
                {
                    echo $key . ":" . $value;
                }
            }
            /**
             * Fields which start with "$" are considered to be property fields *
             */
            echo $record->getProperty('$fieldName'); // To get a particular property value
            $properties = $record->getAllProperties(); // To get record properties as map
            foreach ($properties as $key => $value) {
                if (is_array($value)) // If value is an array
                {
                    echo "KEY::" . $key . "=";
                    foreach ($value as $key1 => $value1) {
                        if (is_array($value1)) {
                            foreach ($value1 as $key2 => $value2) {
                                echo $key2 . ":" . $value2;
                            }
                        } else {
                            echo $key1 . ":" . $value1;
                        }
                    }
                } else {
                    echo $key . ":" . $value;
                }
            }
            $layouts = $record->getLayout(); // To get record layout
            echo $layouts->getId(); // To get layout_id
            echo $layouts->getName(); // To get layout name

            $taxlists = $record->getTaxList(); // To get the tax list
            foreach ($taxlists as $taxlist) {
                echo $taxlist->getTaxName(); // To get tax name
                echo $taxlist->getPercentage(); // To get tax percentage
                echo $taxlist->getValue(); // To get tax value
            }
            $lineItems = $record->getLineItems(); // To get line_items as map
            foreach ($lineItems as $lineItem) {
                echo $lineItem->getId(); // To get line_item id
                echo $lineItem->getListPrice(); // To get line_item list price
                echo $lineItem->getQuantity(); // To get line_item quantity
                echo $lineItem->getDescription(); // To get line_item description
                echo $lineItem->getTotal(); // To get line_item total amount
                echo $lineItem->getDiscount(); // To get line_item discount
                echo $lineItem->getDiscountPercentage(); // To get line_item discount percentage
                echo $lineItem->getTotalAfterDiscount(); // To get line_item amount after discount
                echo $lineItem->getTaxAmount(); // To get line_item tax amount
                echo $lineItem->getNetTotal(); // To get line_item net total amount
                echo $lineItem->getDeleteFlag(); // To get line_item delete flag
                echo $lineItem->getProduct()->getEntityId(); // To get line_item product's entity id
                echo $lineItem->getProduct()->getLookupLabel(); // To get line_item product's lookup label
                $linTaxs = $lineItem->getLineTax(); // To get line_item's line_tax as array
                foreach ($linTaxs as $lineTax) {
                    echo $lineTax->getTaxName(); // To get line_tax name
                    echo $lineTax->getPercentage(); // To get line_tax percentage
                    echo $lineTax->getValue(); // To get line_tax value
                }
            }
            $pricedetails = $record->getPriceDetails(); // To get the price_details array
            foreach ($pricedetails as $pricedetail) {
                echo "\n\n";
                echo $pricedetail->getId(); // To get the record's price_id
                echo $pricedetail->getToRange(); // To get the price_detail record's to_range
                echo $pricedetail->getFromRange(); // To get price_detail record's from_range
                echo $pricedetail->getDiscount(); // To get price_detail record's discount
                echo "\n\n";
            }
            $participants = $record->getParticipants(); // To get Event record's participants
            foreach ($participants as $participant) {
                echo $participant->getName(); // To get the record's participant name
                echo $participant->getEmail(); // To get the record's participant email
                echo $participant->getId(); // To get the record's participant id
                echo $participant->getType(); // To get the record's participant type
                echo $participant->isInvited(); // To check if the record's participant(s) are invited or not
                echo $participant->getStatus(); // To get the record's participants' status
            }
            /* End Event */
        } catch (ZCRMException $ex) {
            echo $ex->getMessage(); // To get ZCRMException error message
            echo $ex->getExceptionCode(); // To get ZCRMException error code
            echo $ex->getFile(); // To get the file name that throws the Exception
        }
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
    public function getAllProfiles(){
        $orgIns = $rest=ZCRMRestClient::getInstance()->getOrganizationInstance(); // to get the organization instance
        $response=$orgIns->getAllProfiles();//to get the profiles
        $profiles=$response->getData();//to get the profiles in form of array of ZCRMProfile
        foreach ($profiles as $profile){
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
            echo "___________\n"."\n";
        }
    }
    public function asd(){
        $zcrmModuleIns = ZCRMModule::getInstance("Contacts");
        $bulkAPIResponse=$zcrmModuleIns->getRecords();
        $recordsArray = $bulkAPIResponse->getData(); // $r
        print_r($recordsArray); echo "\n\n";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        #$this->getAllModules();
        #$this->getRecord();
        #$this->getAllAdminUsers();
        #$this->getAllProfiles();
        #$this->asd();
        #$this->getDepartments();
        #$this->getActivitiesRecord();
        #$this->getUser();

        #$this->getProfile();
        $this->getAllRoles();
        echo 1;
    }


    public function getDepartments()
    {
        $zcrmModuleIns = ZCRMModule::getInstance("departments");
        $bulkAPIResponse=$zcrmModuleIns->getRecords();
        $recordsArray = $bulkAPIResponse->getData(); // $r
        print_r($recordsArray); echo "\n\n";
    }
    public function getActivitiesRecord()
    {
        $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance("Calls"); // To get module instance
        $response = $moduleIns->getRecord("102325000117996176"); // To get module records
        $record = $response->getData(); // To get response data
        try {

            echo "\n\n";
            echo $record->getEntityId(); // To get record id
            echo $record->getModuleApiName(); // To get module api name
            echo $record->getLookupLabel(); // To get lookup object name
            $createdBy = $record->getCreatedBy();
            echo $createdBy->getId(); // To get user_id who created the record
            echo $createdBy->getName(); // To get user name who created the record
            $modifiedBy = $record->getModifiedBy();
            echo $modifiedBy->getId(); // To get user_id who modified the record
            echo $modifiedBy->getName(); // To get user name who modified the record
            $owner = $record->getOwner();
            echo $owner->getId(); // To get record owner_id
            echo $owner->getName(); // To get record owner name
            echo $record->getCreatedTime(); // To get record created time
            echo $record->getModifiedTime(); // To get record modified time
            echo $record->getLastActivityTime(); // To get last activity time(latest modify/view time)
            echo $record->getFieldValue("FieldApiName"); // To get particular field value
            $map = $record->getData(); // To get record data as map
            foreach ($map as $key => $value) {
                if ($value instanceof ZCRMRecord) // If value is ZCRMRecord object
                {
                    echo $value->getEntityId(); // to get the record id
                    echo $value->getModuleApiName(); // to get the api name of the module
                    echo $value->getLookupLabel(); // to get the lookup label of the record
                } else // If value is not ZCRMRecord object
                {
                    echo $key . ":" . $value;
                }
            }
            /**
             * Fields which start with "$" are considered to be property fields *
             */
            echo $record->getProperty('$fieldName'); // To get a particular property value
            $properties = $record->getAllProperties(); // To get record properties as map
            foreach ($properties as $key => $value) {
                if (is_array($value)) // If value is an array
                {
                    echo "KEY::" . $key . "=";
                    foreach ($value as $key1 => $value1) {
                        if (is_array($value1)) {
                            foreach ($value1 as $key2 => $value2) {
                                echo $key2 . ":" . $value2;
                            }
                        } else {
                            echo $key1 . ":" . $value1;
                        }
                    }
                } else {
                    echo $key . ":" . $value;
                }
            }
            $layouts = $record->getLayout(); // To get record layout
            echo $layouts->getId(); // To get layout_id
            echo $layouts->getName(); // To get layout name

            $taxlists = $record->getTaxList(); // To get the tax list
            foreach ($taxlists as $taxlist) {
                echo $taxlist->getTaxName(); // To get tax name
                echo $taxlist->getPercentage(); // To get tax percentage
                echo $taxlist->getValue(); // To get tax value
            }
            $lineItems = $record->getLineItems(); // To get line_items as map
            foreach ($lineItems as $lineItem) {
                echo $lineItem->getId(); // To get line_item id
                echo $lineItem->getListPrice(); // To get line_item list price
                echo $lineItem->getQuantity(); // To get line_item quantity
                echo $lineItem->getDescription(); // To get line_item description
                echo $lineItem->getTotal(); // To get line_item total amount
                echo $lineItem->getDiscount(); // To get line_item discount
                echo $lineItem->getDiscountPercentage(); // To get line_item discount percentage
                echo $lineItem->getTotalAfterDiscount(); // To get line_item amount after discount
                echo $lineItem->getTaxAmount(); // To get line_item tax amount
                echo $lineItem->getNetTotal(); // To get line_item net total amount
                echo $lineItem->getDeleteFlag(); // To get line_item delete flag
                echo $lineItem->getProduct()->getEntityId(); // To get line_item product's entity id
                echo $lineItem->getProduct()->getLookupLabel(); // To get line_item product's lookup label
                $linTaxs = $lineItem->getLineTax(); // To get line_item's line_tax as array
                foreach ($linTaxs as $lineTax) {
                    echo $lineTax->getTaxName(); // To get line_tax name
                    echo $lineTax->getPercentage(); // To get line_tax percentage
                    echo $lineTax->getValue(); // To get line_tax value
                }
            }
            $pricedetails = $record->getPriceDetails(); // To get the price_details array
            foreach ($pricedetails as $pricedetail) {
                echo "\n\n";
                echo $pricedetail->getId(); // To get the record's price_id
                echo $pricedetail->getToRange(); // To get the price_detail record's to_range
                echo $pricedetail->getFromRange(); // To get price_detail record's from_range
                echo $pricedetail->getDiscount(); // To get price_detail record's discount
                echo "\n\n";
            }
            $participants = $record->getParticipants(); // To get Event record's participants
            foreach ($participants as $participant) {
                echo $participant->getName(); // To get the record's participant name
                echo $participant->getEmail(); // To get the record's participant email
                echo $participant->getId(); // To get the record's participant id
                echo $participant->getType(); // To get the record's participant type
                echo $participant->isInvited(); // To check if the record's participant(s) are invited or not
                echo $participant->getStatus(); // To get the record's participants' status
            }
            /* End Event */
        } catch (ZCRMException $ex) {
            echo $ex->getMessage(); // To get ZCRMException error message
            echo $ex->getExceptionCode(); // To get ZCRMException error code
            echo $ex->getFile(); // To get the file name that throws the Exception
        }
    }

    public function getUser(){
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

    public function getProfile(){
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
