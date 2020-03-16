<?php

namespace App\Console\Commands;

use App\Zoho\AuthByToken;
use Illuminate\Console\Command;

class ZohoRegenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:regen-token {token}';

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
     * Command regenerate grand and refresh tokens. It is actual if scopes was changed.
     * For regenerating tokens you should to enter scopes on ZOHO site in console section
     * https://accounts.zoho.com/developerconsole then chose "Self client" for Client Name in menu.
     * Set new scopes and got grant token, then set that token to this command.
     * For example:
     *  php artisan zoho:regen-token 1000.3a4b523d7d4c150d4516f193d72e2cc1.25d6b295a69ab887944b9dbfb1985545
     *
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new AuthByToken($this->argument('token')))->generateAccessToken();
    }
}
