# /// script
# requires-python = ">=3.9"
# dependencies = [
#     "ephem==4.2.1",
#     "tzdata==2026.6",
# ]
# ///
"""Generate the Reference ephemeris fixture for the accuracy test.

Writes tests/reference/positions.json: birth cases stored as the inputs a user
gives (local date, local time, IANA time zone, latitude, longitude), each with
the expected UTC moment and PyEphem's position of every Body.

Regenerate from the repo root with:

    uv run tests/reference/generate_positions.py

(or `pip install ephem==4.2.1 tzdata==2026.6` and run it with python3).
Only regenerating needs Python; the PHP tests just read the JSON.

Reference frame. Crystal computes apparent geocentric ecliptic longitude
against the true equinox of date, so that is what this writes. PyEphem's
Ecliptic(body) uses the astrometric J2000 position, which is up to ~2° off
over 1800-2050, so instead each body's apparent geocentric RA/Dec of date
(g_ra, g_dec: precession, nutation, aberration) is turned into ecliptic
longitude with the true obliquity. check_reference_frame() compares this
against published values and stops before writing if any is off.

Ascendant. PyEphem has no Ascendant, so it is calculated here from
PyEphem's apparent local sidereal time and the true obliquity with the
standard formula, only for latitudes within ±60°.

True obliquity. PyEphem doesn't expose it, so it is PyEphem's mean obliquity
(read back through its own ecliptic-to-equatorial conversion) plus the
nutation in obliquity from the leading IAU 1980 terms (Meeus, "Astronomical
Algorithms", ch. 22, accurate to ~0.1").

Places and time zones. Each case is a zone from tzdata's zone1970.tab at the
coordinates of its principal city. Those zones are canonical, so their
history doesn't depend on whether a time zone database includes backzone.
Local times inside a daylight-saving gap or overlap are redrawn.

Hand-picked cases. After the random cases come cases named "hand-picked: ..."
where errors matter most: the first and last supported days, moments just
before and just after a Sign change for the Sun, the Moon and the Ascendant,
and birth times skipped or repeated at a daylight-saving change, in New York
and Sydney. Their expected UTC moments follow Crystal's rule; see DST_CASES.
"""

import json
import math
import random
import zoneinfo
from datetime import date, datetime, timedelta, timezone
from importlib import resources
from pathlib import Path

import ephem
import tzdata

SEED = 20251122
CASE_COUNT = 200
FIRST_DAY = date(1800, 1, 1)
LAST_DAY = date(2050, 12, 31)
ASCENDANT_MAX_LATITUDE = 60.0
SIGNS = ["Aries", "Taurus", "Gemini", "Cancer", "Leo", "Virgo",
         "Libra", "Scorpio", "Sagittarius", "Capricorn", "Aquarius", "Pisces"]

# (label, zone, local date and time) on the first and last supported days.
RANGE_END_CASES = [
    ("first supported day", "Europe/London", "1800-01-01 00:00"),
    ("first supported day", "Australia/Sydney", "1800-01-01 12:00"),
    ("last supported day", "America/Sao_Paulo", "2050-12-31 18:00"),
    ("last supported day", "Europe/London", "2050-12-31 23:59"),
]

# (Body, Sign it enters, zone, UTC moment to search forward from). Each gives a
# case SIGN_CHANGE_OFFSET before and one after the moment the Reference
# ephemeris puts the Body on the Sign boundary. Ascendant zones are within ±60°.
SIGN_CHANGE_CASES = [
    ("Sun", "Aries", "Europe/Paris", "1820-03-01 00:00"),
    ("Sun", "Cancer", "Asia/Tokyo", "2045-06-01 00:00"),
    ("Moon", "Aries", "Africa/Lagos", "1955-01-01 00:00"),
    ("Moon", "Scorpio", "America/Sao_Paulo", "2031-01-01 00:00"),
    ("Ascendant", "Aries", "Europe/Tallinn", "1890-05-10 00:00"),
    ("Ascendant", "Libra", "Australia/Sydney", "2012-08-01 12:00"),
]
SIGN_CHANGE_OFFSET = timedelta(seconds=10)
SIGN_CHANGE_STEP = {"Sun": timedelta(days=1), "Moon": timedelta(hours=1), "Ascendant": timedelta(minutes=1)}

