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
  wheel. Across 220 test births from 1800 to 2050, every position compared
  with the PyEphem ephemeris is within 0.053° of it, and
  `tests/AccuracyTest.php` checks each stays within the 0.1° On-the-cusp
  margin (see [Tests](#tests)).
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

## Tests

The Chart maths is covered by a PHPUnit suite. It needs PHP 8.2+ and
[Composer](https://getcomposer.org/), but no database, network access, or
Python. (The site itself still runs on PHP 7.4+.) From the repo root:

```sh
composer install   # installs PHPUnit, a dev-only dependency, into vendor/
composer test
```

GitHub Actions runs the same two commands on every push and pull request, on
PHP 8.2 to 8.5.

### Accuracy

`tests/AccuracyTest.php` builds a Chart for each of the 220 birth cases in
`tests/reference/positions.json` and compares every Body with PyEphem (the
Ascendant only within 60° of the equator; see below). Each must be within
0.1°, the On-the-cusp margin (`ASTRO_CUSP_MARGIN`), so a Placement is never
shown in the wrong Sign without the On-the-cusp note. The cases are 200
random births across the world from 1800 to 2050, plus hand-picked ones: the
first and last supported days, moments just before and just after a Sign
change for the Sun, Moon, and Ascendant, and birth times skipped or repeated
by a daylight-saving change.

Largest error measured across those cases, in degrees:

| Sun | Moon | Mercury | Venus | Mars | Jupiter | Saturn | Uranus | Neptune | Pluto | Ascendant |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 0.009 | 0.006 | 0.014 | 0.021 | 0.053 | 0.032 | 0.048 | 0.038 | 0.021 | 0.022 | 0.006 |

**Known limitation:** the Ascendant isn't verified for births beyond about 60°
latitude, north or south. The site still calculates one there, but the
reference data has no Ascendant for births beyond 60°, so it isn't checked.

### Reference data

The expected positions were generated once by
`tests/reference/generate_positions.py` and committed, so you don't need
Python to run the tests, only to add or change cases. The script uses a fixed
random seed and pins its dependencies (PyEphem 4.2.1 and tzdata), so it
always writes the same cases. Only the generation timestamp changes. To
regenerate with [uv](https://docs.astral.sh/uv/):

```sh
uv run tests/reference/generate_positions.py
```

Without uv, run `pip install ephem==4.2.1 tzdata==2026.6`, then
`python3 tests/reference/generate_positions.py`.

To make the comparison fair, the script converts PyEphem's output to the
quantity the PHP computes: apparent geocentric ecliptic longitude against the
true equinox of date. Before writing anything, it checks that conversion
against published positions. PyEphem has no Ascendant, so the script
calculates one from PyEphem's sidereal time with the standard formula,
independently of the PHP code. The script's header comment has the details.

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
| `tests/AccuracyTest.php` | Every Body checked against the PyEphem reference data |
| `tests/ChartBuildingTest.php` | Rules for what a Chart contains: missing or bad birth details, Uncertain placements, supported dates, time zones |
| `tests/SignTest.php`, `tests/CuspTest.php` | Sign lookup and On-the-cusp boundaries |
| `tests/reference/generate_positions.py` | Generates the reference data with PyEphem (Python, only to regenerate) |
| `tests/reference/positions.json` | Reference data: birth cases and expected positions |
