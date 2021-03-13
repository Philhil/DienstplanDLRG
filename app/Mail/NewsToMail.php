<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewsToMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $news;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\News $news)
    {
        $this->news = $news;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client = $this->news->client()->first();
        return $this->subject('Dienstplan👂: '. $this->news->title)->view('email.news')->with([
            'news' => $this->news,
        ])->replyTo($client->mailReplyAddress, $client->mailSenderName);
    }
}
