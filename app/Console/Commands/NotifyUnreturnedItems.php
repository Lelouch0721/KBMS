<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Borrow;
use App\Mail\UnreturnedItemNotification;
use Carbon\Carbon;

class NotifyUnreturnedItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:unreturned-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to borrowers who have not returned items by 8 PM.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        if ($now->hour >= 20) {
            $unreturnedItems = Borrow::where('status', 'borrowed')
            ->leftJoin('users', 'borrows.borrower', '=', 'users.email')
            ->leftJoin('items', 'borrows.item_id', '=', 'items.id')
            ->select('users.email', 'users.name as borrower_name', 'items.name as item_name', 'borrows.type')
            ->get();        

            foreach ($unreturnedItems as $item) {
                Mail::to($item->email)->send(new UnreturnedItemNotification($item));
            }

            $this->info('Email notifications have been sent to all borrowers with unreturned items.');
        } else {
            $this->info('It is not yet 8 PM. No notifications sent.');
        }
    }
}
