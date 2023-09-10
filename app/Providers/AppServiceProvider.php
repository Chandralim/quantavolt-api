<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      setlocale(LC_ALL, 'id_ID.utf8');
      Carbon::setLocale('id_ID.utf8');

      DB::enableQueryLog();
      DB::connection('pgsql')->enableQueryLog();
      DB::connection('pgsql2')->enableQueryLog();

      DB::listen(function($query)  {
        if (trim($query->sql)[0]!=="s") {

          $date=new \DateTime();
          $ip=\Request::ip();
          $timestamp=$date->format("Y-m-d H:i:s.v");
          $today=date("Y-m-d");
          $filename="/logs/xquery.".$today.".log";
          $content=vsprintf(str_replace(array('?'), array('\'%s\''), $query->sql), $query->bindings)."; // {$timestamp} {$ip}". PHP_EOL;

          \File::append(storage_path($filename),$content);
        }
      });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
