<?php
/*
 * Crystal shop catalog and individualized recommendations.
 *
 * Each crystal lists the signs it is traditionally associated with and the
 * element it carries. A user's recommendations are ranked by how well a
 * crystal matches their Sun, Moon and Ascendant, and whether it supports the
 * element their monthly goal is building.
 */

require_once __DIR__ . '/guide.inc.php';

const CRYSTALS = [
	'yellow-topaz' => ['name' => 'Yellow Topaz', 'element' => 'Fire', 'signs' => ['Leo', 'Sagittarius'], 'intention' => 'for building joy', 'price' => 19.99, 'colors' => ['#f6e3b4', '#e2b866']],
	'lapis-lazuli' => ['name' => 'Lapis Lazuli', 'element' => 'Air', 'signs' => ['Sagittarius', 'Libra', 'Taurus'], 'intention' => 'for communication and connection', 'price' => 17.99, 'colors' => ['#2d4fb0', '#14275e']],
	'peridot' => ['name' => 'Peridot', 'element' => 'Earth', 'signs' => ['Leo', 'Virgo', 'Scorpio', 'Sagittarius'], 'intention' => 'for celebrating friendship', 'price' => 16.99, 'colors' => ['#d4ec6a', '#8fb31f']],
	'selenite' => ['name' => 'Selenite', 'element' => 'Water', 'signs' => ['Taurus', 'Cancer'], 'intention' => 'to help a meditator connect to a higher consciousness', 'price' => 14.99, 'colors' => ['#ffffff', '#d9dde2']],
	'sodalite' => ['name' => 'Sodalite', 'element' => 'Air', 'signs' => ['Sagittarius', 'Virgo'], 'intention' => 'for order and calmness in mind', 'price' => 15.99, 'colors' => ['#4a6cc4', '#1d3270']],
	'tigers-eye' => ['name' => 'Tiger\'s Eye', 'element' => 'Earth', 'signs' => ['Leo', 'Capricorn', 'Gemini'], 'intention' => 'to find clarity in your intentions', 'price' => 17.99, 'colors' => ['#d89a3c', '#6e3f10']],
	'amethyst' => ['name' => 'Amethyst', 'element' => 'Water', 'signs' => ['Pisces', 'Aquarius', 'Virgo', 'Capricorn'], 'intention' => 'for calm and intuition', 'price' => 18.99, 'colors' => ['#b28ddb', '#5b3a8c']],
	'rose-quartz' => ['name' => 'Rose Quartz', 'element' => 'Water', 'signs' => ['Taurus', 'Libra'], 'intention' => 'for self-love and an open heart', 'price' => 14.99, 'colors' => ['#fbd9dc', '#e59aa6']],
	'clear-quartz' => ['name' => 'Clear Quartz', 'element' => 'Fire', 'signs' => ['Aries', 'Leo'], 'intention' => 'for clarity and amplifying your intentions', 'price' => 12.99, 'colors' => ['#ffffff', '#cfe0ea']],
	'citrine' => ['name' => 'Citrine', 'element' => 'Fire', 'signs' => ['Aries', 'Gemini', 'Leo', 'Libra'], 'intention' => 'for abundance and confidence', 'price' => 16.99, 'colors' => ['#f9d371', '#d98a0b']],
	'carnelian' => ['name' => 'Carnelian', 'element' => 'Fire', 'signs' => ['Aries', 'Taurus', 'Leo', 'Virgo'], 'intention' => 'for motivation and courage', 'price' => 15.99, 'colors' => ['#ee7a4f', '#9c2e12']],
	'garnet' => ['name' => 'Garnet', 'element' => 'Fire', 'signs' => ['Aries', 'Leo', 'Virgo', 'Capricorn'], 'intention' => 'for passion and devotion', 'price' => 17.99, 'colors' => ['#c2384a', '#5e0f1c']],
	'sunstone' => ['name' => 'Sunstone', 'element' => 'Fire', 'signs' => ['Leo', 'Libra'], 'intention' => 'for optimism and personal power', 'price' => 18.99, 'colors' => ['#f7ad78', '#c95a26']],
	'moonstone' => ['name' => 'Moonstone', 'element' => 'Water', 'signs' => ['Cancer', 'Libra', 'Scorpio'], 'intention' => 'for emotional balance and new beginnings', 'price' => 18.99, 'colors' => ['#f3f6fa', '#a9bcd6']],
	'aquamarine' => ['name' => 'Aquamarine', 'element' => 'Water', 'signs' => ['Pisces', 'Aquarius', 'Gemini'], 'intention' => 'for calm courage and honest speech', 'price' => 19.99, 'colors' => ['#bfe9f0', '#5fb3c4']],
	'labradorite' => ['name' => 'Labradorite', 'element' => 'Water', 'signs' => ['Leo', 'Scorpio', 'Sagittarius'], 'intention' => 'for transformation and intuition', 'price' => 18.99, 'colors' => ['#7fa3b8', '#2f4350']],
	'black-tourmaline' => ['name' => 'Black Tourmaline', 'element' => 'Earth', 'signs' => ['Capricorn', 'Libra'], 'intention' => 'for protection and grounding', 'price' => 16.99, 'colors' => ['#4a4a4a', '#0d0d0d']],
	'smoky-quartz' => ['name' => 'Smoky Quartz', 'element' => 'Earth', 'signs' => ['Capricorn', 'Scorpio', 'Sagittarius'], 'intention' => 'for letting go of stress', 'price' => 15.99, 'colors' => ['#a89384', '#4f4036']],
	'green-aventurine' => ['name' => 'Green Aventurine', 'element' => 'Earth', 'signs' => ['Aries', 'Taurus', 'Virgo'], 'intention' => 'for luck and growth', 'price' => 13.99, 'colors' => ['#83cf9b', '#2f7f4c']],
	'malachite' => ['name' => 'Malachite', 'element' => 'Earth', 'signs' => ['Taurus', 'Scorpio', 'Capricorn'], 'intention' => 'for positive change', 'price' => 19.99, 'colors' => ['#3fb07a', '#0f5333']],
	'fluorite' => ['name' => 'Fluorite', 'element' => 'Air', 'signs' => ['Pisces', 'Capricorn'], 'intention' => 'for focus and a clear mind', 'price' => 15.99, 'colors' => ['#9fdcc9', '#8a6cc0']],
	'blue-lace-agate' => ['name' => 'Blue Lace Agate', 'element' => 'Air', 'signs' => ['Gemini', 'Pisces'], 'intention' => 'for gentle, peaceful communication', 'price' => 14.99, 'colors' => ['#dbe9f7', '#86aedb']],
	'amazonite' => ['name' => 'Amazonite', 'element' => 'Air', 'signs' => ['Aquarius', 'Virgo'], 'intention' => 'for truth and harmony', 'price' => 14.99, 'colors' => ['#a6e3d6', '#4aa596']],
];

