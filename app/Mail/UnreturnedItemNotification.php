<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class UnreturnedItemNotification extends Mailable
{
    public $item;

    /**
     * Create a new message instance.
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Reminder: Please return your borrowed item')
                    ->view('emails.unreturned_item_notification')
                    ->with('item', $this->item);
    }
}
