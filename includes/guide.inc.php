<?php
/*
 * Crystal's individualized spiritual guide.
 *
 * The guide is built from the user's natal chart:
 *  - This Month's Goal targets the element the chart is weakest in, using the
 *    element it is strongest in to get there.
 *  - The Week's Plan rotates through the challenges of the Sun, Moon and
 *    Ascendant signs.
 *  - Today's Guide rotates through the user's placements and mentions the
 *    sign the Moon is passing through today.
 *
 * Every choice is deterministic for a given user and date, so the guide does
 * not change when the page is refreshed.
 */

require_once __DIR__ . '/astrology.inc.php';

const ELEMENTS = ['Fire', 'Earth', 'Air', 'Water'];

const SIGN_INFO = [
	'Aries' => [
		'element' => 'Fire', 'modality' => 'Cardinal', 'glyph' => "\u{2648}",
		'sun' => 'Your Sun is in Aries, meaning you are fundamentally brave, direct, and driven to begin things. You are at your best when there is a challenge in front of you, and you would rather act and learn than wait for permission. Your energy gets people moving.',
		'moon' => 'Your Moon is in Aries, meaning your emotional self is quick, honest, and fiery. You feel things intensely and in the moment, and you need room to let off steam. Your feelings flare up fast, but they rarely linger.',
		'rising' => 'Your Ascendant is in Aries, meaning you come across as bold, energetic, and competitive. People notice your confidence right away, and you tend to jump into new situations headfirst.',
		'style' => 'courage, speed, and a love of firsts',
		'weekly' => [
			'title' => 'Slow down.',
			'insight' => 'Aries energy wants to win, and to win now, which can make you impatient with people who move at a different pace.',
			'practice' => 'This week, before you jump in, take one breath and ask a question first. Let someone else set the pace once, and notice what you learn when you follow.',
		],
		'ideas' => ['Start the task you have been putting off.', 'Try a new workout or a faster walking route.', 'Stand up for someone who needs it.'],
	],
	'Taurus' => [
		'element' => 'Earth', 'modality' => 'Fixed', 'glyph' => "\u{2649}",
		'sun' => 'Your Sun is in Taurus, meaning you are fundamentally steady, loyal, and grounded. You value comfort, beauty, and things that last, and you build your life slowly and deliberately. People rely on you because you follow through.',
		'moon' => 'Your Moon is in Taurus, meaning your emotional self craves security and calm. You feel safest with routine, good food, and familiar people, and you are slow to anger but also slow to forgive. Comfort is not a luxury for you; it is how you recharge.',
		'rising' => 'Your Ascendant is in Taurus, meaning you come across as calm, patient, and reliable. You have a relaxed, easygoing presence, and people often find you reassuring to be around.',
		'style' => 'patience, loyalty, and an eye for beauty',
		'weekly' => [
			'title' => 'Try something new.',
			'insight' => 'Taurus energy loves the familiar, which can turn into stubbornness when life asks you to change.',
			'practice' => 'This week, change one small habit on purpose: a new route, a new recipe, or an opinion you hear out to the end. Growth often starts just outside your comfort zone.',
		],
		'ideas' => ['Cook a meal slowly and enjoy every step.', 'Spend ten minutes outside with your feet on the grass.', 'Treat yourself to something small and beautiful.'],
	],
	'Gemini' => [
		'element' => 'Air', 'modality' => 'Mutable', 'glyph' => "\u{264A}",
		'sun' => 'Your Sun is in Gemini, meaning you are fundamentally curious, quick-witted, and social. You collect ideas and people with equal enthusiasm, and you can talk your way into (and out of) almost anything. Variety keeps you alive.',
		'moon' => 'Your Moon is in Gemini, meaning your emotional self is often restless and unsettled. You\'re extremely adaptable, which sometimes makes you feel pulled in too many directions. You are easily bored and need to feel like you are free to be creative and create meaning, which can make you feel like you\'re in a constant identity crisis.',
		'rising' => 'Your Ascendant is in Gemini, meaning you come across as quick-witted, chatty, eclectic, and fun, though somewhat inconsistent. In conversation, you seem probing and curious.',
		'style' => 'curiosity, wit, and conversation',
		'weekly' => [
			'title' => 'Finish one thing.',
			'insight' => 'Gemini energy loves starting new ideas, which can leave a trail of half-finished projects and scattered attention.',
			'practice' => 'This week, pick one task and see it all the way through before you start the next. Notice how good it feels to close the loop.',
		],
		'ideas' => ['Call a friend you have not talked to in a while.', 'Read something outside your usual interests.', 'Write down three ideas before bed.'],
	],
	'Cancer' => [
		'element' => 'Water', 'modality' => 'Cardinal', 'glyph' => "\u{264B}",
		'sun' => 'Your Sun is in Cancer, meaning you are fundamentally caring, protective, and deeply loyal. Home and family, whether you were born into it or chose it, are at the center of your world. You remember everything, and you nurture everyone.',
		'moon' => 'Your Moon is in Cancer, meaning your emotional self is sensitive, thoughtful, and empathetic. You have a tendency to feel like a martyr, and secretly fear being abandoned by those you love. You often have trouble letting things go and can feel like an emotional wreck.',
		'rising' => 'Your Ascendant is in Cancer, meaning you come across as warm, gentle, and a little reserved at first. People sense that you care, and they often open up to you quickly.',
		'style' => 'care, memory, and emotional intuition',
		'weekly' => [
			'title' => 'Come out of your shell.',
			'insight' => 'Cancer energy protects itself by retreating, which can leave the people who love you guessing about what you need.',
			'practice' => 'This week, say one need out loud instead of hoping someone will notice it. Letting yourself be cared for is part of caring.',
		],
		'ideas' => ['Cook or bake something for someone you love.', 'Look through old photos and send one to the person in it.', 'Make your space cozy: fresh sheets, a candle, a tidy corner.'],
	],
	'Leo' => [
		'element' => 'Fire', 'modality' => 'Fixed', 'glyph' => "\u{264C}",
		'sun' => 'Your Sun is in Leo, meaning you are fundamentally bold and proud. You love attention and to be cared for. You pay it back in kind with your charm and your ability to make people feel better when they\'re blue. You know what your skills are and how to use them.',
		'moon' => 'Your Moon is in Leo, meaning your emotional self is warm, generous, and dramatic. You need to feel seen and appreciated, and you give your affection loudly and loyally. When you feel ignored, your pride can be hurt more than you let on.',
		'rising' => 'Your Ascendant is in Leo, meaning you come across as confident, magnetic, and warm. You naturally take up space in a room, and people tend to remember meeting you.',
		'style' => 'warmth, creativity, and generous leadership',
		'weekly' => [
			'title' => 'Listen up.',
			'insight' => 'Leo energy loves being in charge, which can make you seem smothering to friends. It is natural for you to dominate a conversation.',
			'practice' => 'Focus this week on noticing when you have interrupted someone, and let that person finish their sentence. True listening requires setting aside oneself, and sometimes that means setting aside your personal opinion.',
		],
		'ideas' => ['Compliment someone in front of others.', 'Make something creative just for fun.', 'Share the spotlight: ask a quiet friend about their work.'],
	],
	'Virgo' => [
		'element' => 'Earth', 'modality' => 'Mutable', 'glyph' => "\u{264D}",
		'sun' => 'Your Sun is in Virgo, meaning you are fundamentally thoughtful, precise, and helpful. You notice the details everyone else misses, and you feel most like yourself when you are improving something, or helping someone you care about.',
		'moon' => 'Your Moon is in Virgo, meaning your emotional self finds calm in order and usefulness. You process feelings by analyzing them, and you show love through acts of service. When life feels chaotic, you can turn your critical eye on yourself.',
		'rising' => 'Your Ascendant is in Virgo, meaning you come across as composed, modest, and observant. People see you as capable and put-together, even when you feel otherwise.',
		'style' => 'attention to detail and quiet acts of service',
		'weekly' => [
			'title' => 'Let it be good enough.',
			'insight' => 'Virgo energy strives for perfection, which can turn into harsh self-criticism and endless fixing.',
			'practice' => 'This week, finish one thing at "good enough" and leave it alone. Each time you catch your inner critic, answer it with one thing you did well.',
		],
		'ideas' => ['Tidy one drawer or shelf.', 'Help someone with a task they have been dreading.', 'Plan tomorrow tonight, then put the list away.'],
	],
	'Libra' => [
		'element' => 'Air', 'modality' => 'Cardinal', 'glyph' => "\u{264E}",
		'sun' => 'Your Sun is in Libra, meaning you are fundamentally fair, gracious, and relationship-minded. You see every side of a situation, and you care deeply about harmony, beauty, and justice. You bring people together.',
		'moon' => 'Your Moon is in Libra, meaning your emotional self needs balance and partnership. You feel best when your relationships are peaceful, and you may smooth over a conflict before you have figured out what you actually feel.',
		'rising' => 'Your Ascendant is in Libra, meaning you come across as charming, polite, and easy to like. You have a natural sense of style, and people find you approachable and diplomatic.',
		'style' => 'harmony, fairness, and an ability to give without strings attached',
		'weekly' => [
			'title' => 'Make a decision.',
			'insight' => 'Libra energy weighs every option to keep the peace, which can leave you stuck, or putting others first by default.',
			'practice' => 'This week, make one decision quickly and stand by it, even if not everyone is pleased. Your preferences count, too.',
		],
		'ideas' => ['Pay for the person behind you in line.', 'Offer up your seat.', 'Volunteer.'],
	],
	'Scorpio' => [
		'element' => 'Water', 'modality' => 'Fixed', 'glyph' => "\u{264F}",
		'sun' => 'Your Sun is in Scorpio, meaning you are fundamentally intense, perceptive, and loyal to the core. You are drawn to what lies beneath the surface, and you would rather have a few deep bonds than many shallow ones. You transform through every challenge.',
		'moon' => 'Your Moon is in Scorpio, meaning your emotional self is deep, private, and powerful. You feel everything fully but share it carefully, and people earn your trust slowly. Once you love, you love completely.',
		'rising' => 'Your Ascendant is in Scorpio, meaning you come across as passionate, incisive, strategic, and perceptive. Your intense and tenacious drive can come off as intimidating and powerful to people who do not know you yet.',
		'style' => 'depth, focus, and fierce loyalty',
		'weekly' => [
			'title' => 'Let someone in.',
			'insight' => 'Scorpio energy guards its inner world closely, which can turn into suspicion or keeping people at arm\'s length.',
			'practice' => 'This week, share one honest thing about yourself with someone you trust. Vulnerability is a strength you already have.',
		],
		'ideas' => ['Journal about something you have never said out loud.', 'Have a real conversation instead of small talk.', 'Let go of one thing you no longer need.'],
	],
	'Sagittarius' => [
		'element' => 'Fire', 'modality' => 'Mutable', 'glyph' => "\u{2650}",
		'sun' => 'Your Sun is in Sagittarius, meaning you are fundamentally adventurous, optimistic, and honest. You are always looking for the bigger picture, whether through travel, ideas, or belief, and your enthusiasm is contagious.',
		'moon' => 'Your Moon is in Sagittarius, meaning your emotional self needs freedom and meaning. You cheer yourself up with plans, humor, and movement, and you can feel trapped when life gets too routine.',
		'rising' => 'Your Ascendant is in Sagittarius, meaning you come across as friendly, upbeat, and a little restless. People see you as fun and open-minded, always up for the next adventure.',
		'style' => 'optimism, adventure, and the search for meaning',
		'weekly' => [
			'title' => 'Follow through.',
			'insight' => 'Sagittarius energy loves freedom, which can make commitments feel heavy and lead you to overpromise.',
			'practice' => 'This week, keep one promise exactly as you made it, and say no to one thing you know you would not finish.',
		],
		'ideas' => ['Explore a part of your town you have never visited.', 'Learn a few words of a new language.', 'Plan a trip, even a small one.'],
	],
	'Capricorn' => [
		'element' => 'Earth', 'modality' => 'Cardinal', 'glyph' => "\u{2651}",
		'sun' => 'Your Sun is in Capricorn, meaning you are fundamentally ambitious, disciplined, and responsible. You play the long game, and you are willing to work hard for goals that matter. People trust you to get things done.',
		'moon' => 'Your Moon is in Capricorn, meaning your emotional self is reserved and self-reliant. You feel secure when you are in control and making progress, and you may hide your softer feelings behind work.',
		'rising' => 'Your Ascendant is in Capricorn, meaning you come across as serious, capable, and mature beyond your years. People see you as dependable, and your dry humor surprises those who get close.',
		'style' => 'discipline, patience, and long-term vision',
		'weekly' => [
			'title' => 'Rest without earning it.',
			'insight' => 'Capricorn energy measures worth by achievement, which can make rest feel like something you have to earn.',
			'practice' => 'This week, schedule one block of time with no goal at all, and protect it like a meeting. You are more than what you produce.',
		],
		'ideas' => ['Write down one long-term goal and the very first step toward it.', 'Ask a mentor or an elder for advice.', 'Take an hour off without checking your phone.'],
	],
	'Aquarius' => [
		'element' => 'Air', 'modality' => 'Fixed', 'glyph' => "\u{2652}",
		'sun' => 'Your Sun is in Aquarius, meaning you are fundamentally unconventional and independent. Comfortable dissenting or straying from the norm, your intellectual talent of abstraction is oriented towards pushing the boundaries of what seems reasonable. You carry a lot on your shoulders and have a need to fight for the underdog.',
		'moon' => 'Your Moon is in Aquarius, meaning your emotional self is cool, independent, and idealistic. You process feelings through ideas and need plenty of space, which can make you seem detached even when you care deeply.',
		'rising' => 'Your Ascendant is in Aquarius, meaning you come across as original, friendly, and a little unpredictable. People notice right away that you march to your own beat.',
		'style' => 'originality, vision, and care for the group',
		'weekly' => [
			'title' => 'Let go.',
			'insight' => 'Aquarius energy doesn\'t hold grudges, exactly, but it does remember a lot of disagreements and hurt feelings. Learn to let go!',
			'practice' => 'Notice when you are frustrated and remind yourself that it is just a feeling. Holding a grudge can do damage to your presence of mind in the long run.',
		],
		'ideas' => ['Notice when you have a great laugh with someone and appreciate it.', 'If you feel like hugging someone, do it.', 'Ask that one person out.'],
	],
	'Pisces' => [
		'element' => 'Water', 'modality' => 'Mutable', 'glyph' => "\u{2653}",
		'sun' => 'Your Sun is in Pisces, meaning you are fundamentally compassionate, imaginative, and intuitive. You feel what others feel, and you move through the world with an artist\'s sensitivity and a dreamer\'s hope.',
		'moon' => 'Your Moon is in Pisces, meaning your emotional self is gentle, dreamy, and absorbent. You soak up the moods around you, and you need quiet time alone to sort out which feelings are actually yours.',
		'rising' => 'Your Ascendant is in Pisces, meaning you come across as soft, kind, and a little mysterious. People find you easy to talk to and often feel understood by you.',
		'style' => 'compassion, imagination, and intuition',
		'weekly' => [
			'title' => 'Set a boundary.',
			'insight' => 'Pisces energy wants to help everyone, which can leave you drained and carrying feelings that are not yours.',
			'practice' => 'This week, say no once to something that would cost you too much. Protecting your energy is how you keep having it to give.',
		],
		'ideas' => ['Listen to one full song with your eyes closed.', 'Write down a dream or a daydream.', 'Do one kind thing anonymously.'],
	],
];

