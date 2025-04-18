<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Rental;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        // Most overdue book
        $mostOverdueBook = Book::orderBy('overdue_count', 'desc')->first();

        // Most popular book (most rented)
        $mostPopularBook = Book::orderBy('rental_count', 'desc')->first();

        // Least popular book (least rented)
        $leastPopularBook = Book::orderBy('rental_count', 'asc')->first();

        // Current overdue rentals count
        $currentOverdueCount = Rental::where('is_overdue', true)
            ->whereNull('returned_at')
            ->count();

        return response()->json([
            'most_overdue_book' => $mostOverdueBook,
            'most_popular_book' => $mostPopularBook,
            'least_popular_book' => $leastPopularBook,
            'current_overdue_count' => $currentOverdueCount,
        ]);
    }
}
