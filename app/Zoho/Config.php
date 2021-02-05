<?php
namespace App\Zoho;

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class Config
{
    public const zoho_data_relative_path = '/storage/zoho/';
    public const token_persistence_path = "token_storage";
    public const logs_persistence_path = "logs";
    public $token_file_name = 'zcrm_oauthtokens.txt';

    /**
     * Consists access to applications created on ZOHO. Different accounts for prod and dev for
     * easy development. If Amount of developers grow need to create additional applications on ZOHO.
     * Because getting data from zoho related with tokens and they are refreshing, so every one will have different token.
     *
     *
     * @var array
     */
    private $a_access_data = [
        'prod' => [
            // softphone account TODO for future if it needs special store for these data. \App\Zoho\Auth::userEmail
            //'client_id'=>'1000.YZO05BI18M18TAUKJGUA38BKMVNYKH',
            //'client_secret'=>'e17bc239cf031167f2a20cfc707a518383c04a5cb0',

            // Alex Kam account
            'client_id'=>'1000.7Z028HI2GHKELNFROUPXMW668738QI',
            'client_secret'=>'7f56880e52d0e4581d86a9c751580ada4664c10bf2',

        ],
        'dev' => [
            // softphone account TODO for future if it needs special store for these data.
            /*'client_id'=>'1000.SP95RNDM8ATPVS67H15R5HMLNK5TMH',
            'client_secret'=>'15edb4b3e0912d41d05725898070ebc0840dafc600',*/

            // Alex Kam account
            'client_id'=>'1000.D06W0XN2OPX71JMM797RQB0CAS19UT',
            'client_secret'=>'3bcc5d654308ccf2ceaa4b2d87f47cdd4ad8a5bd14',
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

    /**
     * To add here your hostname to work at local machine.
     *
     * @return bool
     */
    private function _modeDevIs()
    {
        $is = false;
        if(in_array(gethostname(), ["andrey-comp"]))
            $is = true;

        if(in_array(gethostname(), ["WIN-1COBLQQLJQU"]))
            $is = true;

        return $is;
    }

    private function _modeProdIs()
    {
        $is = false;
        if(in_array(gethostname(), ["community-dev"]))
            $is = true;

        return $is;
    }

    public function __construct($configuration = [])
    {
        $client_data = [];
        if($this->_modeDevIs())
            $client_data = $this->a_access_data['dev'];
        if($this->_modeProdIs())
            $client_data = $this->a_access_data['prod'];

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
