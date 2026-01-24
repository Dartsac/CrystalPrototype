<?php
/*
 * Crystal astrology engine.
 *
 * Pure PHP, no external services. Computes the tropical zodiac positions
 * (ecliptic longitude of date) of the Sun, Moon, planets and the Ascendant
 * for a moment of birth.
 *
 * Sources:
 *  - Sun: Jean Meeus, "Astronomical Algorithms", ch. 25 (low precision, ~0.01°)
 *  - Moon: Meeus ch. 47, the largest periodic terms (~0.05°)
 *  - Planets: E.M. Standish (JPL), "Keplerian Elements for Approximate
 *    Positions of the Major Planets", table 1, valid 1800-2050 AD
 *  - Sidereal time / Ascendant: Meeus ch. 12 and the standard ascendant formula
 *
 * That precision is far finer than the 30° width of a sign; it only matters
 * for placements within a few hundredths of a degree of a cusp.
 */

const ZODIAC_SIGNS = [
	'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo',
	'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces',
];

// Supported birth years (limited by the planetary elements above).
const ASTRO_MIN_YEAR = 1800;
const ASTRO_MAX_YEAR = 2050;

// Display order of the bodies in a chart.
const CHART_BODIES = ['Sun', 'Moon', 'Ascendant', 'Mercury', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'Uranus', 'Neptune', 'Pluto'];

/*
 * JPL Keplerian elements (J2000 ecliptic and equinox), table 1.
 * Each row: [value at J2000, rate per Julian century] for
 * a (au), e, I (deg), L (deg), longitude of perihelion (deg), longitude of node (deg).
 */
const PLANET_ELEMENTS = [
	'Mercury' => [[0.38709927, 0.00000037], [0.20563593, 0.00001906], [7.00497902, -0.00594749], [252.25032350, 149472.67411175], [77.45779628, 0.16047689], [48.33076593, -0.12534081]],
	'Venus'   => [[0.72333566, 0.00000390], [0.00677672, -0.00004107], [3.39467605, -0.00078890], [181.97909950, 58517.81538729], [131.60246718, 0.00268329], [76.67984255, -0.27769418]],
	'EMBary'  => [[1.00000261, 0.00000562], [0.01671123, -0.00004392], [-0.00001531, -0.01294668], [100.46457166, 35999.37244981], [102.93768193, 0.32327364], [0.0, 0.0]],
	'Mars'    => [[1.52371034, 0.00001847], [0.09339410, 0.00007882], [1.84969142, -0.00813131], [-4.55343205, 19140.30268499], [-23.94362959, 0.44441088], [49.55953891, -0.29257343]],
	'Jupiter' => [[5.20288700, -0.00011607], [0.04838624, -0.00013253], [1.30439695, -0.00183714], [34.39644051, 3034.74612775], [14.72847983, 0.21252668], [100.47390909, 0.20469106]],
	'Saturn'  => [[9.53667594, -0.00125060], [0.05386179, -0.00050991], [2.48599187, 0.00193609], [49.95424423, 1222.49362201], [92.59887831, -0.41897216], [113.66242448, -0.28867794]],
	'Uranus'  => [[19.18916464, -0.00196176], [0.04725744, -0.00004397], [0.77263783, -0.00242939], [313.23810451, 428.48202785], [170.95427630, 0.40805281], [74.01692503, 0.04240589]],
	'Neptune' => [[30.06992276, 0.00026291], [0.00859048, 0.00005105], [1.77004347, 0.00035372], [-55.12002969, 218.45945325], [44.96476227, -0.32241464], [131.78422574, -0.00508664]],
	'Pluto'   => [[39.48211675, -0.00031596], [0.24882730, 0.00005170], [17.14001206, 0.00004818], [238.92903833, 145.20780515], [224.06891629, -0.04062942], [110.30393684, -0.01183482]],
];

/*
 * Moon periodic terms for longitude (Meeus table 47.A), largest first.
 * Each row: [D, M, M', F, coefficient in 1e-6 degrees].
 */
