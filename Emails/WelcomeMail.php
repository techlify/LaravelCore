<?php

namespace Modules\LaravelCore\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $modules;
    public $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $modules, $password)
    {
        $this->user = $user;
        $this->modules = $modules;
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
            ->subject('Welcome to ' . env('APP_NAME'))
            ->view(config('laravelcore.email_templates.welcome'))
            ->with('user', $this->user, $this->modules, $this->password);
    }
}
