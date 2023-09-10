<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgetPasswordLinkMail2 extends Mailable
{
    use Queueable, SerializesModels;

    protected $code;
    protected $data;
    protected $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data,$code,$url=null)
    {
      $this->data=$data;
      $this->code=$code;
      $this->url=$url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->markdown('email.forget_password_link2')->with(['email_token'=> $this->data->email_token,'code'=> $this->code,'url'=> $this->url]);
    }
}
