<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Every Body in a Chart agrees with the Reference ephemeris (PyEphem) to
 * within the On-the-cusp margin, so a Placement is never shown in the wrong
 * Sign without the On-the-cusp note.
 *
 * Cases come from tests/reference/positions.json, written by
 * tests/reference/generate_positions.py; see that script for how the
 * expected positions are produced and how to regenerate them.
 */
final class AccuracyTest extends TestCase {
	private const FIXTURE = __DIR__ . '/reference/positions.json';

	public static function referenceCases(): iterable {
		$fixture = json_decode(file_get_contents(self::FIXTURE), true, flags: JSON_THROW_ON_ERROR);
		foreach ($fixture['cases'] as $case) {
			$in = $case['input'];
			$label = sprintf('%s: %s %s %s (%s, %s)', $case['name'], $in['date'], $in['time'], $in['timezone'], $in['latitude'], $in['longitude']);
			yield $label => [$in, $case['expected']];
		}
	}

	#[DataProvider('referenceCases')]
	public function testEveryBodyIsWithinTheCuspMarginOfTheReferenceEphemeris(array $input, array $expected): void {
		$chart = astroBuildChart($input['date'], $input['time'], $input['latitude'], $input['longitude'], $input['timezone']);

		$this->assertNotNull($chart, 'No Chart was built');
		$this->assertSame($expected['utc'], $chart['momentUtc']->format('Y-m-d\TH:i:s\Z'), 'UTC moment');

		$misses = [];
		foreach ($expected['longitudes'] as $body => $expectedLongitude) {
			$actual = $chart['placements'][$body]['longitude'];
			$error = self::angularDistance($actual, $expectedLongitude);
			if ($error > ASTRO_CUSP_MARGIN) {
				$misses[] = sprintf('%s: expected %.6f°, got %.6f°, off by %.6f°', $body, $expectedLongitude, $actual, $error);
			}
		}
		$this->assertSame([], $misses, sprintf('Bodies more than %s° from the Reference ephemeris', ASTRO_CUSP_MARGIN));
	}

	/** Shortest distance between two longitudes in [0°, 360°), in degrees (0 to 180). */
	private static function angularDistance(float $a, float $b): float {
		return abs(fmod($a - $b + 540.0, 360.0) - 180.0);
	}
}
