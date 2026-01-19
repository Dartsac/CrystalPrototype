# Crystal

[![Tests](https://github.com/Dartsac/CrystalPrototype/actions/workflows/tests.yml/badge.svg)](https://github.com/Dartsac/CrystalPrototype/actions/workflows/tests.yml)

Bringing you closer to yourself through spirituality. Crystal builds an
astrological understanding of each user from the minute and place they were
born, then turns it into an individualized spiritual guide and crystal shop.

## What it does

- **Sign up** with a display name, email, password, and birth date, time, and
  city. City suggestions come from the free Open-Meteo geocoding API, which
  also gives the coordinates and timezone needed for an exact chart.
- **Chart** (`about.php`): Sun, Moon, Ascendant, and every planet through
  Pluto, calculated in PHP (`includes/astrology.inc.php`) and drawn as a chart
  wheel. Positions agree with the PyEphem ephemeris to within about 0.06°.
- **Guide** (`profile.php`):
  - *This Month's Goal* targets the element your chart is weakest in.
  - *The Week's Plan* rotates through the challenges of your Sun, Moon, and
    Ascendant signs.
  - *Today's Guide* focuses on one of your placements each day, along with the
    sign the Moon is in today.
- **Crystal shop** (`shop.php`): every crystal ranked for your chart, with the
  reasons it was picked.

## Running it locally

Requirements: PHP 7.4+ with the `mysqli` and `curl` extensions, and MySQL (or MariaDB).

1. Create the database:

   ```sh
   mysql -u root < sql/schema.sql
   ```

   If you already have a `Crystal` database from before birth locations were
   stored, upgrade it instead:

   ```sh
   mysql -u root Crystal < sql/migrate-001-birth-location.sql
   ```

2. Start the site:

   ```sh
   php -S localhost:8080
   ```

   and open <http://localhost:8080>. It also runs as-is under MAMP/XAMPP.

The database connection defaults to `root` with no password on `localhost`,
database `Crystal`. Override with the `CRYSTAL_DB_HOST`, `CRYSTAL_DB_USER`,
`CRYSTAL_DB_PASSWORD`, and `CRYSTAL_DB_NAME` environment variables.

## Photos

Site and crystal photos live in `img/site/` and `img/crystals/`. They are
openly licensed (CC0, public domain, CC BY, or CC BY-SA, all of which allow
commercial use) and credited on the Photo credits page (`credits.php`), as
those licenses require. The list is in `includes/photo-credits.inc.php`: when
you add or replace a photo, add its credit there. A crystal without a photo
falls back to a drawn gem in its colors.

## Personal images

A user's profile picture and banner are read from `img/<username>/profile.png`
and `img/<username>/banner.gif` when they exist. Otherwise the profile shows
the user's initial and the Milky Way photo as a banner.

## Code map

| File | What it does |
| --- | --- |
| `includes/astrology.inc.php` | Sun, Moon, planet, and Ascendant positions |
| `includes/guide.inc.php` | Sign and planet write-ups, and the monthly/weekly/daily guide |
| `includes/crystals.inc.php` | Crystal catalog and recommendations |
| `includes/chart-wheel.inc.php` | SVG chart wheel |
| `includes/geocode.inc.php` | Birth city lookup |
| `includes/current-user.inc.php` | Loads the logged-in user and chart for a page |
| `includes/functions.inc.php` | Validation, users, and sessions |
