<?php
namespace zoho;

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

require_once __DIR__ . '/../../vendor/autoload.php';

abstract class Auth
{
  public const redirect_uri = "https://www.wellnessliving.com";
  public $token_file_name = 'default.txt';
  public const userEmail = "support@wellnessliving.com";

  public function __construct($configuration = [])
  {
    #ZCRMRestClient::initialize($configuration);
  }
}
