<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    protected $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data,$url=null)
    {
      $this->data=$data;
      $this->url=$url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      // return $this->to( $this->data->email)->view('email/verified')->with([
      //   'email_token'=> $this->data->email_token,
      //   'url'=>$this->url
      // ]);

      return $this->markdown('email.verified')->with(['email_token'=> $this->data->email_token,'url'=>$this->url]);
    }
}
