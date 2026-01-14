<?php
/*
 * Natal chart wheel, drawn as inline SVG.
 *
 * Signs run counter-clockwise. When the Ascendant is known it sits at the
 * left (9 o'clock), as on a traditional chart; otherwise 0° Aries does.
 */

require_once __DIR__ . '/guide.inc.php';

const WHEEL_SIZE = 440;
const WHEEL_ELEMENT_FILLS = ['Fire' => '#f7dcd2', 'Earth' => '#dfead3', 'Air' => '#ebe3f5', 'Water' => '#d6e5f2'];

function wheelPoint(float $radius, float $longitude, float $base): array {
	$c = WHEEL_SIZE / 2;
	$theta = deg2rad(180 + $longitude - $base);
	return [round($c + $radius * cos($theta), 2), round($c - $radius * sin($theta), 2)];
}

/* Spread glyph positions so neighbouring planets do not overlap. */
function wheelSpread(array $longitudes, float $minGap = 9.0): array {
	asort($longitudes);
	$names = array_keys($longitudes);
	$pos = array_values($longitudes);
	$n = count($pos);
	if ($n < 2) return $longitudes;
	for ($pass = 0; $pass < 50; $pass++) {
		$moved = false;
		for ($i = 0; $i < $n; $i++) {
			$j = ($i + 1) % $n;
			$gap = $pos[$j] - $pos[$i];
			if ($j === 0) $gap += 360;
			if ($gap < $minGap) {
				$push = ($minGap - $gap) / 2;
				$pos[$i] -= $push;
				$pos[$j] += $push;
				$moved = true;
			}
		}
		if (!$moved) break;
	}
	return array_combine($names, $pos);
}

function renderChartWheel(array $chart): string {
	$size = WHEEL_SIZE;
	$c = $size / 2;
	$rOuter = 210;
	$rSigns = 172;
	$rPlanets = 136;
	$rInner = 96;
	$placements = $chart['placements'];
	$base = isset($placements['Ascendant']) ? $placements['Ascendant']['longitude'] : 0.0;
	$font = "'Apple Symbols','Segoe UI Symbol','Noto Sans Symbols 2','DejaVu Sans',sans-serif";

	$svg = '<svg class="chart-wheel" viewBox="0 0 ' . $size . ' ' . $size . '" role="img" aria-labelledby="wheel-title">';
	$svg .= '<title id="wheel-title">Your birth chart</title>';
	// Light face for the whole wheel, so it reads on dark backgrounds too.
	$svg .= '<circle cx="' . $c . '" cy="' . $c . '" r="' . $rOuter . '" fill="#fbfbfb"/>';

	// Sign ring.
	foreach (ZODIAC_SIGNS as $i => $sign) {
		$start = $i * 30;
		$end = $start + 30;
		[$x1, $y1] = wheelPoint($rOuter, $start, $base);
		[$x2, $y2] = wheelPoint($rOuter, $end, $base);
		[$x3, $y3] = wheelPoint($rSigns, $end, $base);
		[$x4, $y4] = wheelPoint($rSigns, $start, $base);
		$fill = WHEEL_ELEMENT_FILLS[SIGN_INFO[$sign]['element']];
		$svg .= '<path d="M' . $x1 . ' ' . $y1 . ' A' . $rOuter . ' ' . $rOuter . ' 0 0 0 ' . $x2 . ' ' . $y2
			. ' L' . $x3 . ' ' . $y3 . ' A' . $rSigns . ' ' . $rSigns . ' 0 0 1 ' . $x4 . ' ' . $y4 . ' Z" fill="' . $fill . '" stroke="#8462A3" stroke-width="1"><title>' . $sign . '</title></path>';
		[$gx, $gy] = wheelPoint(($rOuter + $rSigns) / 2, $start + 15, $base);
		$svg .= '<text x="' . $gx . '" y="' . $gy . '" font-size="20" fill="#112637" text-anchor="middle" dominant-baseline="central" font-family="' . $font . '">' . textGlyph(SIGN_INFO[$sign]['glyph']) . '</text>';
	}

	// Degree ticks every 10°.
	for ($d = 0; $d < 360; $d += 10) {
		if ($d % 30 === 0) continue;
		[$x1, $y1] = wheelPoint($rSigns, $d, $base);
		[$x2, $y2] = wheelPoint($rSigns - 6, $d, $base);
		$svg .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="#8462A3" stroke-width="1"/>';
	}

	$svg .= '<circle cx="' . $c . '" cy="' . $c . '" r="' . $rInner . '" fill="#f4f0f9" stroke="#8462A3" stroke-width="1"/>';

	// Horizon line through the Ascendant.
	if (isset($placements['Ascendant'])) {
		[$ax, $ay] = wheelPoint($rSigns, $base, $base);
		[$dx, $dy] = wheelPoint($rSigns, $base + 180, $base);
		$svg .= '<line x1="' . $ax . '" y1="' . $ay . '" x2="' . $dx . '" y2="' . $dy . '" stroke="#1E3B6E" stroke-width="1.5" stroke-dasharray="4 4"/>';
		$svg .= '<text x="' . ($ax + 6) . '" y="' . ($ay - 6) . '" font-size="12" font-weight="600" fill="#1E3B6E" font-family="sans-serif">AC</text>';
	}

	// Planets.
	$longitudes = [];
	foreach ($placements as $body => $p) {
		if ($body !== 'Ascendant') $longitudes[$body] = $p['longitude'];
	}
	foreach (wheelSpread($longitudes) as $body => $shown) {
		$p = $placements[$body];
		[$tx1, $ty1] = wheelPoint($rSigns, $p['longitude'], $base);
		[$tx2, $ty2] = wheelPoint($rSigns - 12, $p['longitude'], $base);
		[$lx, $ly] = wheelPoint($rPlanets + 14, $shown, $base);
		[$gx, $gy] = wheelPoint($rPlanets, $shown, $base);
		$label = $body . ' ' . formatDegree($p['degree']) . ' ' . $p['sign'];
		$svg .= '<g><title>' . htmlspecialchars($label, ENT_QUOTES) . '</title>'
			. '<line x1="' . $tx1 . '" y1="' . $ty1 . '" x2="' . $tx2 . '" y2="' . $ty2 . '" stroke="#112637" stroke-width="2"/>'
			. '<line x1="' . $tx2 . '" y1="' . $ty2 . '" x2="' . $lx . '" y2="' . $ly . '" stroke="#B2A0DA" stroke-width="1"/>'
			. '<text x="' . $gx . '" y="' . $gy . '" font-size="20" fill="#8462A3" text-anchor="middle" dominant-baseline="central" font-family="' . $font . '">' . textGlyph(BODY_INFO[$body]['glyph']) . '</text></g>';
	}

	$svg .= '</svg>';
	return $svg;
}