# (label, zone, local date and time) for a birth time skipped or repeated at a
# daylight-saving change. Crystal moves a skipped time forward by the gap and
# reads a repeated time as its first occurrence, as Python's default fold=0 does.
DST_CASES = [
    ("skipped hour", "America/New_York", "1995-04-02 02:30"),
    ("repeated hour", "America/New_York", "1995-10-29 01:30"),
    ("skipped hour", "Australia/Sydney", "2008-10-05 02:30"),
    ("repeated hour", "Australia/Sydney", "2008-04-06 02:30"),
]

OUTPUT = Path(__file__).with_name("positions.json")

EPHEMERIS_BODIES = {
    "Sun": ephem.Sun,
    "Moon": ephem.Moon,
    "Mercury": ephem.Mercury,
    "Venus": ephem.Venus,
    "Mars": ephem.Mars,
    "Jupiter": ephem.Jupiter,
    "Saturn": ephem.Saturn,
    "Uranus": ephem.Uranus,
    "Neptune": ephem.Neptune,
    "Pluto": ephem.Pluto,
}

# Use the pinned tzdata package, never the system's time zone files.
zoneinfo.reset_tzpath(to=[])


def julian_centuries_tt(moment):
    jd_tt = ephem.julian_date(moment) + ephem.delta_t(moment) / 86400.0
    return (jd_tt - 2451545.0) / 36525.0


def true_obliquity(moment):
    """True obliquity of the ecliptic (radians) at a PyEphem date."""
    pole = ephem.Equatorial(ephem.Ecliptic(0.0, math.pi / 2, epoch=moment), epoch=moment)
    mean = math.pi / 2 - float(pole.dec)

    t = julian_centuries_tt(moment)
    node = math.radians(125.04452 - 1934.136261 * t)
    sun = math.radians(280.4665 + 36000.7698 * t)
    moon = math.radians(218.3165 + 481267.8813 * t)
    nutation_arcsec = (9.20 * math.cos(node) + 0.57 * math.cos(2 * sun)
                       + 0.10 * math.cos(2 * moon) - 0.09 * math.cos(2 * node))
    return mean + math.radians(nutation_arcsec / 3600.0)


def ecliptic_longitude(ra, dec, obliquity):
    """Ecliptic longitude (degrees) of an equatorial position, all of date."""
    lon = math.atan2(math.sin(ra) * math.cos(obliquity) + math.tan(dec) * math.sin(obliquity), math.cos(ra))
    return math.degrees(lon) % 360.0


def body_longitude(name, moment):
    body = EPHEMERIS_BODIES[name]()
    body.compute(moment)
    return ecliptic_longitude(float(body.g_ra), float(body.g_dec), true_obliquity(moment))


def ascendant(moment, latitude, longitude):
    """Ecliptic longitude (degrees) rising on the eastern horizon."""
    observer = ephem.Observer()
    observer.date = moment
    observer.lon = math.radians(longitude)
    ramc = float(observer.sidereal_time())  # apparent local sidereal time
    obliquity = true_obliquity(moment)
    asc = math.atan2(
        math.cos(ramc),
        -(math.sin(ramc) * math.cos(obliquity) + math.tan(math.radians(latitude)) * math.sin(obliquity)),
    )
    return math.degrees(asc) % 360.0


def angular_distance(a, b):
    return abs((a - b + 180.0) % 360.0 - 180.0)


def from_terrestrial_time(text):
    """PyEphem (UT) date for a moment given in Terrestrial Time."""
    moment = ephem.Date(text)
    return ephem.Date(moment - ephem.delta_t(moment) / 86400.0)


