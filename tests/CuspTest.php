<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CuspTest extends TestCase {
	/** Small enough to sit right against the margin, large enough to survive floating point. */
	private const NUDGE = 1e-9;

	public static function anglesAroundBoundaries(): array {
		$inside = ASTRO_CUSP_MARGIN - self::NUDGE;
		$outside = ASTRO_CUSP_MARGIN + self::NUDGE;

		return [
			// Aries–Taurus boundary at 30°.
			'end of Aries, inside the margin'      => [30.0 - $inside, 'Taurus'],
			'end of Aries, outside the margin'     => [30.0 - $outside, null],
			'start of Taurus, inside the margin'   => [30.0 + $inside, 'Aries'],
			'start of Taurus, outside the margin'  => [30.0 + $outside, null],

			// Pisces–Aries wrap at 0° / 360°.
			'end of Pisces, inside the margin'     => [360.0 - $inside, 'Aries'],
			'end of Pisces, outside the margin'    => [360.0 - $outside, null],
			'start of Aries, inside the margin'    => [0.0 + $inside, 'Pisces'],
			'start of Aries, outside the margin'   => [0.0 + $outside, null],
			'exactly 0° is on the Pisces cusp'     => [0.0, 'Pisces'],
			'negative angle inside the margin'     => [-$inside, 'Aries'],
			'negative angle outside the margin'    => [-$outside, null],

			// Well clear of any boundary.
			'middle of a Sign'                     => [45.0, null],
		];
	}

	#[DataProvider('anglesAroundBoundaries')]
	public function testCuspNeighbourOfAngle(float $longitude, ?string $expectedNeighbour): void {
		$this->assertSame($expectedNeighbour, astroCuspNeighbor($longitude));
	}
}
