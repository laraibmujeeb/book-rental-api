# Book Rental API

A Laravel API for managing book rentals with overdue notifications.

## Features

- User authentication (register, login, logout)
- Browse and search for books by title, author, or genre
- Rent and return books
- Automatic detection of overdue books
- Email notifications for overdue books
- Statistics on popular and overdue books

## Setup Instructions

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL or MariaDB
- Mailtrap account (for testing email notifications)

### Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/book-rental-api.git
   cd book-rental-api
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Set up environment variables:
   ```
   cp .env.example .env
   ```
   
   Edit the `.env` file to configure your database and mail settings.

4. Generate application key:
   ```
   php artisan key:generate
   ```

5. Create the database:
   ```
   mysql -u root -p
   CREATE DATABASE book_rental;
   EXIT;
   ```

6. Run migrations and seed the database:
   ```
   php artisan migrate:fresh --seed
   ```

7. Start the development server:
   ```
   php artisan serve
   ```

### API Endpoints

- **POST /api/register** - Register a new user
- **POST /api/login** - Login and get access token
- **POST /api/logout** - Logout (requires authentication)
- **GET /api/books** - Browse all books (supports filtering by title, author, genre)
- **GET /api/books/{id}** - View a specific book
- **POST /api/rentals** - Rent a book (requires authentication)
- **GET /api/rentals** - View rental history (requires authentication)
- **POST /api/rentals/{id}/return** - Return a book (requires authentication)
- **GET /api/statistics** - View statistics (requires authentication)

### Testing Overdue Notifications

1. Rent a book using the API
2. Manually update the due date in the database to make it overdue
3. Run the overdue check command: `php artisan books:check-overdue`
4. Check Mailtrap for the notification email
