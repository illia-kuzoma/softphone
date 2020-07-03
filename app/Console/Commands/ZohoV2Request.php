<?php

namespace App\Console\Commands;

use App\Zoho\Auth;
use App\Zoho\Config;
use App\Zoho\V1\ActiveTimers;
use Illuminate\Console\Command;

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


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new ActiveTimers())->getAll2();
        echo ' ';
    }
}
