<?php

namespace App\Mail;

use App\Models\PublicComplaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;

    public function __construct(PublicComplaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function build()
    {
        return $this->subject('Kode Aduan Anda - ' . config('app.name'))
            ->view('emails.complaint-code');
    }
}