/* What each placement represents. */
const BODY_INFO = [
	'Sun' => [
		'glyph' => "\u{2609}",
		'intro' => 'The sun determines your ego, identity, and "role" in life. It\'s the core of who you are, and is the sign you\'re most likely to already know.',
		'meaning' => 'your core self and the energy you radiate',
		'prompt' => 'Today, let that part of you lead: do one thing that feels completely like you.',
	],
	'Moon' => [
		'glyph' => "\u{263D}",
		'intro' => 'The moon rules your emotions, moods, and feelings. This is likely the sign you most think of yourself as, since it reflects your personality when you\'re alone or deeply comfortable.',
		'meaning' => 'your emotions and what you need to feel safe',
		'prompt' => 'Today, check in with how you feel before you react, and give yourself what you need.',
	],
	'Ascendant' => [
		'glyph' => 'AC',
		'intro' => 'Your ascendant is the "mask" you present to people. It can be seen in your personal style and how you come off to people when you first meet. Some say it becomes less relevant as you get older. It changes about every two hours, so if it doesn\'t make sense, reconfirm your birth time to be sure.',
		'meaning' => 'the first impression you make',
		'prompt' => 'Today, show up on purpose: notice the energy you bring into each room.',
	],
	'Mercury' => [
		'glyph' => "\u{263F}",
		'meaning' => 'how you think, learn, and communicate',
		'prompt' => 'Today, put that gift into words: write, ask, or explain something.',
	],
	'Venus' => [
		'glyph' => "\u{2640}",
		'meaning' => 'how you love and what you value',
		'prompt' => 'Today, make room for beauty and for the people you care about.',
	],
	'Mars' => [
		'glyph' => "\u{2642}",
		'meaning' => 'your drive and how you take action',
		'prompt' => 'Today, channel that energy into one concrete act, like a kind gesture for a stranger.',
	],
	'Jupiter' => [
		'glyph' => "\u{2643}",
		'meaning' => 'where you grow and find luck',
		'prompt' => 'Today, say yes to one opportunity to grow.',
	],
	'Saturn' => [
		'glyph' => "\u{2644}",
		'meaning' => 'where you build discipline and lasting strength',
		'prompt' => 'Today, take one small, steady step on something long-term.',
	],
	'Uranus' => [
		'glyph' => "\u{2645}",
		'meaning' => 'how your generation breaks the mold',
		'prompt' => 'Today, don\'t let anyone pressure you into second-guessing your own ideas.',
	],
	'Neptune' => [
		'glyph' => "\u{2646}",
		'meaning' => 'your generation\'s dreams and ideals',
		'prompt' => 'Today, leave a little room for imagination.',
	],
	'Pluto' => [
		'glyph' => "\u{2647}",
		'meaning' => 'your generation\'s deepest transformations',
		'prompt' => 'Today, notice one thing you are ready to let go of.',
	],
];

