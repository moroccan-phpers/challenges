# Mail microservice

### Name 
> OUTAMA Othmane 

### Email
> outama.othmane@gmail.com

### Description
> This is a microservice to handle "email sending" using third party mail services.
> Built using Laravel (PHP Framework)

### Assumptions

- Whenever we try to send an email, we create it in our DB with an auto-generated uuid as request_id
- I assume that the third party mail service delivery can accept our request_id

### Information

- All the mail delivery services that we want to use should be placed in `app/MailDeliveryService/Adapters` and implement the `App\MailDeliveryService\MailDeliveryServiceContract`
- For the sake of example, I created `MailgunExample` and it doesn't connect to the real API.
- In real world situation, the method `isTrustedWebhook@MailDeliveryServiceContract` should be implemented and should not always return true.

### Architecture

![Microservice Architecture](https://i.imgur.com/99C2Ktg.png)

### Project setup

In order to run this project locally, you need to have `PHP`, `Composer` and a database server installed in your machine. In my case for example I have `MySQL`.

First, we need to install the required dependencies by running:

```shell
composer install
```

Then we will create a file that contains our environment variables

```shell
cp .env.example .env

# Generate the secret key
php artisan key:generate
```

Then insert your database credentials, example:

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mydbname
DB_USERNAME=myusername
DB_PASSWORD=mypassword
```

Next, we need to create the necessary tables inside our database.

We can achieve that by executing:

```shell
php artisan migrate
```

Finally, it's time to serve and run our application

```shell
php artisan serve
```

#### Run unit tests

```shell
# For Mac and Linux users
./vendor/bin/phpunit

# For Windows
php vendor/phpunit/phpunit/phpunit
```

### Improvements
