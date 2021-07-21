## Web App using steps.
configure .env

1. composer install
2. php artisan ui vue --auth
3. npm install
4. npm run dev
5. php artisan storage:link
6. php artisan migrate
7. php artisan db:seed --class=CreateUsersSeeder 
8. php artisan db:seed --class=CreateScrapSettingsSeeder
9. Go to example.com/login and login with cred. 
email - admin@10web.io, password 123456 and you can update Scrap Settings.
10. Run command php artisan get:scraped-articles