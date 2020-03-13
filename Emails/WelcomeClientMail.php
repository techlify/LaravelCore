<?php

namespace Modules\LaravelCore\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\LaravelCore\Entities\Client;

class WelcomeClientMail extends Mailable
{
    use Queueable, SerializesModels;
    public $client;
    public $modules;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Client $client, $modules)
    {
        $this->client = $client;
        $this->modules = $modules;
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
            ->view('laravelcore.email_templates.client_welcome')
            ->with('client', $this->client, $this->modules);
    }
}
