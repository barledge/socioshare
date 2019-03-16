<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Carbon\Carbon;
use Artisan;
use DB;

class ResetDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warbler:reset_demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the database for the DEMO';

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

      if(env('DEMO')) {
        $this->line("Resetting...");
        $this->truncateTables();
        $this->seedDatabase();
        $this->scheduleQueue();
        $this->line("Done.");
      }

    }

    private function truncateTables() {
      $this->line("Truncating...");
      DB::statement('SET FOREIGN_KEY_CHECKS=0');
      $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
      foreach($tables as $table) {
          DB::table($table)->truncate();
      }
      DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function seedDatabase() {
      $this->line("Seeding...");
      Artisan::call('db:seed');
    }

    private function scheduleQueue() {
      $this->line("Schedule queue...");
      Artisan::call('warbler:schedule_queue');
    }

}
