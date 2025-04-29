<?php

namespace App\Jobs;

use App\Mail\registrationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class RegisterEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    private $email;
    private $user;
    public function __construct($email, $user)

    {
        $this->email = $email;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new registrationEmail($this->user));
    }
}