// Placements counted for the element balance, and how much each one weighs.
const ELEMENT_WEIGHTS = ['Sun' => 3, 'Moon' => 3, 'Ascendant' => 2, 'Mercury' => 1, 'Venus' => 1, 'Mars' => 1];

// Placements the daily guide rotates through (personal planets plus Jupiter, Saturn and Uranus).
const DAILY_BODIES = ['Sun', 'Moon', 'Ascendant', 'Mercury', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'Uranus'];

const ELEMENT_INFO = [
	'Fire' => ['signs' => 'Aries, Leo, and Sagittarius', 'gives' => 'courage, passion, and drive', 'today' => 'a good day for courage and for starting things'],
	'Earth' => ['signs' => 'Taurus, Virgo, and Capricorn', 'gives' => 'patience, practicality, and follow-through', 'today' => 'a good day for practical tasks and for taking care of your body'],
	'Air' => ['signs' => 'Gemini, Libra, and Aquarius', 'gives' => 'curiosity, ideas, and connection', 'today' => 'a good day for conversation and new ideas'],
	'Water' => ['signs' => 'Cancer, Scorpio, and Pisces', 'gives' => 'intuition, empathy, and emotional depth', 'today' => 'a good day for rest, feelings, and intuition'],
];

/* Monthly goals, grouped by the element they help a chart build. */
const MONTHLY_GOALS = [
	'Air' => [
		['title' => 'Build deeper relationships with friends.', 'body' => 'Close friendships are one of the strongest sources of happiness we have. Having strong social relationships can have effects on your overall health, including building a stronger immune system, and living a longer, more fulfilling life.'],
		['title' => 'Say what you think.', 'body' => 'Practice honest, open conversation. Once a week, share an idea or an opinion you would normally keep to yourself, and stay curious about the response.'],
		['title' => 'Learn something new.', 'body' => 'Pick one subject you know nothing about and spend a little time on it each week. A curious mind keeps your spirit flexible and opens new connections with others.'],
	],
	'Water' => [
		['title' => 'Build a meditation and/or reflection habit.', 'body' => 'Only do 5 minutes a day. Meditation is a great way to consciously and unconsciously get in touch with your spiritual self. By sitting in a comfortable position and focusing on your breath you can allow your mind to step away from the troubles of the physical world and drift into a realm of relaxation.'],
		['title' => 'Get to know your feelings.', 'body' => 'Keep a short feelings journal. Each evening, write down one emotion you felt that day and what caused it. Naming a feeling is the first step to understanding it.'],
		['title' => 'Practice compassion.', 'body' => 'Each day this month, try to understand one person\'s point of view before responding to it, including your own. Compassion grows the more you use it.'],
	],
	'Earth' => [
		['title' => 'Create a grounding routine.', 'body' => 'Choose one small ritual, like a morning stretch, a cup of tea without your phone, or a walk at sunset, and repeat it every day. Routines give your spirit a steady place to stand.'],
		['title' => 'Spend time in nature.', 'body' => 'Get outside at least three times a week, even for ten minutes. Time among trees, water, and open sky has been shown to lower stress and help you feel present.'],
		['title' => 'Take care of your body.', 'body' => 'Your body carries your spirit. This month, pay attention to sleep, water, and movement, and notice how your mood changes when your body feels cared for.'],
	],
	'Fire' => [
		['title' => 'Move your body.', 'body' => 'Find a kind of movement you enjoy, like dancing, running, or hiking, and do it a few times a week. Physical energy feeds spiritual energy.'],
		['title' => 'Say yes to something new.', 'body' => 'Courage is a habit. This month, say yes to one invitation or opportunity each week that you would usually turn down.'],
		['title' => 'Make time for joy.', 'body' => 'Schedule something purely for fun every week. Play and laughter are a form of spiritual practice, too.'],
	],
];