const MOON_LONGITUDE_TERMS = [
	[0, 0, 1, 0, 6288774], [2, 0, -1, 0, 1274027], [2, 0, 0, 0, 658314], [0, 0, 2, 0, 213618],
	[0, 1, 0, 0, -185116], [0, 0, 0, 2, -114332], [2, 0, -2, 0, 58793], [2, -1, -1, 0, 57066],
	[2, 0, 1, 0, 53322], [2, -1, 0, 0, 45758], [0, 1, -1, 0, -40923], [1, 0, 0, 0, -34720],
	[0, 1, 1, 0, -30383], [2, 0, 0, -2, 15327], [0, 0, 1, 2, -12528], [0, 0, 1, -2, 10980],
	[4, 0, -1, 0, 10675], [0, 0, 3, 0, 10034], [4, 0, -2, 0, 8548], [2, 1, -1, 0, -7888],
	[2, 1, 0, 0, -6766], [1, 0, -1, 0, -5163], [1, 1, 0, 0, 4987], [2, -1, 1, 0, 4036],
	[2, 0, 2, 0, 3994], [4, 0, 0, 0, 3861], [2, 0, -3, 0, 3665], [0, 1, -2, 0, -2689],
	[2, 0, -1, 2, -2602], [2, -1, -2, 0, 2390], [1, 0, 1, 0, -2348], [2, -2, 0, 0, 2236],
	[0, 1, 2, 0, -2120], [0, 2, 0, 0, -2069], [2, -2, -1, 0, 2048], [2, 0, 1, -2, -1773],
	[2, 0, 0, 2, -1595], [4, -1, -1, 0, 1215], [0, 0, 2, 2, -1110], [3, 0, -1, 0, -892],
	[2, 1, 1, 0, -810], [4, -1, -2, 0, 759], [0, 2, -1, 0, -713], [2, 2, -1, 0, -700],
	[2, 1, -2, 0, 691], [2, -1, 0, -2, 596], [4, 0, 1, 0, 549], [0, 0, 4, 0, 537],
	[4, -1, 0, 0, 520], [1, 0, -2, 0, -487], [2, 1, 0, -2, -399], [0, 0, 2, -2, -381],
	[1, 1, 1, 0, 351], [3, 0, -2, 0, -340], [4, 0, -3, 0, 330], [2, -1, 2, 0, 327],
	[0, 2, 1, 0, -323], [1, 1, -1, 0, 299], [2, 0, 3, 0, 294],
];

function astroNorm360(float $deg): float {
	$deg = fmod($deg, 360.0);
	return $deg < 0 ? $deg + 360.0 : $deg;
}

function astroSin(float $deg): float { return sin(deg2rad($deg)); }
function astroCos(float $deg): float { return cos(deg2rad($deg)); }

/* Julian Day (UT) of a moment. */
function astroJulianDay(DateTimeInterface $moment): float {
	return $moment->getTimestamp() / 86400.0 + 2440587.5;
}

/*
 * Approximate ΔT = TT − UT in seconds, interpolated from observed values.
 * Ignoring it entirely would shift the Moon by at most ~0.02°; this keeps
 * the error well below that.
 */
function astroDeltaT(float $jdUT): float {
	$year = 2000.0 + ($jdUT - 2451545.0) / 365.25;
	$table = [
		1800 => 13.7, 1820 => 11.9, 1840 => 5.7, 1860 => 7.9, 1880 => -5.4,
		1900 => -2.8, 1920 => 21.2, 1940 => 24.3, 1960 => 33.2, 1980 => 50.5,
		2000 => 63.8, 2020 => 69.4, 2040 => 72.0, 2060 => 75.0,
	];
	$years = array_keys($table);
	if ($year <= $years[0]) return $table[$years[0]];
	if ($year >= end($years)) return $table[end($years)];
	for ($i = 0; $i < count($years) - 1; $i++) {
		$y0 = $years[$i];
		$y1 = $years[$i + 1];
		if ($year <= $y1) {
			return $table[$y0] + ($table[$y1] - $table[$y0]) * ($year - $y0) / ($y1 - $y0);
		}
	}
	return 69.0;
}

/* Julian centuries of TT since J2000.0. */
function astroCenturiesTT(float $jdUT): float {
	$jdTT = $jdUT + astroDeltaT($jdUT) / 86400.0;
	return ($jdTT - 2451545.0) / 36525.0;
}

/* Nutation in longitude (degrees), Meeus ch. 22 low-precision form. */
function astroNutationLongitude(float $t): float {
	$omega = 125.04452 - 1934.136261 * $t;
	$lSun = 280.4665 + 36000.7698 * $t;
	$lMoon = 218.3165 + 481267.8813 * $t;
	$arcsec = -17.20 * astroSin($omega) - 1.32 * astroSin(2 * $lSun) - 0.23 * astroSin(2 * $lMoon) + 0.21 * astroSin(2 * $omega);
	return $arcsec / 3600.0;
}