def check_reference_frame():
    """Stop unless the frame matches published apparent positions of date.

    An astrometric (J2000) frame would be ~2° off in 1850 and ~0.7° in 2050;
    a missing nutation term would be off by up to ~0.005°.
    """
    tolerance = 0.002
    checks = [
        # USNO equinoxes and solstices (UT, to the minute: the Sun moves 0.0007°/min).
        ("Sun at the 1850 March equinox", body_longitude("Sun", ephem.Date("1850/3/20 23:02")), 0.0),
        ("Sun at the 1850 June solstice", body_longitude("Sun", ephem.Date("1850/6/21 19:59")), 90.0),
        ("Sun at the 2050 March equinox", body_longitude("Sun", ephem.Date("2050/3/20 10:19")), 0.0),
        ("Sun at the 2050 June solstice", body_longitude("Sun", ephem.Date("2050/6/21 03:33")), 90.0),
        # Meeus, "Astronomical Algorithms", example 47.a: 1992 April 12, 0h TT.
        ("Moon in Meeus example 47.a", body_longitude("Moon", from_terrestrial_time("1992/4/12")), 133.167265),
    ]

    # Meeus example 12.a: apparent sidereal time at Greenwich, 1987 April 10, 0h UT = 13h10m46.1351s.
    greenwich = ephem.Observer()
    greenwich.date = ephem.Date("1987/4/10")
    checks.append(("Sidereal time in Meeus example 12.a", math.degrees(float(greenwich.sidereal_time())),
                   (13 + 10 / 60 + 46.1351 / 3600) * 15))

    for label, actual, expected in checks:
        error = angular_distance(actual, expected)
        if error > tolerance:
            raise SystemExit(f"Reference frame check failed: {label}: got {actual:.6f}°, "
                             f"published {expected:.6f}°, off by {error:.6f}°")
        print(f"ok  {label}: off by {error:.6f}°")


def zone1970_places():
    """(zone, latitude, longitude) for every canonical zone, in name order."""
    def degrees(text, width):
        sign = -1 if text[0] == "-" else 1
        digits = text[1:]
        d, m, s = int(digits[:width]), int(digits[width:width + 2]), int(digits[width + 2:] or 0)
        return round(sign * (d + m / 60 + s / 3600), 4)

    places = []
    table = resources.files("tzdata.zoneinfo").joinpath("zone1970.tab").read_text()
    for line in table.splitlines():
        if line.startswith("#"):
            continue
        coordinates, zone = line.split("\t")[1:3]
        split = max(coordinates.rfind("+"), coordinates.rfind("-"))
        places.append((zone, degrees(coordinates[:split], 2), degrees(coordinates[split:], 3)))
    return sorted(places)


def to_utc(local, zone):
    """UTC moment of a local time; one skipped or repeated at a daylight-saving change uses fold=0."""
    return local.replace(tzinfo=zoneinfo.ZoneInfo(zone)).astimezone(timezone.utc)


def is_gap_or_overlap(local, zone):
    """Whether a local time falls in a daylight-saving gap or overlap."""
    tz = zoneinfo.ZoneInfo(zone)
    return local.replace(tzinfo=tz, fold=0).utcoffset() != local.replace(tzinfo=tz, fold=1).utcoffset()


def random_case(rng, places):
    """A birth at a real place and a local time that exists exactly once."""
    while True:
        zone, latitude, longitude = rng.choice(places)
        day = FIRST_DAY + timedelta(days=rng.randrange((LAST_DAY - FIRST_DAY).days + 1))
        local = datetime(day.year, day.month, day.day, *divmod(rng.randrange(24 * 60), 60))

        if is_gap_or_overlap(local, zone):
            continue
        utc = to_utc(local, zone)
        if not FIRST_DAY.year <= utc.year <= LAST_DAY.year:
            continue
        return zone, latitude, longitude, local, utc


def reference_longitude(body, utc, latitude, longitude):
    moment = ephem.Date(utc.replace(tzinfo=None))
    if body == "Ascendant":
        return ascendant(moment, latitude, longitude)
    return body_longitude(body, moment)


