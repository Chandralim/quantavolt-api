<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Http\Request;

use File;


use \App\Helpers\MyLib;
use \App\Helpers\EzLog;

class migrateAfter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mgAfter {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make All Data To More Reasonable';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    //  private $limitSMS=160;
    //  private $senderNumber;
    //  private $originalText;
    //  private $recreateText;
    //  private $ID;
    private $date_time_spacer;
    private $date_from_data;
    private $limit = 0;



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
      $checknull=""==null;
      $this->info("==========");
      $this->info($checknull);
      $this->info("==========");

      // //   $this->info("same date \n ");
      // $argumentValue = $this->argument('date');
      // // Your command logic here
      // $this->info('Argument value: '.$argumentValue);
      return;
      // $migrationsPath = database_path('migrations');

      // $excludedMigrations = [
      //     // List the migration file names you want to exclude here
      //     '2022_01_01_000000_example_migration.php',
      //     '2022_02_01_000000_another_migration.php',
      // ];

      // $files = scandir($migrationsPath);

      // foreach ($files as $file) {
      //     if (in_array($file, $excludedMigrations)) {
      //         continue;
      //     }

      //     $path = $migrationsPath . '/' . $file;
      //     $kernel->call('migrate:up', ['--path' => $path, '--pretend' => true]);
      // }


      // $this->msg = $this->ask('Isi SMS:');
      // $this->info('Pesan yang dikirimkan : '.$this->msg. " \n ");

    }
}