/* Mean obliquity of the ecliptic (degrees), Meeus eq. 22.2. */
function astroObliquity(float $t): float {
	return 23.4392911 - (46.8150 * $t + 0.00059 * $t * $t - 0.001813 * $t * $t * $t) / 3600.0;
}

/* Apparent geocentric longitude of the Sun (degrees, equinox of date). */
function astroSunLongitude(float $t): float {
	$l0 = 280.46646 + 36000.76983 * $t + 0.0003032 * $t * $t;
	$m = 357.52911 + 35999.05029 * $t - 0.0001537 * $t * $t;
	$c = (1.914602 - 0.004817 * $t - 0.000014 * $t * $t) * astroSin($m)
		+ (0.019993 - 0.000101 * $t) * astroSin(2 * $m)
		+ 0.000289 * astroSin(3 * $m);
	$omega = 125.04 - 1934.136 * $t;
	return astroNorm360($l0 + $c - 0.00569 - 0.00478 * astroSin($omega));
}

/* Apparent geocentric longitude of the Moon (degrees, equinox of date). */
function astroMoonLongitude(float $t): float {
	$t2 = $t * $t;
	$t3 = $t2 * $t;
	$t4 = $t3 * $t;
	$lp = 218.3164477 + 481267.88123421 * $t - 0.0015786 * $t2 + $t3 / 538841 - $t4 / 65194000;
	$d  = 297.8501921 + 445267.1114034 * $t - 0.0018819 * $t2 + $t3 / 545868 - $t4 / 113065000;
	$m  = 357.5291092 + 35999.0502909 * $t - 0.0001536 * $t2 + $t3 / 24490000;
	$mp = 134.9633964 + 477198.8675055 * $t + 0.0087414 * $t2 + $t3 / 69699 - $t4 / 14712000;
	$f  = 93.2720950 + 483202.0175233 * $t - 0.0036539 * $t2 - $t3 / 3526000 + $t4 / 863310000;
	$a1 = 119.75 + 131.849 * $t;
	$a2 = 53.09 + 479264.290 * $t;
	$e  = 1 - 0.002516 * $t - 0.0000074 * $t2;

	$sum = 0.0;
	foreach (MOON_LONGITUDE_TERMS as [$cd, $cm, $cmp, $cf, $coef]) {
		$term = $coef * astroSin($cd * $d + $cm * $m + $cmp * $mp + $cf * $f);
		if (abs($cm) === 1) {
			$term *= $e;
		} elseif (abs($cm) === 2) {
			$term *= $e * $e;
		}
		$sum += $term;
	}
	$sum += 3958 * astroSin($a1) + 1962 * astroSin($lp - $f) + 318 * astroSin($a2);

	return astroNorm360($lp + $sum / 1000000.0 + astroNutationLongitude($t));
}

/* Heliocentric ecliptic coordinates (au, J2000 frame) from the JPL elements. */
function astroHeliocentric(string $body, float $t): array {
	$el = PLANET_ELEMENTS[$body];
	$a = $el[0][0] + $el[0][1] * $t;
	$e = $el[1][0] + $el[1][1] * $t;
	$inc = $el[2][0] + $el[2][1] * $t;
	$meanLong = $el[3][0] + $el[3][1] * $t;
	$periLong = $el[4][0] + $el[4][1] * $t;
	$node = $el[5][0] + $el[5][1] * $t;

	$argPeri = $periLong - $node;
	$meanAnomaly = fmod($meanLong - $periLong, 360.0);
	if ($meanAnomaly > 180) $meanAnomaly -= 360;
	if ($meanAnomaly < -180) $meanAnomaly += 360;

	// Solve Kepler's equation (degrees) by Newton iteration.
	$eDeg = rad2deg($e);
	$ecc = $meanAnomaly + $eDeg * astroSin($meanAnomaly);
	for ($i = 0; $i < 30; $i++) {
		$deltaM = $meanAnomaly - ($ecc - $eDeg * astroSin($ecc));
		$deltaE = $deltaM / (1 - $e * astroCos($ecc));
		$ecc += $deltaE;
		if (abs($deltaE) < 1e-9) break;
	}

	$xp = $a * (astroCos($ecc) - $e);
	$yp = $a * sqrt(1 - $e * $e) * astroSin($ecc);

	$cw = astroCos($argPeri); $sw = astroSin($argPeri);
	$cn = astroCos($node);    $sn = astroSin($node);
	$ci = astroCos($inc);     $si = astroSin($inc);

	return [
		($cw * $cn - $sw * $sn * $ci) * $xp + (-$sw * $cn - $cw * $sn * $ci) * $yp,
		($cw * $sn + $sw * $cn * $ci) * $xp + (-$sw * $sn + $cw * $cn * $ci) * $yp,
		($sw * $si) * $xp + ($cw * $si) * $yp,
	];
}

