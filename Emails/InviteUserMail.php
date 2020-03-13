<?php

namespace Modules\LaravelCore\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Modules\LaravelCore\Entities\Module;

class InviteUserMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $roles;
    public $module;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $roles, Module $module)
    {
        $this->user = $user;
        $this->roles = $roles;
        $this->module = $module;
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
        ->subject($this->user['subject'])
        ->view(config('laravelcore.email_templates.module_invite_user'))
        ->with('user', $this->user);
    }
}
