<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:calls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update missed calls tables';

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
        $unattendedCalls = new \App\Models\ReportUnattended();
        $unattendedCalls->loadFromRemoteServer();
    }
}