/* General precession in longitude from J2000 to the equinox of date (degrees). */
function astroPrecession(float $t): float {
	return (5029.0966 * $t + 1.11113 * $t * $t) / 3600.0;
}

/*
 * Heliocentric ecliptic coordinates (au, J2000 frame) of Jupiter or Saturn.
 *
 * The JPL table-1 elements leave out the large mutual perturbation of these
 * two planets (up to ~0.3°), so they use Paul Schlyter's elements and
 * perturbation series ("How to compute planetary positions"), which are
 * referred to the equinox of date and rotated back to J2000 here.
 */
function astroHeliocentricGiant(string $body, float $t): array {
	$d = $t * 36525.0 + 1.5; // days since 2000 Jan 0.0 TT
	$mj = astroNorm360(19.8950 + 0.0830853001 * $d);
	$ms = astroNorm360(316.9670 + 0.0334442282 * $d);

	if ($body === 'Jupiter') {
		$node = 100.4542 + 2.76854e-5 * $d;
		$inc = 1.3030 - 1.557e-7 * $d;
		$argPeri = 273.8777 + 1.64505e-5 * $d;
		$a = 5.20256;
		$e = 0.048498 + 4.469e-9 * $d;
		$meanAnomaly = $mj;
	} else {
		$node = 113.6634 + 2.38980e-5 * $d;
		$inc = 2.4886 - 1.081e-7 * $d;
		$argPeri = 339.3939 + 2.97661e-5 * $d;
		$a = 9.55475;
		$e = 0.055546 - 9.499e-9 * $d;
		$meanAnomaly = $ms;
	}

	$ecc = $meanAnomaly + rad2deg($e) * astroSin($meanAnomaly) * (1 + $e * astroCos($meanAnomaly));
	for ($i = 0; $i < 30; $i++) {
		$deltaE = ($ecc - rad2deg($e) * astroSin($ecc) - $meanAnomaly) / (1 - $e * astroCos($ecc));
		$ecc -= $deltaE;
		if (abs($deltaE) < 1e-9) break;
	}
	$xv = $a * (astroCos($ecc) - $e);
	$yv = $a * sqrt(1 - $e * $e) * astroSin($ecc);
	$v = rad2deg(atan2($yv, $xv));
	$r = sqrt($xv * $xv + $yv * $yv);

	$xh = $r * (astroCos($node) * astroCos($v + $argPeri) - astroSin($node) * astroSin($v + $argPeri) * astroCos($inc));
	$yh = $r * (astroSin($node) * astroCos($v + $argPeri) + astroCos($node) * astroSin($v + $argPeri) * astroCos($inc));
	$zh = $r * astroSin($v + $argPeri) * astroSin($inc);
	$lon = rad2deg(atan2($yh, $xh));
	$lat = rad2deg(atan2($zh, sqrt($xh * $xh + $yh * $yh)));

	if ($body === 'Jupiter') {
		$lon += -0.332 * astroSin(2 * $mj - 5 * $ms - 67.6)
			- 0.056 * astroSin(2 * $mj - 2 * $ms + 21)
			+ 0.042 * astroSin(3 * $mj - 5 * $ms + 21)
			- 0.036 * astroSin($mj - 2 * $ms)
			+ 0.022 * astroCos($mj - $ms)
			+ 0.023 * astroSin(2 * $mj - 3 * $ms + 52)
			- 0.016 * astroSin($mj - 5 * $ms - 69);
	} else {
		$lon += 0.812 * astroSin(2 * $mj - 5 * $ms - 67.6)
			- 0.229 * astroCos(2 * $mj - 4 * $ms - 2)
			+ 0.119 * astroSin($mj - 2 * $ms - 3)
			+ 0.046 * astroSin(2 * $mj - 6 * $ms - 69)
			+ 0.014 * astroSin($mj - 3 * $ms + 32);
		$lat += -0.020 * astroCos(2 * $mj - 4 * $ms - 2)
			+ 0.018 * astroSin(2 * $mj - 6 * $ms - 49);
	}

	// Equinox of date -> J2000, to match the Earth position.
	$lon -= astroPrecession($t);
	return [
		$r * astroCos($lat) * astroCos($lon),
		$r * astroCos($lat) * astroSin($lon),
		$r * astroSin($lat),
	];
}

