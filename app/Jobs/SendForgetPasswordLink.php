<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Mail;
use App\Mail\ForgetPasswordLinkMail;

class SendForgetPasswordLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $code;
    protected $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$code,$url=null)
    {
      $this->data = $data;
      $this->code = $code;
      $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $email = new ForgetPasswordLinkMail($this->data,$this->code,$this->url);
      Mail::to($this->data->email)->send($email);
    }
}
