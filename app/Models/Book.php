<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'genre',
        'available',
        'rental_count',
        'overdue_count'
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