/* Element of a sign. */
function signElement(string $sign): string {
	return SIGN_INFO[$sign]['element'];
}

/* Weighted element totals for a chart, e.g. ['Fire' => 5, 'Earth' => 1, ...]. */
function chartElementBalance(array $chart): array {
	$balance = array_fill_keys(ELEMENTS, 0);
	foreach (ELEMENT_WEIGHTS as $body => $weight) {
		if (isset($chart['placements'][$body])) {
			$balance[signElement($chart['placements'][$body]['sign'])] += $weight;
		}
	}
	return $balance;
}

/* Pick from a list of tied options in a way that changes with the period, not on refresh. */
function guidePick(array $options, int $seed) {
	$options = array_values($options);
	return $options[(($seed % count($options)) + count($options)) % count($options)];
}

/* This Month's Goal. */
function guideMonthlyGoal(array $chart, DateTimeInterface $today): array {
	$balance = chartElementBalance($chart);
	$monthSeed = (int) $today->format('Y') * 12 + (int) $today->format('n');

	$min = min($balance);
	$max = max($balance);
	$weakest = guidePick(array_keys($balance, $min, true), $monthSeed);
	$strongest = guidePick(array_keys($balance, $max, true), $monthSeed);

	$goal = guidePick(MONTHLY_GOALS[$weakest], $monthSeed);

	if ($weakest === $strongest) {
		$why = 'Your chart is evenly balanced across the elements, so this month we are focusing on ' . strtolower($weakest) . ': ' . ELEMENT_INFO[$weakest]['gives'] . '.';
	} else {
		$why = 'Your chart is lightest in ' . strtolower($weakest) . ' (' . ELEMENT_INFO[$weakest]['signs'] . '), the element of '
			. ELEMENT_INFO[$weakest]['gives'] . '. Lean on your strongest element, ' . strtolower($strongest) . ', and its '
			. ELEMENT_INFO[$strongest]['gives'] . ', to get there.';
	}

	return $goal + ['element' => $weakest, 'strongest' => $strongest, 'why' => $why];
}

