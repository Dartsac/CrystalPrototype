<?php
/*
 * Birth place lookup.
 *
 * Uses the free Open-Meteo geocoding API (no API key needed), which returns
 * coordinates and the IANA timezone of a place. The timezone is what lets us
 * turn a local birth time into an exact moment, including historical
 * daylight saving rules.
 */

const GEOCODE_URL = 'https://geocoding-api.open-meteo.com/v1/search';

/* Valid IANA timezone (or alias) that PHP knows about. */
function isValidTimezone(?string $tz): bool {
	if ($tz === null || $tz === '' || strlen($tz) > 64 || !preg_match('#^[A-Za-z_]+(/[A-Za-z0-9_+\-]+)*$#', $tz)) {
		return false;
	}
	try {
		new DateTimeZone($tz);
		return true;
	} catch (Exception $e) {
		return false;
	}
}

/* "Atlanta, Georgia, United States" from an Open-Meteo result. */
function geocodeLabel(array $result): string {
	$parts = [$result['name']];
	if (!empty($result['admin1']) && $result['admin1'] !== $result['name']) $parts[] = $result['admin1'];
	if (!empty($result['country'])) $parts[] = $result['country'];
	return implode(', ', $parts);
}

function geocodeFetch(string $name): array {
	$url = GEOCODE_URL . '?' . http_build_query(['name' => $name, 'count' => 10, 'language' => 'en', 'format' => 'json']);
	$ch = curl_init($url);
	curl_setopt_array($ch, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 6,
		CURLOPT_CONNECTTIMEOUT => 4,
		CURLOPT_USERAGENT => 'Crystal/1.0',
	]);
	$body = curl_exec($ch);
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($body === false || $status !== 200) return [];
	$data = json_decode($body, true);
	return is_array($data) && isset($data['results']) && is_array($data['results']) ? $data['results'] : [];
}

/*
 * Look up a place typed by the user. Returns
 * ['place' => label, 'lat' => float, 'lng' => float, 'tz' => string] or null.
 */
function geocodePlace(string $query): ?array {
	$query = trim(preg_replace('/\s+/', ' ', $query));
	if ($query === '' || mb_strlen($query) > 200) return null;

	$segments = array_values(array_filter(array_map('trim', explode(',', $query)), 'strlen'));
	if (!$segments) return null;

	$results = geocodeFetch($query);
	if (!$results && count($segments) > 1) {
		// Search by city name, then keep results whose region or country matches the rest.
		$qualifiers = array_map('mb_strtolower', array_slice($segments, 1));
		foreach (geocodeFetch($segments[0]) as $candidate) {
			$haystack = mb_strtolower(implode(' ', array_filter([
				$candidate['admin1'] ?? '', $candidate['admin2'] ?? '', $candidate['country'] ?? '', $candidate['country_code'] ?? '',
			])));
			$matches = true;
			foreach ($qualifiers as $q) {
				if (mb_strpos($haystack, $q) === false) {
					$matches = false;
					break;
				}
			}
			if ($matches) $results[] = $candidate;
		}
	}

	foreach ($results as $r) {
		if (isset($r['latitude'], $r['longitude'], $r['timezone'], $r['name']) && isValidTimezone($r['timezone'])) {
			return [
				'place' => geocodeLabel($r),
				'lat' => (float) $r['latitude'],
				'lng' => (float) $r['longitude'],
				'tz' => $r['timezone'],
			];
		}
	}
	return null;
}
