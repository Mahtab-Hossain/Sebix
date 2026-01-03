# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

## Functional requirements — detailed (one by one)

1. User Authentication & Registration
   - 1.1 Register (all roles): user can create account with email, password, name, phone, location, and select role (End User, Service Provider, Admin).
     - Acceptance: registration returns success and creates a user record with role.
   - 1.2 Login: email + password authentication and session/token issuance.
     - Acceptance: valid credentials return authenticated session/token; invalid credentials return error.
   - 1.3 Role-based access control: endpoint/middleware enforces role permissions.
     - Acceptance: protected endpoints allow only permitted roles.

2. End User Features
   - 2.1 Search services by category and location.
     - Acceptance: search API returns matching providers with distance and category filters.
   - 2.2 View provider profile (services, rates, availability, contact).
     - Acceptance: profile endpoint returns full provider details.
   - 2.3 Book a service (select provider, select date/time, confirm).
     - Acceptance: booking is created with status "pending" and notifies provider.
   - 2.4 View booking history and status.
     - Acceptance: user can list past and upcoming bookings with statuses.

3. Service Provider Features
   - 3.1 Create and edit profile (services offered, rates, availability, location).
     - Acceptance: provider profile endpoints allow CRUD of profile fields.
   - 3.2 Receive and respond to booking requests (accept/reject).
     - Acceptance: provider can change booking status; user sees updated status.
   - 3.3 View booking history and status.
     - Acceptance: provider booking list shows requests and past bookings.

4. Admin Features (basic MVP oversight)
   - 4.1 Manage users (view, deactivate).
     - Acceptance: admin can list users and disable accounts.
   - 4.2 Monitor bookings and locations (basic reporting).
     - Acceptance: admin can list bookings filtered by location/status.

5. Data & API notes (implementation hints)
   - 5.1 Core entities: User (role), ProviderProfile, ServiceCategory, Booking, Availability.
   - 5.2 Minimal API endpoints: /auth/register, /auth/login, /providers (search, profile), /bookings (create, list, update), /admin/users, /admin/bookings.
   - 5.3 Acceptance criteria: every function above includes an API response and DB state change test.
