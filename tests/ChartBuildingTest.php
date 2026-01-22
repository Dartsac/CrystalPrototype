<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The rules that decide what a Chart contains, tested through astroBuildChart,
 * the same entry point the pages use.
 *
 * Sign changes used below are published equinox and solstice times (UTC):
 *   2000-03-20 07:35  Sun enters Aries
 *   2003-09-23 10:47  Sun enters Libra
 *   2006-06-21 12:26  Sun enters Cancer
 */
final class ChartBuildingTest extends TestCase {
	private const LONDON_LAT = 51.5074;
	private const LONDON_LNG = -0.1278;

	private static function utc(string $moment): DateTimeImmutable {
		return new DateTimeImmutable($moment, new DateTimeZone('UTC'));
	}

	/* Ascendant */

	public function testAChartWithFullBirthDetailsHasAnAscendant(): void {
		$chart = astroBuildChart('2000-07-04', '12:00', self::LONDON_LAT, self::LONDON_LNG, 'Europe/London');

		$this->assertArrayHasKey('Ascendant', $chart['placements']);
	}

	public function testWithoutABirthTimeTheChartHasNoAscendant(): void {
		$chart = astroBuildChart('2000-07-04', null, self::LONDON_LAT, self::LONDON_LNG, 'Europe/London');

		$this->assertArrayHasKey('Sun', $chart['placements']);
		$this->assertArrayNotHasKey('Ascendant', $chart['placements']);
	}

	public function testWithoutABirthPlaceTheChartHasNoAscendant(): void {
		$chart = astroBuildChart('2000-07-04', '12:00', null, null, 'Europe/London');

		$this->assertArrayHasKey('Sun', $chart['placements']);
		$this->assertArrayNotHasKey('Ascendant', $chart['placements']);
	}

	/* Uncertain placements */

	public function testWithoutABirthTimeABodyThatChangesSignDuringTheDateIsUncertain(): void {
		$chart = astroBuildChart('2000-03-20', null, null, null, 'UTC');

		$this->assertTrue($chart['placements']['Sun']['uncertain']);
	}

	public function testWithoutABirthTimeABodyThatStaysInOneSignAllDayIsNotUncertain(): void {
		// Mid-Cancer: the Sun is about 12° from either edge of its Sign.
		$chart = astroBuildChart('2000-07-04', null, null, null, 'UTC');

		$this->assertSame('Cancer', $chart['placements']['Sun']['sign']);
		$this->assertFalse($chart['placements']['Sun']['uncertain']);
	}

	public static function signChangesJustOutsideTheLocalDate(): array {
		return [
			// Only reachable if the window runs to 23:59 at UTC−12, i.e. 11:59 UTC next day.
			'10h47m after the UTC day ends'    => ['2003-09-22'],
			// Only reachable if the window starts at 00:00 at UTC+14, i.e. 10:00 UTC the day before.
			'11h34m before the UTC day starts' => ['2006-06-22'],
		];
	}

	#[DataProvider('signChangesJustOutsideTheLocalDate')]
	public function testWithoutATimeZoneTheWindowCoversEveryUtcOffset(string $birthDate): void {
		$withZone = astroBuildChart($birthDate, null, null, null, 'UTC');
		$withoutZone = astroBuildChart($birthDate, null, null, null, null);

		$this->assertFalse($withZone['placements']['Sun']['uncertain'], 'The Sign change is outside the UTC date itself');
		$this->assertTrue($withoutZone['placements']['Sun']['uncertain'], 'Without a time zone the Sign change is inside the window');
	}

	public function testWithFullBirthDetailsNoPlacementIsUncertain(): void {
		// Five minutes before the Sun enters Aries: On the cusp, but not uncertain.
		$chart = astroBuildChart('2000-03-20', '07:30', self::LONDON_LAT, self::LONDON_LNG, 'Europe/London');

		$this->assertCount(count(CHART_BODIES), $chart['placements']);
		foreach ($chart['placements'] as $body => $placement) {
			$this->assertFalse($placement['uncertain'], "$body should not be uncertain");
		}
	}

	/* Supported range */

