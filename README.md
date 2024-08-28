<p align="center"><a href="https://laravel.com" target="_blank">
<img src="https://openfoodnetwork.org.au/rails/active_storage/blobs/redirect/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBBbVFCIiwiZXhwIjpudWxsLCJwdXIiOiJibG9iX2lkIn19--34ea38a5bd4cc160f7bbd5a7e0490805fe6fd137/logo-australia.png" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/openfoodfoundation/vine/actions"><img src="https://github.com/openfoodfoundation/vine/actions/workflows/main-push-deploy.yml/badge.svg" alt="Build Status"></a>
<a href="https://github.com/openfoodfoundation/vine/actions"><img src="https://github.com/openfoodfoundation/vine/workflows/develop-push-deploy.yml/badge.svg" alt="Build Status"></a>
</p>

# Open Food Foundation VINE: Vouchers Integration Engine

VINE is a voucher management platform owned & managed by the Open Food Foundation. It is a Laravel application, leveraging the power of Vue3 and TailwindCSS.

## Developer initial setup

To run the locally on your development environment, follow these steps:

1. Clone this repository to your local machine.
2. Install the required dependencies using [`npm install`]
3. Install the required composer dependencies using [`composer install`]
4. Set up the `.env` configuration by copying the `.env.example` file
5. Create a database with the same name from the `.env` file.
6. Populate the database with the required tables and data using [`php artisan migrate --seed`]
7. Run the development server using the command [`npm run dev`]


All pushes to develop and master branches will run the app through GitHub Actions. If the tests pass, the action will send a webhook request to the chosen deployment platform, where it will be deployed.


### How to build the application and leverage assets

To run a testing system locally run the following in the root of your project, in terminal:

```
npm run dev
```

### Testing policies

Tests are run on every push to the following branches:

- feature/*
- develop
- main

To run tests locally, do the following:

- Run `php artisan migrate:fresh --env=testing`
- Run one of the following
- `./vendor/bin/phpunit` or
- `php artisan test`

### Deployment policies

All pushes to develop and master branches will run the app through GitHub Actions. If the tests pass, the system will deploy the app using the configured deployment tool.

## API documentation

API documentation is being generated using a package called [Scribe](https://scribe.knuckles.wtf/).

It does this using annotations on API endpoints via PHP attributes. The configuration file is found  at `config/scribe.php`. It will publish its assets in the `public/scribe`, which will include an `openpi.yaml` file as well as a Postman `collection.json` file.

To re-generate these assets and the accompanying documentation page, run `php artisan scribe:generate`. This command will also automatically run whenever the `composer update` command is executed. The docs can be found at the path `/api-documentation`.


## Code of conduct

Our goal is to create a welcoming and inclusive environment for everyone who participates in this project. This code of conduct outlines our expectations for behavior and the procedures for addressing unacceptable behavior.

### Expected Behavior

- **Respect**: Treat everyone with respect, and be considerate of diverse opinions and backgrounds.
- **Professionalism**: Communicate in a professional and constructive manner. Provide helpful feedback and collaborate
  effectively.

### Acknowledgement

By participating in this project, you agree to adhere to this code of conduct.

## Contributing policies

TODO 


## Release note collation

Release notes to be begun being generated only after production v1.0

----
# Configuration

----

## Emails
Currently supported integrations: `smtp`, and `mailgun`.

#### SMTP Emails
To configure your system to use a third party SMTP service, update the following environment variables. Note that you may need to update some configuration at your SMTP server provider to enable sending from your server installation IP range.

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS="vine@openfoodnetwork.org.au"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Mailgun
To use Mailgun, you'll need a configured domain, an API secret and to update the following environment variables. Note that MAILGUN_ENDPOINT is usually only required if not using the US region to send mail from.

```dotenv
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=
MAILGUN_SECRET=
MAILGUN_ENDPOINT=[if necessary to switch to EU]
```
---

## Application Logging Configuration
We've configured the system so that you can use environment variables to configure which logging service you would like to use for the installation. To configure logging, update the `LOG_STACK` environment variable. 

```dotenv
# A single log file located on the server
LOG_STACK=single 
```
You may configure multiple log channels also:
```dotenv
# Use both a single and daily log
LOG_STACK=single,daily 
```

### Third party logging services
In production, it's not great to allow log files build up in size or they might take up a lot of space on the server, so we have built in the following integrations. If you wish to integrate a different service, create an issue.

#### Flare (flareapp.io)
Update your env file:
```dotenv
# Add flare key
FLARE_KEY=your_flare_key
# Update your log stack
LOG_STACK=flare  # or daily,flare,single etc for multiple
```

#### Sentry (sentry.io)
Update your env file:
```dotenv
# Add flare key
SENTRY_LARAVEL_DSN=your_sentry_dsn
# Update your log stack
LOG_STACK=sentry  # or sentry,single etc for multiple
```
----

## Code Linting / Formatting

Todo - explain how pint works, and under what circumstances it'll run

---- 

## File Storage: Local vs. AWS S3
You are free to use whichever storage cloud you like - the default environment variable of `FILESYSTEM_DISK=local` means that the application will use the local server.

To configure the system to use AWS S3, first create a bucket in S3 and some access keys for your implementation. then update the following environment variables:

```dotenv
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=[YourApplicationKey]
AWS_SECRET_ACCESS_KEY=[YourApplicationSecret]
AWS_DEFAULT_REGION=[S3 Bucket location eg ap-southeast-2]
AWS_BUCKET=[S3 Bucket Name]
AWS_USE_PATH_STYLE_ENDPOINT=false
```
The S3 bucket uses a configuration called `root`, which separates the base bucket into the environment that the application is running under. This is based on `APP_ENV`. If you are running `APP_ENV=local` and your bucket was called `AWS_BUCKET=ofn-vine-uk`, your bucket folder structure would look like this:

```
/ofn-vine-uk
    /local 👈 // Objects would be placed here by default at the path you provide
    /staging
    /production
```

NOTE: No extra config is required for this behaviour on AWS S3. It is not the default for any other filesystem storage providers.

## Queue Configuration
By default, the queue is set to `sync`: `QUEUE_CONNECTION=sync` which runs jobs as part of the current request. You are free to reconfigure this. 

If using AWS SQS, you need to create queues for each environment, and update the env vars as follows:

```dotenv
QUEUE_CONNECTION=sqs
SQS_QUEUE="queue-name-${APP_ENV}" # eg ofn-vine-uk-local as set in AWS SQS
SQS_PREFIX=https://sqs.${AWS_DEFAULT_REGION}.amazonaws.com/[YOUR-AWS-ACCOUNT-ID]
```
You'll also need to run at least one properly configured queue worker on your server environment for this to work.

## API Middleware - Protecting The API

In /bootstrap.app.php we've configured middleware as follows:

```php
$middleware->alias(
    [
        'abilities' => CheckForAnyTokenAbilities::class,
    ]);
```

This runs the API middleware through `CheckForAnyTokenAbilities`, checking the incoming personal access token for the
correct ability.

To configure an endpoint to be guarded by an ability, do the following: add a middleware ability (one or more) to the
definition in the API routes file.

```php
//
  ->middleware(
      [
          'abilities:ability-one,ability-two'
      ]
  );
```

A more concrete example would be the /system-statistics endpoint:

```php
Route::get('/system-statistics', [ApiSystemStatisticsController::class, 'index'])
      ->name('api.v1.system-statistics.getMany')
      ->middleware(
          [
              'abilities:' .
              PersonalAccessTokenAbility::SUPER_ADMIN->value . ',' .
              PersonalAccessTokenAbility::SYSTEM_STATISTICS_READ->value
          ]
      );
```

.. the above endpoint allows API tokens with `super-admin` and `system-statistic-read` abilities. Either will work; anything else in place of these will fail with a 401.

---


# Integrations: API Tokens & Abilities

The Vine system issues long-lived API tokens so that external systems may integrate with it. You will be issued with a
text-based API token. Add it to your API requests as an Authorization header:

```
Accept: application/json
Authorization: Bearer [API_TOKEN]
```

Your API token will have `abilities` associated with them. Abilities are relevant to the API endpoint you are trying to
access. For example:

POST /api/v1/system-statistics => 'system-statistics-create'
GET /api/v1/system-statistics => 'system-statistics-read'
GET /api/v1/system-statistics/1234 => 'system-statistics-read'
PUT /api/v1/system-statistics/1234 => 'system-statistics-update'
DELETE /api/v1/system-statistics/1234 => 'system-statistics-delete'

- An API token can have more than 1 ability.
- Certain endpoint verbs will always return a 403 (Method Not Allowed), regardless of token abilities.
- More information will be available in out OpenAPI Spec documentation.



