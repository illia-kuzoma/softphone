<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDirs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:projdirs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create project dirs.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $dir_list = [
        [
            'mode'=>0777,
            'dir'=>'public/storage/images'
        ]
    ];
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dir_1 = base_path() . '/';
        foreach($this->dir_list as $dir_obj)
        {
            $dir_mode = $dir_obj['mode'];
            $dir_str = $dir_obj['dir'];
            $dir_arr = explode('/', $dir_str);
            $sub_dir = base_path() . '/';
            foreach($dir_arr as $dir_name)
            {
                $sub_dir .= $dir_name . '/';
                if(is_dir($sub_dir))
                {
                    chmod($sub_dir,$dir_mode);
                }
                else
                {
                    mkdir($sub_dir,$dir_mode, true);
                    chmod($sub_dir,$dir_mode);
                }
                #echo $sub_dir."\n";
            }
        }
    }
}
