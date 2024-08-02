<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Member;

class RegistMail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;

    /**
     * Create a new message instance.
     * @param Member $member
     * @return void
     */

    public function __construct(Member $member)
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
        return $this->view('emails.regist_email') //メールの内容はviwes/emails/regist_email/blade.phpを参照
                    ->subject('会員登録完了のお知らせ') //メールの件名
                    ->with([
                        'name_sei' => $this->member->name_sei,
                        'name_mei' => $this->member->name_mei,
                        'nickname' => $this->member->nickname,
                        'email' => $this->member->email,
                        'gender' => $this->member->gender, //ビューに渡すデータをwithメソッドで指定
                    ]);
    }
}
