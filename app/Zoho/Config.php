<?php
namespace App\Zoho;

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class Config
{
    public const zoho_data_relative_path = '/storage/zoho/';
    public const token_persistence_path = "token_storage";
    public const logs_persistence_path = "logs";
    public $token_file_name = 'zcrm_oauthtokens.txt';

    private $a_access_data = [
        'prod' => [
            // softphone account TODO for future if it needs special store for these data.
            //'client_id'=>'1000.YZO05BI18M18TAUKJGUA38BKMVNYKH',
            //'client_secret'=>'e17bc239cf031167f2a20cfc707a518383c04a5cb0',

            // Alex Kam account
            'client_id'=>'1000.8NB0SF64WYI2SU5PMFN41J1M5T96ER',
            'client_secret'=>'affedf3a971a39224dcc7c4796fe5fba6c926f84ed',
        ],
        'dev' => [
            'client_id'=>'1000.SP95RNDM8ATPVS67H15R5HMLNK5TMH',
            'client_secret'=>'15edb4b3e0912d41d05725898070ebc0840dafc600',
        ],
    ];

    private function changeFilesMod($dir, $mode = 0777)
    {
      foreach(scandir($dir) as $file)
      {
          if(!in_array($file, ['.','..']))
          {
              $full_name = $dir . $file;
              //chmod($full_name, $mode);
              exec('chmod 0777 '.$full_name) ;
          }
      }
    }

    protected function getPathToFileLogs($sub_path = '')
    {
        $dir = base_path() . self::zoho_data_relative_path . $sub_path . '/' ;
        if(!is_dir($dir))
        {
            Log::put(sprintf("Mkdir %s", $dir));
            mkdir($dir,0777, true);
        }
        #$this->changeFilesMod($dir, 0777);
        return $dir;
    }

    public function createIfNotExists(string $file_name, string $dir)
    {
        $file = $dir . DIRECTORY_SEPARATOR . $file_name;
        if(!file_exists($file))
        {
            file_put_contents($file, '');
            $this->changeFilesMod($dir);
        }
    }

    protected function getTokenPath($client_id = null)
    {
        $dir = $this->getPathToFileLogs(self::token_persistence_path . ($client_id ? DIRECTORY_SEPARATOR . $client_id : '') /*. $_SERVER['HTTP_HOST']*/);
        $this->createIfNotExists($this->token_file_name, $dir);
        return $dir;
    }

    protected function getZohoLogPath()
    {
        $dir = $this->getPathToFileLogs(self::logs_persistence_path);
        $this->changeFilesMod($dir);
        return $dir;
    }

    public function __construct($configuration = [])
    {
        $client_data = $this->a_access_data['prod'];
        if(gethostname() == "andrey-comp")
            $client_data = $this->a_access_data['dev'];

        $configuration = array_merge([
          "client_id" => $client_data['client_id'],
          "client_secret" => $client_data['client_secret'],
          #"redirect_uri"=>self::redirect_uri,
          #"currentUserEmail"=>self::userEmail,
          "sandbox" => false, ///<<<<<<<<<<  false for prod mode.
          #'apiBaseUrl'=>'www.zohoapis.eu',
          "token_persistence_path" => $this->getTokenPath($client_data['client_id']),
          'applicationLogFilePath' => $this->getZohoLogPath()/* . "ZCRMClientLibrary.log"*/, #optional, absolute path of log file
        ], $configuration);
        ZCRMRestClient::initialize($configuration);
    }
}
