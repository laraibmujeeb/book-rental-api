<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Rental;
use App\Notifications\OverdueBookNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueBooks extends Command
{
    protected $signature = 'books:check-overdue';
    protected $description = 'Check for overdue books and send notifications';

    public function handle()
    {
        $now = Carbon::now();

        // Find rentals that are overdue but not marked as overdue yet
        $overdueRentals = Rental::where('due_at', '<', $now)
            ->where('is_overdue', false)
            ->whereNull('returned_at')
            ->get();

        $this->info("Found {$overdueRentals->count()} new overdue rentals.");

        foreach ($overdueRentals as $rental) {
            // Mark as overdue
            $rental->is_overdue = true;
            $rental->save();

            // Update book statistics
            $book = $rental->book;
            $book->overdue_count += 1;
            $book->save();

            $this->info("Marked rental #{$rental->id} for book '{$book->title}' as overdue.");
        }

        // Find overdue rentals where notification hasn't been sent yet
        $notificationRentals = Rental::where('is_overdue', true)
            ->where('notification_sent', false)
            ->whereNull('returned_at')
            ->with(['user', 'book'])
            ->get();

        $this->info("Sending notifications for {$notificationRentals->count()} overdue rentals.");

        foreach ($notificationRentals as $rental) {
            $user = $rental->user;
            $user->notify(new OverdueBookNotification($rental));

            // Mark notification as sent
            $rental->notification_sent = true;
            $rental->save();

            $this->info("Sent notification to {$user->email} for book '{$rental->book->title}'.");
        }

        return 0;
    }
}
