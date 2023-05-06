<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ThankyouMailv2 extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $inquiry;
    public $offer;
    public $weeks;
    /**
     * Create a new message instance.
     *
     * @return void
     */
     
    public function __construct($email, $inquiry, $offer, $weeks)
    {
        $this->email = $email;
        $this->inquiry = $inquiry;
        $this->offer = $offer;
        $this->weeks = $weeks;
    }
    


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
         return $this->subject('Inquiry Submitted')->view('thankyouv2')
                    ->with([
                        'email' => $this->email,
                        'inquiry' => $this->inquiry,
                        'offer' => $this->offer,
                        'weeks' => $this->weeks
                    ]);
    }
}