/* Apparent geocentric longitude of a planet (degrees, equinox of date). */
function astroPlanetLongitude(string $body, float $t): float {
	$helio = ($body === 'Jupiter' || $body === 'Saturn') ? 'astroHeliocentricGiant' : 'astroHeliocentric';
	$earth = astroHeliocentric('EMBary', $t);

	// Correct for light travel time (one iteration is plenty).
	$lightDaysPerAu = 0.0057755183;
	$planet = $helio($body, $t);
	$dist = sqrt(($planet[0] - $earth[0]) ** 2 + ($planet[1] - $earth[1]) ** 2 + ($planet[2] - $earth[2]) ** 2);
	$planet = $helio($body, $t - $dist * $lightDaysPerAu / 36525.0);

	$lonJ2000 = rad2deg(atan2($planet[1] - $earth[1], $planet[0] - $earth[0]));

	// Precess from the J2000 equinox to the equinox of date, then add nutation.
	return astroNorm360($lonJ2000 + astroPrecession($t) + astroNutationLongitude($t));
}

/* Ascendant (degrees) for a moment and a place (latitude north +, longitude east +). */
function astroAscendant(float $jdUT, float $latitude, float $longitude): float {
	$t = astroCenturiesTT($jdUT);
	$tUT = ($jdUT - 2451545.0) / 36525.0;
	$gmst = 280.46061837 + 360.98564736629 * ($jdUT - 2451545.0) + 0.000387933 * $tUT * $tUT - $tUT * $tUT * $tUT / 38710000.0;
	$obliquity = astroObliquity($t);
	// Apparent sidereal time includes the equation of the equinoxes.
	$ramc = astroNorm360($gmst + astroNutationLongitude($t) * astroCos($obliquity) + $longitude);

	$y = astroCos($ramc);
	$x = -(astroSin($ramc) * astroCos($obliquity) + tan(deg2rad($latitude)) * astroSin($obliquity));
	return astroNorm360(rad2deg(atan2($y, $x)));
}

/* Longitude of one body at a UTC moment. */
function astroBodyLongitude(string $body, DateTimeInterface $utc, ?float $lat = null, ?float $lng = null): float {
	$jd = astroJulianDay($utc);
	$t = astroCenturiesTT($jd);
	switch ($body) {
		case 'Sun':
			return astroSunLongitude($t);
		case 'Moon':
			return astroMoonLongitude($t);
		case 'Ascendant':
			return astroAscendant($jd, $lat, $lng);
		default:
			return astroPlanetLongitude($body, $t);
	}
}

/* Sign name for an ecliptic longitude. */
function astroSignOf(float $longitude): string {
	return ZODIAC_SIGNS[(int) floor(astroNorm360($longitude) / 30.0) % 12];
}

/*
 * Placements this close to the edge of a sign are "on the cusp". The margin
 * is larger than the worst error of these formulas (checked against the
 * PyEphem ephemeris), so a placement is never reported in the wrong sign
 * without this flag.
 */
const ASTRO_CUSP_MARGIN = 0.1;

/* The neighbouring sign if the longitude is on a cusp, else null. */
function astroCuspNeighbor(float $longitude): ?string {
	$degree = fmod(astroNorm360($longitude), 30.0);
	if ($degree < ASTRO_CUSP_MARGIN) return astroSignOf($longitude - 1.0);
	if ($degree > 30.0 - ASTRO_CUSP_MARGIN) return astroSignOf($longitude + 1.0);
	return null;
}

/*
 * The earliest moment showing the same local clock time as $local. When the
 * clocks go back, a local time happens twice and PHP picks one by a rule of
 * its own; this always gives the first. Other moments are returned unchanged.
 */
