# Censony Tech Test

### Set-up

1. Clone the repository
2. `composer install`
3. `npm install`
4. `php artisan key:generate`
5. Set up MySQL in the `.env`
6. `php artisan migrate --seed`
7. `composer run dev`

### How to use

1. Login with the following credentials:
    - Email: `test@example.com`
    - Password: `password`
2. Go to `/posts`

> [!NOTE]
> While the spec didn't mention it, I used InertiaJS because it came with the Vue starter kit that I used to get up and running.
