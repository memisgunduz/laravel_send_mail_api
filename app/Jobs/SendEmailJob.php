<?php

namespace App\Jobs;

use App\Models\MailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use Exception;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected  $mailLogId;
    protected  $subject;
    protected  $email;
    protected  $html;
    protected  $attachment;

      public function __construct($mailLogId, $subject, $email, $html, $attachment = null)
      {
          $this->mailLogId = $mailLogId;
          $this->subject = $subject;
          $this->email = $email;
          $this->html = $html;
          $this->attachment = $attachment;
      }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::send('mails.mail-view', ['html' => $this->html], function ($message) {
            $message->subject($this->subject);
            $message->to($this->email);
            if($this->attachment) $message->attach(public_path($this->attachment));
        });

        MailLog::where('id', $this->mailLogId)->update([
            'status' => 1
        ]);
    }

    public function failed(Exception $exception)
    {
        MailLog::where('id', $this->mailLogId)->update([
            'status' => 9,
            'exception' => $exception->getMessage()
        ]);
    }
}