/* Photo for a crystal if one exists in img/crystals/ (see includes/photo-credits.inc.php), else null. */
function crystalPhoto(string $id): ?string {
	foreach (['jpg', 'png'] as $ext) {
		$path = 'img/crystals/' . $id . '.' . $ext;
		if (is_file(__DIR__ . '/../' . $path)) return $path;
	}
	return null;
}

/* HTML for a crystal's picture: the photo, or a drawn gem in the crystal's colors. */
function crystalImageHtml(string $id): string {
	$crystal = CRYSTALS[$id];
	$name = htmlspecialchars($crystal['name'], ENT_QUOTES);
	$photo = crystalPhoto($id);
	if ($photo !== null) {
		return '<img class="crystal-img" src="' . htmlspecialchars($photo, ENT_QUOTES) . '" alt="' . $name . '" loading="lazy"/>';
	}
	[$light, $dark] = $crystal['colors'];
	$gradId = 'gem-' . $id;
	return '<svg class="crystal-img crystal-gem" viewBox="0 0 120 120" role="img" aria-label="' . $name . '">'
		. '<defs><linearGradient id="' . $gradId . '" x1="0" y1="0" x2="1" y2="1">'
		. '<stop offset="0" stop-color="' . $light . '"/><stop offset="1" stop-color="' . $dark . '"/></linearGradient></defs>'
		. '<rect width="120" height="120" fill="#f4f1f8"/>'
		. '<polygon points="60,14 92,40 80,100 40,100 28,40" fill="url(#' . $gradId . ')" stroke="#141414" stroke-opacity=".25" stroke-width="1.5"/>'
		. '<polygon points="60,14 92,40 60,48 28,40" fill="#fff" fill-opacity=".35"/>'
		. '<path d="M28 40 L60 48 L92 40 M60 48 L60 100" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="1.5"/>'
		. '</svg>';
}

/*
 * Crystals ranked for a chart. Returns a list of
 * ['id' => ..., 'crystal' => [...], 'score' => int, 'reasons' => [...]], best first.
 */
function recommendCrystals(array $chart, string $goalElement): array {
	$weights = ['Sun' => 3, 'Moon' => 2, 'Ascendant' => 2];
	$ranked = [];
	$order = 0;
	foreach (CRYSTALS as $id => $crystal) {
		$score = 0;
		$reasons = [];
		foreach ($weights as $body => $weight) {
			if (!isset($chart['placements'][$body])) continue;
			$sign = $chart['placements'][$body]['sign'];
			if (in_array($sign, $crystal['signs'], true)) {
				$score += $weight;
				$reasons[] = 'Matches your ' . $body . ' in ' . $sign;
			}
		}
		if ($crystal['element'] === $goalElement) {
			$score += 2;
			$reasons[] = 'Brings in ' . strtolower($goalElement) . ' for this month\'s goal';
		}
		$ranked[] = ['id' => $id, 'crystal' => $crystal, 'score' => $score, 'reasons' => $reasons, 'order' => $order++];
	}
	usort($ranked, function ($a, $b) {
		return [$b['score'], $a['order']] <=> [$a['score'], $b['order']];
	});
	return $ranked;
}

function formatPrice(float $price): string {
	return '$' . number_format($price, 2);
}

/* A product card for one item from recommendCrystals(). */
function crystalCardHtml(array $item, bool $showSigns = true): string {
	$crystal = $item['crystal'];
	$h = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
	$html = '<article class="product-card">' . crystalImageHtml($item['id']) . '<div class="product-body">'
		. '<span class="element-tag">' . $h($crystal['element']) . '</span>'
		. '<h3>' . $h($crystal['name']) . '</h3>'
		. '<p>' . $h(ucfirst($crystal['intention'])) . '.</p>';
	if ($showSigns) {
		$html .= '<p class="product-meta">' . $h(implode(' · ', $crystal['signs'])) . '</p>';
	}
	if (!empty($item['reasons'])) {
		$html .= '<ul class="reasons">';
		foreach ($item['reasons'] as $reason) $html .= '<li>' . $h($reason) . '</li>';
		$html .= '</ul>';
	}
	$html .= '<div class="product-foot"><span class="price">' . $h(formatPrice($crystal['price'])) . '</span></div>'
		. '</div></article>';
	return $html;
}
