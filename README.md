# Teknohub

## Setup Guide

1. Clone this repo into your `htdocs` folder:
   ```bash
   git clone https://github.com/AngoyJessaMae/Teknohub.git
   ```

2. Open the project folder in terminal:
   ```bash
   cd Teknohub
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate the application key:
   ```bash
   php artisan key:generate
   ```

6. Create a database named `teknohub` in phpMyAdmin.

7. Open `.env` and update the database config:
   ```

8. Run the migrations:
   ```bash
   php artisan migrate
   ```

9. Start the server:
   ```bash
   php artisan serve
   ```

10. Open **http://localhost:8000** in your browser.