def sign_of(longitude):
    return SIGNS[int(longitude // 30.0) % 12]


def sign_change(body, sign, latitude, longitude, search_from):
    """First UTC moment (to the second) after search_from that body is in sign."""
    def in_sign(utc):
        return sign_of(reference_longitude(body, utc, latitude, longitude)) == sign

    before = search_from
    if in_sign(before):
        raise SystemExit(f"{body} is already in {sign} at {search_from}")
    while not in_sign(before + SIGN_CHANGE_STEP[body]):
        before += SIGN_CHANGE_STEP[body]
    after = before + SIGN_CHANGE_STEP[body]
    while after - before > timedelta(seconds=1):
        middle = before + (after - before) / 2
        before, after = (before, middle) if in_sign(middle) else (middle, after)
    return after.replace(microsecond=0)


def hand_picked_cases(places):
    """Cases at the range ends, either side of Sign changes, and at daylight-saving changes."""
    place = {zone: (latitude, longitude) for zone, latitude, longitude in places}
    cases = []

    for label, zone, text in RANGE_END_CASES:
        local = datetime.fromisoformat(text)
        if is_gap_or_overlap(local, zone):
            raise SystemExit(f"{label} {zone} {text} is at a daylight-saving change")
        utc = to_utc(local, zone)
        if not FIRST_DAY <= utc.date() <= LAST_DAY:
            raise SystemExit(f"{label} {zone} {text} is {utc} UTC, outside the supported range")
        cases.append(build_case(f"hand-picked: {label}", zone, *place[zone], local, utc))

    for body, sign, zone, text in SIGN_CHANGE_CASES:
        latitude, longitude = place[zone]
        if body == "Ascendant" and abs(latitude) > ASCENDANT_MAX_LATITUDE:
            raise SystemExit(f"{zone} is beyond the Ascendant's latitude limit")
        boundary = sign_change(body, sign, latitude, longitude,
                               datetime.fromisoformat(text).replace(tzinfo=timezone.utc))
        previous = SIGNS[SIGNS.index(sign) - 1]
        for when, utc, expected_sign in (("before", boundary - SIGN_CHANGE_OFFSET, previous),
                                         ("after", boundary + SIGN_CHANGE_OFFSET, sign)):
            if sign_of(reference_longitude(body, utc, latitude, longitude)) != expected_sign:
                raise SystemExit(f"{body} is not in {expected_sign} {when} entering {sign} at {utc}")
            local = utc.astimezone(zoneinfo.ZoneInfo(zone)).replace(tzinfo=None)
            if is_gap_or_overlap(local, zone):
                raise SystemExit(f"{body} enters {sign} at a daylight-saving change in {zone}")
            name = f"hand-picked: {body} enters {sign}, {SIGN_CHANGE_OFFSET.seconds} s {when}"
            cases.append(build_case(name, zone, latitude, longitude, local, utc))

    for label, zone, text in DST_CASES:
        local = datetime.fromisoformat(text)
        if not is_gap_or_overlap(local, zone):
            raise SystemExit(f"{zone} {text} is not at a daylight-saving change")
        utc = to_utc(local, zone)
        cases.append(build_case(f"hand-picked: daylight saving, {label}", zone, *place[zone], local, utc))

    return cases


def build_case(name, zone, latitude, longitude, local, utc):
    moment = ephem.Date(utc.replace(tzinfo=None))
    longitudes = {body: round(body_longitude(body, moment), 6) for body in EPHEMERIS_BODIES}
    if abs(latitude) <= ASCENDANT_MAX_LATITUDE:
        longitudes["Ascendant"] = round(ascendant(moment, latitude, longitude), 6)
    return {
        "name": name,
        "input": {
            "date": local.strftime("%Y-%m-%d"),
            "time": local.strftime("%H:%M:%S" if local.second else "%H:%M"),
            "timezone": zone,
            "latitude": latitude,
            "longitude": longitude,
        },
        "expected": {
            "utc": utc.strftime("%Y-%m-%dT%H:%M:%SZ"),
            "longitudes": longitudes,
        },
    }


def main():
    check_reference_frame()

    rng = random.Random(SEED)
    places = zone1970_places()
    cases = [build_case(f"random {i:03d}", *random_case(rng, places)) for i in range(1, CASE_COUNT + 1)]
    cases += hand_picked_cases(places)

    fixture = {
        "metadata": {
            "generator": "tests/reference/generate_positions.py",
            "pyephem_version": ephem.__version__,
            "tzdata_version": tzdata.IANA_VERSION,
            "seed": SEED,
            "generated_at": datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"),
            "longitudes": "apparent geocentric ecliptic longitude, true equinox of date, degrees",
            "ascendant_max_latitude": ASCENDANT_MAX_LATITUDE,
        },
        "cases": cases,
    }
    OUTPUT.write_text(json.dumps(fixture, indent=1) + "\n")
    print(f"Wrote {len(cases)} cases to {OUTPUT}")


if __name__ == "__main__":
    main()
