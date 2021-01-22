<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class ZohoRelatedListRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:related-list-records';

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
        $record = ZCRMRestClient::getInstance()->getRecordInstance("{module_api_name}", "{record_id}"); // To get record instance
        /* For VERSION <=2.0.6 $relatedlistrecords = $record->getRelatedListRecords("Attachments")->getData(); // to get the related list records in form of ZCRMRecord instance*/

        $param_map=array("page"=>"1","per_page"=>"200"); // key-value pair containing all the parameters - optional
        $header_map = array("if-modified-since"=>"2019-10-10T15:26:49+05:30"); // key-value pair containing all the headers - optional
        $relatedlistrecords = $record->getRelatedListRecords("Attachments",$param_map,$header_map)->getData(); // to get the related list records in form of ZCRMRecord instance

        foreach ($relatedlistrecords as $relatedlistrecord) {
            echo $relatedlistrecord->getEntityId(); // to get the entity id
            echo $relatedlistrecord->getFieldValue("File_Name"); // to get the file name
            echo $relatedlistrecord->getModuleApiName(); // to get the api name of the module
        }
        $relatedlistrecords = $record->getRelatedListRecords("Products")->getData(); // to get the related list record inform of ZCRMRecord instance
        foreach ($relatedlistrecords as $relatedlistrecord) {
            echo $relatedlistrecord->getModuleApiName(); // to get the api name of the module
            echo $relatedlistrecord->getFieldValue("Product_Name"); // to get the product name
            echo $relatedlistrecord->getEntityId(); // to get the entity id
            echo $relatedlistrecord->getFieldValue("Product_Code"); // to get the product code
        }
        $relatedlistrecords = $record->getRelatedListRecords("Activities")->getData(); // to get the related list record inform of ZCRMRecord instance
        foreach ($relatedlistrecords as $relatedlistrecord) {
            echo $relatedlistrecord->getModuleApiName(); // to get the api name of the module
            echo $relatedlistrecord->getEntityId(); // to get the entity id
            echo $relatedlistrecord->getFieldValue("Subject"); // to get the subject of the activity
            echo $relatedlistrecord->getFieldValue("Due_Date"); // to get the due date of the activity
            echo $relatedlistrecord->getFieldValue("Billable"); // to get the billable value
            echo $relatedlistrecord->getFieldValue("Activity_Type"); // to get the activity type
        }
        $relatedlistrecords = $record->getRelatedListRecords("Campaigns")->getData(); // to get the related list record inform of ZCRMRecord instance
        foreach ($relatedlistrecords as $relatedlistrecord) {
            echo $relatedlistrecord->getModuleApiName(); // to get the api name of the module
            echo $relatedlistrecord->getEntityId(); // to get the entity id
            echo $relatedlistrecord->getFieldValue("Campaign_Name"); // to get the campaigns name
            echo $relatedlistrecord->getFieldValue("Description"); // to get the campaign's description
            echo $relatedlistrecord->getFieldValue("Member_Status"); // to get the member status
        }
        $relatedlistrecords = $record->getRelatedListRecords("Quotes")->getData(); // to get the related list record inform of ZCRMRecord instance

        foreach ($relatedlistrecords as $relatedlistrecord) {
            echo $relatedlistrecord->getModuleApiName(); // to get the api name of the module
            echo $relatedlistrecord->getEntityId(); // to get the entity id
            echo $relatedlistrecord->getFieldValue("Carrier"); // to get the carrier
            echo $relatedlistrecord->getFieldValue("Quote_Stage"); // to get the quote stage
            echo $relatedlistrecord->getFieldValue("Subject"); // to get the quote subject
            echo $relatedlistrecord->getFieldValue("Quote_Number"); // to get the quote number
            echo $relatedlistrecord->getFieldValue("currency_symbol"); // to get the currency symbol
        }
        $relatedlistrecords = $record->getRelatedListRecords("SalesOrders")->getData(); // to get the related list record inform of ZCRMRecord instance

        foreach ($relatedlistrecords as $relatedlistrecord) {
            echo $relatedlistrecord->getModuleApiName(); // to get the api name of the module
            echo $relatedlistrecord->getEntityId(); // to get the entity id
            echo $relatedlistrecord->getFieldValue("Carrier"); // to get the carrier
            echo $relatedlistrecord->getFieldValue("Status"); // to get the status of the sales order
            echo $relatedlistrecord->getFieldValue("Billing_Street"); // to get the billing street
            echo $relatedlistrecord->getFieldValue("Billing_Code"); // to get the billing code
            echo $relatedlistrecord->getFieldValue("Subject"); // to get the subject
            echo $relatedlistrecord->getFieldValue("Billing_City"); // to get the billing city
            echo $relatedlistrecord->getFieldValue("SO_Number"); // to get the sales order number
            echo $relatedlistrecord->getFieldValue("Billing_State"); // to get the billing state
        }
        $relatedlistrecords = $record->getRelatedListRecords("Cases")->getData(); // to get the related list record inform of ZCRMRecord instance
        foreach ($relatedlistrecords as $relatedlistrecord) {
            echo $relatedlistrecord->getModuleApiName(); // to get the api name of the module
            echo $relatedlistrecord->getEntityId(); // to get the entity id
            echo $relatedlistrecord->getFieldValue("Status"); // to get the status of the case
            echo $relatedlistrecord->getFieldValue("Email"); // to get the email id
            echo $relatedlistrecord->getFieldValue("Case_Origin"); // to get the case origin
            echo $relatedlistrecord->getFieldValue("Case_Number"); // to get the case number
        }
    }
}
