<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BooksSeeder extends Seeder
{
    public function run()
    {
        $csvFile = database_path('data/books.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: $csvFile");
            return;
        }

        $file = fopen($csvFile, 'r');

        // Skip header row - read it but don't use it
        $header = fgetcsv($file);
        $this->command->info("Header row: " . implode(', ', $header));

        $count = 0;

        while (($data = fgetcsv($file)) !== false) {
            // Check if we have enough columns
            if (count($data) < 4) {
                $this->command->warn("Skipping row with insufficient data: " . implode(', ', $data));
                continue;
            }

            Book::create([
                'title' => trim($data[0], '"'),
                'author' => trim($data[1], '"'),
                'isbn' => trim($data[2], '"'),
                'genre' => trim($data[3], '"'),
                'available' => true,
                'rental_count' => 0,
                'overdue_count' => 0
            ]);

            $count++;
        }

        fclose($file);

        $this->command->info("Books seeded successfully: $count books added.");
    }
}
