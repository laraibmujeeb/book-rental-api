<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueBookNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $rental;

    public function __construct(Rental $rental)
    {
        $this->rental = $rental;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $book = $this->rental->book;

        return (new MailMessage)
            ->subject('Overdue Book Rental')
            ->line('You have an overdue book rental.')
            ->line("Book: {$book->title} by {$book->author}")
            ->line("Due date: {$this->rental->due_at->format('Y-m-d')}")
            ->line('Please return the book as soon as possible to avoid additional fees.')
            ->action('View Rental Details', url('/rentals'));
    }

    public function toArray($notifiable)
    {
        return [
            'rental_id' => $this->rental->id,
            'book_id' => $this->rental->book_id,
            'book_title' => $this->rental->book->title,
            'due_at' => $this->rental->due_at,
        ];
    }
}
