<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $rentals = $request->user()->rentals()
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($rentals);
    }

    public function rent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $book = Book::findOrFail($request->book_id);

        if (!$book->available) {
            return response()->json([
                'message' => 'This book is not available for rent'
            ], 400);
        }

        // Check if user has any overdue books
        $overdueCount = $request->user()->rentals()
            ->where('is_overdue', true)
            ->whereNull('returned_at')
            ->count();

        if ($overdueCount > 0) {
            return response()->json([
                'message' => 'You have overdue books. Please return them before renting new ones.'
            ], 400);
        }

        // Set rental period (2 weeks)
        $rentedAt = Carbon::now();
        $dueAt = $rentedAt->copy()->addWeeks(2);

        $rental = Rental::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'rented_at' => $rentedAt,
            'due_at' => $dueAt,
        ]);

        // Update book availability
        $book->available = false;
        $book->rental_count += 1;
        $book->save();

        return response()->json([
            'message' => 'Book rented successfully',
            'rental' => $rental->load('book'),
        ]);
    }

    public function return(Request $request, $id)
    {
        $rental = Rental::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->whereNull('returned_at')
            ->firstOrFail();

        $rental->returned_at = Carbon::now();
        $rental->save();

        // Update book availability
        $book = $rental->book;
        $book->available = true;
        $book->save();

        return response()->json([
            'message' => 'Book returned successfully',
            'rental' => $rental->load('book'),
        ]);
    }
}