/* The Week's Plan. */
function guideWeeklyPlan(array $chart, DateTimeInterface $today): array {
	$bodies = array_values(array_filter(['Sun', 'Moon', 'Ascendant'], function ($b) use ($chart) {
		return isset($chart['placements'][$b]);
	}));
	$weekSeed = (int) $today->format('o') * 53 + (int) $today->format('W');
	$body = guidePick($bodies, $weekSeed);
	$sign = $chart['placements'][$body]['sign'];

	return SIGN_INFO[$sign]['weekly'] + [
		'body' => $body,
		'sign' => $sign,
		'source' => 'This week\'s focus comes from your ' . $body . ' in ' . $sign . '.',
	];
}

/* Today's Guide. */
function guideDaily(array $chart, DateTimeInterface $today, int $userId): array {
	$bodies = array_values(array_filter(DAILY_BODIES, function ($b) use ($chart) {
		return isset($chart['placements'][$b]);
	}));
	$daySeed = (int) $today->format('Y') * 366 + (int) $today->format('z');
	$body = guidePick($bodies, $daySeed + $userId);
	$sign = $chart['placements'][$body]['sign'];

	$name = $body === 'Ascendant' ? 'Your Ascendant' : ($body === 'Sun' || $body === 'Moon' ? 'The ' . $body : $body);
	$text = 'Your ' . $body . ' is in ' . $sign . '. ' . $name . ' represents ' . BODY_INFO[$body]['meaning']
		. ', and in ' . $sign . ' it works through ' . SIGN_INFO[$sign]['style'] . '. ' . BODY_INFO[$body]['prompt'];

	// The sign the Moon is passing through at noon today.
	$noon = new DateTimeImmutable($today->format('Y-m-d') . ' 12:00:00', $today->getTimezone());
	$moonSign = astroCurrentMoonSign($noon->setTimezone(new DateTimeZone('UTC')));
	$moonElement = signElement($moonSign);

	return [
		'body' => $body,
		'sign' => $sign,
		'text' => $text,
		'ideas' => SIGN_INFO[$sign]['ideas'],
		'moonSign' => $moonSign,
		'moonText' => 'Today the Moon is in ' . $moonSign . ', ' . (in_array($moonElement, ['Air', 'Earth'], true) ? 'an ' : 'a ')
			. strtolower($moonElement) . ' sign: ' . ELEMENT_INFO[$moonElement]['today'] . '.',
	];
}

