<?php
namespace App\Zoho;

use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class Calls
{
  public function __construct()
  {
    new Config([
      "redirect_uri"=>Auth::redirect_uri,
      "currentUserEmail"=>Auth::userEmail
    ]);

    #self::setGrantToken();
  }

  public function getRecords($page = 1, $per_page = 20, $date_modified = "2019-11-15T15:26:49+05:30")
  {
    $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance("Calls"); // To get module instance
    /* For VERSION <=2.0.6 $response = $moduleIns->getRecords(null, null, null, 1, 100, null); // to get the records(parameter - custom_view_id,field_api_name,sort_order,customHeaders is optional and can be given null if not required), customheader is a keyvalue pair for eg("if-modified-since"=>"2008-09-15T15:53:00")*/
    $param_map=array('page'=>$page,'per_page'=>$per_page); // key-value pair containing all the parameters - optional
    $header_map = array("if-modified-since"=>$date_modified); // key-value pair containing all the headers - optional
    $response = $moduleIns->getRecords($param_map,$header_map); // to get the records($param_map - parameter map,$header_map - header map
    $records = $response->getData(); // To get response data

    $output_arr = [];
    try {
      foreach ($records as $record) {
        echo "\n\n";
        $output_arr['id'] = $record->getEntityId();
        $owner = $record->getOwner();
        $output_arr['owner'] = [
          'id' => $owner->getId(),
          'name' => $owner->getName(),
        ];
        $output_arr['created_time'] = $record->getCreatedTime();
        $output_arr['id'] = $record->getEntityId();
        $output_arr['id'] = $record->getEntityId();
        $output_arr['id'] = $record->getEntityId();
        echo "getEntityId=".$record->getEntityId()."\n"; // To get record id
        echo "getModuleApiName=".$record->getModuleApiName()."\n"; // To get module api name
        echo "getLookupLabel=".$record->getLookupLabel()."\n"; // To get lookup object name
        $createdBy = $record->getCreatedBy();
        echo "createdBy getId=".$createdBy->getId()."\n"; // To get user_id who created the record
        echo "createdBy getName=".$createdBy->getName()."\n"; // To get user name who created the record
        $modifiedBy = $record->getModifiedBy();
        echo "modifiedBy getId=".$modifiedBy->getId()."\n"; // To get user_id who modified the record
        echo "modifiedBy getName=".$modifiedBy->getName()."\n"; // To get user name who modified the record
        $owner = $record->getOwner();
        echo "owner getId=".$owner->getId()."\n"; // To get record owner_id
        echo "owner getName=".$owner->getName()."\n"; // To get record owner name
        echo "getCreatedTime=".$record->getCreatedTime()."\n"; // To get record created time
        echo "getModifiedTime=".$record->getModifiedTime()."\n"; // To get record modified time
        echo "getLastActivityTime=".$record->getLastActivityTime()."\n"; // To get last activity time(latest modify/view time)
        echo "getFieldValue=".$record->getFieldValue("FieldApiName"); // To get particular field value
        $map = $record->getData(); // To get record data as map
        foreach ($map as $key => $value) {
          if ($value instanceof ZCRMRecord) // If value is ZCRMRecord object
          {
            echo "map getEntityId=".$value->getEntityId()."\n"; // to get the record idf
            echo "map getModuleApiName=".$value->getModuleApiName()."\n"; // to get the api name of the module
            echo "map getLookupLabel=".$value->getLookupLabel()."\n"; // to get the lookup label of the record
          } else // If value is not ZCRMRecord object
          {
            echo "map getFile=".$key . ":" ;print_r($value);echo "\n";


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
            echo "getFile="."KEY::" . $key . "="."\n";
            foreach ($value as $key1 => $value1) {
              if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) {
                  echo "  getFile=".$key2 . ":" . $value2."\n";
                }
              } else {
                echo "  getFile=".$key1 . ":" . $value1."\n";
              }
            }
          } else {
            echo "getFile=".$key . ":" . $value."\n";
          }
        }
        $layouts = $record->getLayout(); // To get record layout
        if($layouts){

          echo "layouts getId=".$layouts->getId()."\n"; // To get layout_id
          echo "layouts getName=".$layouts->getName()."\n"; // To get layout name
        }

        $taxlists = $record->getTaxList(); // To get the tax list
        foreach ($taxlists as $taxlist) {
          echo "taxlists getTaxName=".$taxlist->getTaxName()."\n"; // To get tax name
          echo "taxlists getPercentage=".$taxlist->getPercentage()."\n"; // To get tax percentage
          echo "taxlists getValue=".$taxlist->getValue()."\n"; // To get tax value
        }
        $lineItems = $record->getLineItems(); // To get line_items as map
        foreach ($lineItems as $lineItem) {
          echo "lineItems getId=".$lineItem->getId()."\n"; // To get line_item id
          echo "lineItems getListPrice=".$lineItem->getListPrice()."\n"; // To get line_item list price
          echo "lineItems getQuantity=".$lineItem->getQuantity()."\n"; // To get line_item quantity
          echo "lineItems getDescription=".$lineItem->getDescription()."\n"; // To get line_item description
          echo "lineItems getTotal=".$lineItem->getTotal()."\n"; // To get line_item total amount
          echo "lineItems getDiscount=".$lineItem->getDiscount()."\n"; // To get line_item discount
          echo "lineItems getDiscountPercentage=".$lineItem->getDiscountPercentage()."\n"; // To get line_item discount percentage
          echo "lineItems getTotalAfterDiscount=".$lineItem->getTotalAfterDiscount()."\n"; // To get line_item amount after discount
          echo "lineItems getTaxAmount=".$lineItem->getTaxAmount()."\n"; // To get line_item tax amount
          echo "lineItems getNetTotal=".$lineItem->getNetTotal()."\n"; // To get line_item net total amount
          echo "lineItems getDeleteFlag=".$lineItem->getDeleteFlag()."\n"; // To get line_item delete flag
          echo "lineItems getProduct getEntityId=".$lineItem->getProduct()->getEntityId()."\n"; // To get line_item product's entity id
          echo "lineItems getProduct getLookupLabel=".$lineItem->getProduct()->getLookupLabel()."\n"; // To get line_item product's lookup label
          $linTaxs = $lineItem->getLineTax(); // To get line_item's line_tax as array
          foreach ($linTaxs as $lineTax) {
            echo "linTaxs getTaxName=".$lineTax->getTaxName()."\n"; // To get line_tax name
            echo "linTaxs getPercentage=".$lineTax->getPercentage()."\n"; // To get line_tax percentage
            echo "linTaxs getValue=".$lineTax->getValue()."\n"; // To get line_tax value
          }
        }
        $pricedetails = $record->getPriceDetails(); // To get the price_details array
        foreach ($pricedetails as $pricedetail) {
          echo "\n\n";
          echo "pricedetails getId=".$pricedetail->getId()."\n"; // To get the record's price_id
          echo "pricedetails getToRange=".$pricedetail->getToRange()."\n"; // To get the price_detail record's to_range
          echo "pricedetails getFromRange=".$pricedetail->getFromRange()."\n"; // To get price_detail record's from_range
          echo "pricedetails getDiscount=".$pricedetail->getDiscount()."\n"; // To get price_detail record's discount
          echo "\n\n";
        }
        $participants = $record->getParticipants(); // To get Event record's participants
        foreach ($participants as $participant) {
          echo "participants getName=".$participant->getName()."\n"; // To get the record's participant name
          echo "participants getEmail=".$participant->getEmail()."\n"; // To get the record's participant email
          echo "participants getId=".$participant->getId()."\n"; // To get the record's participant id
          echo "participants getType=".$participant->getType()."\n"; // To get the record's participant type
          echo "participants isInvited=".$participant->isInvited()."\n"; // To check if the record's participant(s) are invited or not
          echo "participants getStatus=".$participant->getStatus()."\n"; // To get the record's participants' status
        }
        /* End Event  */

      }
    } catch (ZCRMException $ex) {
      echo "getFile=".$ex->getMessage()."\n"; // To get ZCRMException error message
      echo "getFile=".$ex->getExceptionCode()."\n"; // To get ZCRMException error code
      echo "getFile=".$ex->getFile()."\n"; // To get the file name that throws the Exception
    }
  }
}
