<?php

namespace App\Jobs;

use App\Mail\BulkEmailSender;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BulkEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $details;

    public function __construct($details)

    {
        $this->details = $details;
        // $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        //Log::info('sending to' . $this->details['email']);
        // $users = User::all();
        // foreach ($users as $user) {

        // }

        try {
            if (in_array('All', $this->details['users'])) {
                $users = User::where("role", 'user')->get();
            } else {
                $users = User::whereIn('email', $this->details['users'])->get();
            }
            foreach ($users as $user) {
                Mail::to($user->email)->send(new BulkEmailSender($this->details));
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
