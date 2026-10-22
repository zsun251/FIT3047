# RespiraCare Online

A web app for managing respiratory care. Patients record vital data and symptoms. Clinicians and staff manage patients, appointments, reminders and resources. The app was built for Monash FIT3047 (team 187) with CakePHP 5.

## Requirements

- PHP 8.2 or later
- [Composer](https://getcomposer.org/)
- MySQL or MariaDB

## Getting started

Run all commands from the project root.

### 1. Install dependencies

```sh
composer install
```

This also creates `config/app_local.php` from `config/app_local.example.php`. That file holds your local settings and is gitignored, so each developer has their own copy.

### 2. Create a database and user

Use your MySQL root account (or any account that can create databases and users). The names and password below are examples; pick your own.

```sh
mysql -u root -p -e "
  CREATE DATABASE respiracare;
  CREATE USER 'respiracare'@'localhost' IDENTIFIED BY 'password';
  GRANT ALL PRIVILEGES ON respiracare.* TO 'respiracare'@'localhost';"
```

### 3. Point the app at your database

Open `config/app_local.php`. Under `Datasources` → `default`, set the values from step 2:

```php
'username' => 'respiracare',
'password' => 'password',
'database' => 'respiracare',
```

The template ships with `my_app` / `secret` / `my_app`. If you leave those values, the app cannot connect to the database.

### 4. Create the tables and load sample data

```sh
# App tables
mysql -u respiracare -p respiracare < config/schema/schema.sql

# Sample users, patients, appointments, etc. (development only)
mysql -u respiracare -p respiracare < config/schema/seed.sql

# schema.sql already includes the changes from config/Migrations,
# so mark those migrations as applied instead of running them
bin/cake migrations mark_migrated

# content_blocks table (editable site text) from the ContentBlocks plugin
bin/cake migrations migrate -p ContentBlocks
bin/cake migrations seed --seed ContentBlocksSeed
```

Don't run `bin/cake migrations migrate` on a fresh import. It fails with `Duplicate column name 'reset_token'` because `schema.sql` already has those columns.

### 5. Run the app

```sh
bin/cake server
```

Open http://localhost:8765.

### Test accounts

`seed.sql` creates these accounts. All of them use the password `password`.

| Username     | Role      |
|--------------|-----------|
| `admin1`     | Admin     |
| `clinician1` | Clinician |
| `patient1`   | Patient   |

`seed.sql` contains more sample accounts (for example `admin2`, `clinician2`).

## Working with the database

Each developer uses their own local database. Databases are not shared through Git; only the files that build them are.

**Changing the table structure.** Add a migration so everyone can apply the same change:

```sh
bin/cake bake migration AddSomethingToTable
```

Also update `config/schema/schema.sql`, so new setups get the change from step 4.

**After pulling someone else's migration**, apply it to your database:

```sh
bin/cake migrations migrate
bin/cake migrations status   # check which migrations have run
```

If `migrations status` shows `** MISSING **`, a migration was run on your database (usually from another branch) but its file is not on your current branch. Your database is then ahead of the code.

**Resetting to a clean state.** Re-import the schema and seed files, then repeat the remaining step 4 commands. **This deletes all data in that database.**

**Sharing exact data with a teammate** (for example, to reproduce a bug):

```sh
mysqldump -u respiracare -p respiracare > dump.sql   # export
mysql -u respiracare -p respiracare < dump.sql       # import
```

Don't commit dump files.

## Project structure

| Path | Contents |
|------|----------|
| `src/Controller/` | Controllers (Patients, Clinicians, Appointments, VitalData, Reminders, …) |
| `src/Model/` | Tables and entities |
| `src/Policy/` | Authorization policies (who can see or do what) |
| `templates/` | Views, layouts and email templates |
| `webroot/` | CSS, JavaScript and images |
| `config/app_local.php` | Your local settings (database, email). Not committed. |
| `config/schema/` | `schema.sql` (tables), `seed.sql` (sample data), `production.sql` (initial production data) |
| `config/Migrations/` | Database migrations |

## Production

For a production deployment, import `config/schema/schema.sql` and then `config/schema/production.sql` instead of `seed.sql`. `production.sql` creates a single `admin` account with the temporary password `password`. Change that password right after the first login.

## Troubleshooting

- **Can't connect to the database:** check the `Datasources.default` values in `config/app_local.php`, and make sure MySQL/MariaDB is running.
- **Password reset emails don't arrive:** set up `EmailTransport` in `config/app_local.php` with a real SMTP server.
