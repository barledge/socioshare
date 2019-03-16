<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Carbon\Carbon;
use Artisan;

class CheckCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warbler:check_cron';

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

        //check last cron run
        $setting = Setting::updateOrCreate(
            ['key' => 'cron_last_run'],
		        ['value' => Carbon::now()]
        );

        if($setting) {
            $last_run = new Carbon( $setting->value );
            $cron_running = $last_run->gt(Carbon::now()->subMinutes(5));
            if(!$cron_running) {
                Artisan::call('warbler:schedule_queue');
            }
        }




    }
}