function astroFirstOccurrence(DateTimeImmutable $local): DateTimeImmutable {
	$clock = $local->format('Y-m-d H:i:s');
	$clockAsUtc = (new DateTimeImmutable($clock, new DateTimeZone('UTC')))->getTimestamp();
	$around = $local->getTimestamp();
	$first = $local;
	foreach ($local->getTimezone()->getTransitions($around - 86400, $around + 86400) ?: [] as $transition) {
		$candidate = $local->setTimestamp($clockAsUtc - $transition['offset']);
		if ($candidate < $first && $candidate->format('Y-m-d H:i:s') === $clock) $first = $candidate;
	}
	return $first;
}

/*
 * Build a natal chart.
 *
 * $birthDate  'YYYY-MM-DD'
 * $birthTime  'HH:MM' or 'HH:MM:SS' local time, or null when unknown
 * $lat/$lng   birth place coordinates, or null when unknown
 * $timezone   IANA zone of the birth place (e.g. 'America/New_York'), or null
 *
 * A birth date that is malformed or not a real date gives no chart. A birth
 * time that is malformed or not a real clock time (00:00 to 23:59:59) is
 * treated as unknown, as is an unrecognised time zone.
 *
 * A birth time skipped when the clocks go forward moves forward by the gap;
 * one repeated when they go back is read as its first occurrence.
 *
 * When the time or place is unknown the chart is computed for the middle of
 * the possible window, and any body whose sign could differ inside that
 * window is flagged as 'uncertain'. The Ascendant needs both time and place.
 */
function astroBuildChart(string $birthDate, ?string $birthTime, ?float $lat, ?float $lng, ?string $timezone): ?array {
	if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $birthDate, $date)) return null;
	if (!checkdate((int) $date[2], (int) $date[3], (int) $date[1])) return null;

	$timeKnown = $birthTime !== null && preg_match('/^([01]?\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $birthTime) === 1;
	$zone = null;
	if ($timezone !== null && $timezone !== '') {
		try {
			$zone = new DateTimeZone($timezone);
		} catch (Exception $e) {
			$zone = null;
		}
	}
	$tzKnown = $zone !== null;
	$placeKnown = $lat !== null && $lng !== null && $tzKnown;

	$zone = $zone ?? new DateTimeZone('UTC');
	$utc = new DateTimeZone('UTC');

	try {
		if ($timeKnown) {
			$local = astroFirstOccurrence(new DateTimeImmutable($birthDate . ' ' . $birthTime, $zone));
			$start = $end = $local;
		} else {
			$start = new DateTimeImmutable($birthDate . ' 00:00:00', $zone);
			$end = new DateTimeImmutable($birthDate . ' 23:59:59', $zone);
			$local = new DateTimeImmutable($birthDate . ' 12:00:00', $zone);
		}
	} catch (Exception $e) {
		return null;
	}

	// Without a timezone the local clock time could be anywhere from UTC-12 to UTC+14.
	if (!$tzKnown) {
		$start = $start->modify('-14 hours');
		$end = $end->modify('+12 hours');
	}

	$moment = $local->setTimezone($utc);
	$start = $start->setTimezone($utc);
	$end = $end->setTimezone($utc);

	$year = (int) $moment->format('Y');
	if ($year < ASTRO_MIN_YEAR || $year > ASTRO_MAX_YEAR) return null;

	$placements = [];
	foreach (CHART_BODIES as $body) {
		if ($body === 'Ascendant' && !($timeKnown && $placeKnown)) continue;

		$longitude = astroBodyLongitude($body, $moment, $lat, $lng);
		$uncertain = false;
		if ($start != $end) {
			$uncertain = astroSignOf(astroBodyLongitude($body, $start, $lat, $lng)) !== astroSignOf($longitude)
				|| astroSignOf(astroBodyLongitude($body, $end, $lat, $lng)) !== astroSignOf($longitude);
		}

		$placements[$body] = [
			'longitude' => $longitude,
			'sign' => astroSignOf($longitude),
			'degree' => fmod($longitude, 30.0),
			'uncertain' => $uncertain,
			'cuspWith' => astroCuspNeighbor($longitude),
		];
	}

	return [
		'placements' => $placements,
		'timeKnown' => $timeKnown,
		'placeKnown' => $placeKnown,
		'momentUtc' => $moment,
	];
}

/* Sign the Moon is passing through right now (used for the daily guide). */
function astroCurrentMoonSign(?DateTimeInterface $now = null): string {
	$now = $now ?? new DateTimeImmutable('now', new DateTimeZone('UTC'));
	return astroSignOf(astroBodyLongitude('Moon', $now));
}
