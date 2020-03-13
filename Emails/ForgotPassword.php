<?php

namespace Modules\LaravelCore\Emails;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $subject, $password)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(
            config('laravelcore.email_sender.mail_from_address'),
            config('laravelcore.email_sender.mail_from_name'),
        )
            ->subject($this->subject)
            ->view(config('laravelcore.email_templates.forgot_password'))
            ->with('user', $this->user, $this->password);
    }
}
