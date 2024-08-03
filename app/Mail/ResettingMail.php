<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Member; // Memberモデルを使用する場合

class ResettingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;

    /**
     * Create a new message instance.
     *
     * @param Member $member
     * @return void
     */

    public function __construct($member)
    {
        $this->member = $member;
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