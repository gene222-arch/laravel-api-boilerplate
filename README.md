## Laravel Api Boilerplate
A laravel app boilerplate for building or developing a restful api application.



What features included?
* Login
* Register
* ForgotPassword
* Verify Email
* Custom EnsureEmailIsVerified middleware

Installation:
- Fork and clone the repository
- Change your directory into your cloned repository
```
    cd directory/nested-directory/laravel-api-boilerplate
```
- Create your database
- Thru your terminal, create a copy of the `.env.example` file
```
    cp .env.example .env
```
- configure your `.env` file and update the database configuration
```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_db_name
    DB_USERNAME=your_usename
    DB_PASSWORD=your_password
```
- Thru your terminal, install the composer packages
```
    composer install
```
- generate app key
```
    php artisan key:generate
```
- This project uses `laravel passport`, in order to make use of this package generate your oauth clients, thru your terminal copy the command below;
```
    php artisan passport:install
```
- Once oauth clients are generated copy the value of Laravel Personal Access Client and Laravel Password Grant Client `id` and `secret` and paste it in your `.env` file
```
    PASSPORT_PERSONAL_ACCESS_CLIENT_ID=
    PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=
    PASSPORT_PASSWORD_GRANT_CLIENT_ID=
    PASSPORT_PASSWORD_GRANT_CLIENT_SECRET=
```

# How to know if your laravel app is properly working as it should?
This boilerplate uses the famed software development process called `Test Driven Development (TDD)`. It uses Laravel's beautiful feature or unit testing feature and you just have to run the test to determine if it's properly working.

# Supported Versions

| Version | Laravel Version |
| :-----: | :-:             |
| 1.x     | 9.x             |

> **_NOTE:_**  When verifying an account you need to define the client app url in the `.env` file.