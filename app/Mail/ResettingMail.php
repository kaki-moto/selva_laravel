<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\EmailChangeVerification;

class ResettingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $resetUrl;

    /**
     * Create a new message instance.
     *
     * @param Member $member
     * @return void
     */

    public function __construct($member, $resetUrl)
    {
        $this->member = $member;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->view('emails.resetting_email') //メールの内容はviwes/emails/regist_email.blade.phpを参照
                    ->subject('パスワード再設定');
    }
}
?>