	public static function birthMomentsAroundTheSupportedRange(): array {
		return [
			'last moment before 1800 (UTC)'   => ['1799-12-31', '23:59', 'UTC', false],
			'first moment of 1800 (UTC)'      => ['1800-01-01', '00:00', 'UTC', true],
			'last moment of 2050 (UTC)'       => ['2050-12-31', '23:59', 'UTC', true],
			'first moment after 2050 (UTC)'   => ['2051-01-01', '00:00', 'UTC', false],

			// The limit is judged on the UTC moment, not the local date.
			'local 1800, but UTC is 1799'     => ['1800-01-01', '05:00', 'Asia/Tokyo', false],
			'local 1799, but UTC is 1800'     => ['1799-12-31', '22:00', 'America/New_York', true],
			'local 2050, but UTC is 2051'     => ['2050-12-31', '20:00', 'America/New_York', false],
			'local 2051, but UTC is 2050'     => ['2051-01-01', '05:00', 'Asia/Tokyo', true],
		];
	}

	#[DataProvider('birthMomentsAroundTheSupportedRange')]
	public function testOnlyBirthMomentsFrom1800To2050GiveAChart(string $date, string $time, string $zone, bool $givesChart): void {
		$chart = astroBuildChart($date, $time, self::LONDON_LAT, self::LONDON_LNG, $zone);

		$this->assertSame($givesChart, $chart !== null);
	}

	/* Bad input */

	public static function malformedBirthDates(): array {
		return [
			'missing leading zeros' => ['2000-3-4'],
			'day first'             => ['04/03/2000'],
			'words'                 => ['yesterday'],
			'empty'                 => [''],
		];
	}

	#[DataProvider('malformedBirthDates')]
	public function testAMalformedBirthDateGivesNoChart(string $birthDate): void {
		$this->assertNull(astroBuildChart($birthDate, '12:00', self::LONDON_LAT, self::LONDON_LNG, 'Europe/London'));
	}

	public static function malformedBirthTimes(): array {
		return [
			'12-hour clock' => ['7pm'],
			'no colon'      => ['1230'],
			'words'         => ['noon'],
			'empty'         => [''],
		];
	}

	#[DataProvider('malformedBirthTimes')]
	public function testAMalformedBirthTimeIsTreatedAsUnknown(string $birthTime): void {
		$unknownTime = astroBuildChart('2000-03-20', null, self::LONDON_LAT, self::LONDON_LNG, 'Europe/London');
		$chart = astroBuildChart('2000-03-20', $birthTime, self::LONDON_LAT, self::LONDON_LNG, 'Europe/London');

		$this->assertFalse($chart['timeKnown']);
		$this->assertEquals($unknownTime, $chart);
	}

	public static function unrecognisedTimeZones(): array {
		return [
			'made-up zone' => ['Mars/Olympus_Mons'],
			'words'        => ['not a zone'],
			'empty'        => [''],
		];
	}

	#[DataProvider('unrecognisedTimeZones')]
	public function testAnUnrecognisedTimeZoneIsTreatedAsMissing(string $timezone): void {
		$missingZone = astroBuildChart('2000-03-20', '12:00', self::LONDON_LAT, self::LONDON_LNG, null);
		$chart = astroBuildChart('2000-03-20', '12:00', self::LONDON_LAT, self::LONDON_LNG, $timezone);

		$this->assertFalse($chart['placeKnown']);
		$this->assertEquals($missingZone, $chart);
	}

	/* Local birth time to UTC */

	public static function knownBirths(): array {
		return [
			// Apollo 11 landed at 20:17 UTC, 16:17 Eastern Daylight Time.
			'northern summer time' => ['1969-07-20', '16:17', 'America/New_York', '1969-07-20 20:17:00'],
			// Sydney is on daylight saving time (UTC+11) in January.
			'southern summer time' => ['2000-01-01', '00:00', 'Australia/Sydney', '1999-12-31 13:00:00'],
			'with seconds'         => ['2000-07-04', '12:34:56', 'UTC', '2000-07-04 12:34:56'],
		];
	}

	#[DataProvider('knownBirths')]
	public function testTheChartsUtcMomentIsCorrectForAKnownBirth(string $date, string $time, string $zone, string $expectedUtc): void {
		$chart = astroBuildChart($date, $time, self::LONDON_LAT, self::LONDON_LNG, $zone);

		$this->assertEquals(self::utc($expectedUtc), $chart['momentUtc']);
	}