/* The whole guide for a user. */
function buildGuide(array $chart, int $userId, ?DateTimeInterface $today = null): array {
	$today = $today ?? new DateTimeImmutable('now');
	return [
		'monthly' => guideMonthlyGoal($chart, $today),
		'weekly' => guideWeeklyPlan($chart, $today),
		'daily' => guideDaily($chart, $today, $userId),
	];
}

/* "on the cusp of Capricorn and Aquarius" for a placement on a cusp, else null. */
function cuspPhrase(array $placement): ?string {
	if (empty($placement['cuspWith'])) return null;
	$i = array_search($placement['sign'], ZODIAC_SIGNS, true);
	$j = array_search($placement['cuspWith'], ZODIAC_SIGNS, true);
	$pair = (($i + 1) % 12 === $j) ? [$placement['sign'], $placement['cuspWith']] : [$placement['cuspWith'], $placement['sign']];
	return 'on the cusp of ' . $pair[0] . ' and ' . $pair[1];
}

/* "12°34′" within the sign. */
function formatDegree(float $degreeInSign): string {
	$totalMinutes = (int) floor($degreeInSign * 60);
	return intdiv($totalMinutes, 60) . "\u{00B0}" . str_pad((string) ($totalMinutes % 60), 2, '0', STR_PAD_LEFT) . "\u{2032}";
}

/* Glyph that renders as text (not emoji) on all platforms. */
function textGlyph(string $glyph): string {
	return mb_strlen($glyph) === 1 ? $glyph . "\u{FE0E}" : $glyph;
}
