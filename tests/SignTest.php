<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SignTest extends TestCase {
	/** Small enough to sit right against a boundary, large enough to survive floating point. */
	private const NUDGE = 1e-9;

	public static function boundaryAngles(): array {
		return [
			'0° is the start of Aries'            => [0.0, 'Aries'],
			'just under 30° is still Aries'       => [30.0 - self::NUDGE, 'Aries'],
			'exactly 30° is the start of Taurus'  => [30.0, 'Taurus'],
			'exactly 330° is the start of Pisces' => [330.0, 'Pisces'],
			'just under 360° is still Pisces'     => [360.0 - self::NUDGE, 'Pisces'],
			'360° wraps round to Aries'           => [360.0, 'Aries'],
			'over a full turn wraps round'        => [400.0, 'Taurus'],
			'just below 0° is Pisces'             => [-self::NUDGE, 'Pisces'],
			'-30° is the start of Pisces'         => [-30.0, 'Pisces'],
			'-45° is Aquarius'                    => [-45.0, 'Aquarius'],
			'-360° wraps round to Aries'          => [-360.0, 'Aries'],
		];
	}

	#[DataProvider('boundaryAngles')]
	public function testSignOfAngle(float $longitude, string $expectedSign): void {
		$this->assertSame($expectedSign, astroSignOf($longitude));
	}
}
