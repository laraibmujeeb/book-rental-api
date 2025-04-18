<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        // Search by title
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Search by genre
        if ($request->has('genre')) {
            $query->where('genre', 'like', '%' . $request->genre . '%');
        }

        // Get only available books
        if ($request->has('available') && $request->available === 'true') {
            $query->where('available', true);
        }

        $books = $query->paginate(10);
        return response()->json($books);
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);
        return response()->json($book);
    }
}
