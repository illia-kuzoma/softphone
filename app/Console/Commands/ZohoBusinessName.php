<?php

namespace App\Console\Commands;

use App\Models\ReportUnattendedCall;
use Illuminate\Console\Command;

/**
 * Work with business data.
 *
 * Class ZohoBusinessName
 * @package App\Console\Commands
 */
class ZohoBusinessName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoho:business';

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
     * Gets data for business name and link to business and write it to DB.
     *
     * @return mixed
     */
    public function handle()
    {
        $o = new ReportUnattendedCall();
        foreach(ReportUnattendedCall::all() as $item)
        {
            $o->getBusinessData($item);
        }
        $o->updateBusinessName();
    }
}