	public static function skippedTimes(): array {
		return [
			// Clocks went 02:00 → 03:00 EST→EDT. 02:30 doesn't exist and moves forward to 03:30 EDT (UTC−4).
			'New York, skipped hour' => ['2021-03-14', '02:30', 'America/New_York', '2021-03-14 07:30:00'],
			// Clocks went 02:00 → 03:00 AEST→AEDT. 02:30 doesn't exist and moves forward to 03:30 AEDT (UTC+11).
			'Sydney, skipped hour'   => ['2021-10-03', '02:30', 'Australia/Sydney', '2021-10-02 16:30:00'],
		];
	}

	#[DataProvider('skippedTimes')]
	public function testABirthTimeSkippedByADaylightSavingChangeMovesForward(string $date, string $time, string $zone, string $expectedUtc): void {
		$chart = astroBuildChart($date, $time, self::LONDON_LAT, self::LONDON_LNG, $zone);

		$this->assertEquals(self::utc($expectedUtc), $chart['momentUtc']);
	}

	public static function repeatedTimes(): array {
		return [
			// Clocks went 02:00 → 01:00 EDT→EST. 01:30 happens twice; the first, EDT (UTC−4), is used.
			'New York, repeated hour' => ['2021-11-07', '01:30', 'America/New_York', '2021-11-07 05:30:00'],
			// Clocks went 03:00 → 02:00 AEDT→AEST. 02:30 happens twice; the first, AEDT (UTC+11), is used.
			'Sydney, repeated hour'   => ['2021-04-04', '02:30', 'Australia/Sydney', '2021-04-03 15:30:00'],
			// Clocks went 02:00 → 01:00 BST→GMT. 01:30 happens twice; the first, BST (UTC+1), is used.
			'London, repeated hour'   => ['2021-10-31', '01:30', 'Europe/London', '2021-10-31 00:30:00'],
			// Clocks went 02:00 → 01:30 (UTC+11 → UTC+10:30). 01:45 happens twice; the first, UTC+11, is used.
			'Lord Howe Island, repeated half hour' => ['2021-04-04', '01:45', 'Australia/Lord_Howe', '2021-04-03 14:45:00'],
		];
	}

	#[DataProvider('repeatedTimes')]
	public function testABirthTimeRepeatedByADaylightSavingChangeUsesTheFirstOccurrence(string $date, string $time, string $zone, string $expectedUtc): void {
		$chart = astroBuildChart($date, $time, self::LONDON_LAT, self::LONDON_LNG, $zone);

		$this->assertEquals(self::utc($expectedUtc), $chart['momentUtc']);
	}

	/* Placements */

	public static function charts(): array {
		return [
			'full birth details'                 => ['2000-07-04', '12:00', self::LONDON_LAT, self::LONDON_LNG, 'Europe/London'],
			'Sun just short of 360°'             => ['2000-03-20', '07:30', self::LONDON_LAT, self::LONDON_LNG, 'Europe/London'],
			'no birth time, place or time zone'  => ['1850-11-02', null, null, null, null],
			'southern hemisphere'                => ['2049-02-14', '18:45', -33.8688, 151.2093, 'Australia/Sydney'],
		];
	}

	#[DataProvider('charts')]
	public function testEachPlacementsSignAndDegreeAgreeWithItsLongitude(string $date, ?string $time, ?float $lat, ?float $lng, ?string $zone): void {
		$chart = astroBuildChart($date, $time, $lat, $lng, $zone);

		foreach ($chart['placements'] as $body => $placement) {
			$this->assertContains($placement['sign'], ZODIAC_SIGNS, $body);
			$signStart = 30.0 * array_search($placement['sign'], ZODIAC_SIGNS, true);
			$this->assertGreaterThanOrEqual(0.0, $placement['degree'], $body);
			$this->assertLessThan(30.0, $placement['degree'], $body);
			$this->assertEqualsWithDelta($placement['longitude'], $signStart + $placement['degree'], 1e-9, $body);
		}
	}
}
