## Web App using steps.

1. composer install, configure .env (database name key generate)
2. php artisan ui vue --auth (if already exist choose oprtion no)
3. npm install
4. npm run dev
5. php artisan storage:link
6. php artisan migrate
7. php artisan db:seed --class=CreateUsersSeeder 
8. php artisan db:seed --class=CreateScrapSettingsSeeder
9. Go to your website - example.com/login and login with cred. 
email - admin@10web.io, password 123456 and you can update Scrap Settings.
10. Run cron job command php artisan get:scraped-articles