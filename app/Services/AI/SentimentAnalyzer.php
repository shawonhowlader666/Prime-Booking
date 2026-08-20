<?php

declare(strict_types=1);

namespace App\Services\AI;

/**
 * SentimentAnalyzer — Intelligent Natural Language Sentiment Engine for Reviews.
 * Analyzes guest review comments, computes polarity score (-1.0 to 1.0),
 * detects toxic/fraudulent patterns, and flags urgent customer complaints.
 */
class SentimentAnalyzer
{
    private const POSITIVE_KEYWORDS = [
        'excellent', 'amazing', 'superb', 'great', 'clean', 'comfortable', 'wonderful',
        'friendly', 'spacious', 'helpful', 'delicious', 'perfect', 'loved', 'beautiful',
        'luxury', 'peaceful', 'fantastic', 'spotless', 'cozy', 'recommend', 'polite'
    ];

    private const NEGATIVE_KEYWORDS = [
        'dirty', 'bad', 'terrible', 'horrible', 'noisy', 'broken', 'scam', 'rude',
        'smelly', 'disaster', 'disgusting', 'worst', 'cockroach', 'bedbug', 'cold water',
        'unsafe', 'overpriced', 'refund', 'cheated', 'unfriendly', 'poor'
    ];

    private const CRITICAL_FLAG_WORDS = [
        'scam', 'fraud', 'stolen', 'theft', 'cockroach', 'bedbug', 'police', 'danger', 'unhygienic'
    ];

    /**
     * Analyze review text sentiment and risk.
     *
     * @return array{
     *   sentiment: string,
     *   score: float,
     *   is_flagged: bool,
     *   flag_reasons: list<string>,
     *   highlights: array{positive: list<string>, negative: list<string>}
     * }
     */
    public function analyze(string $text, int|float $starRating = 5): array
    {
        $normalized = strtolower(strip_tags($text));
        $words = preg_split('/[\s,\.!\?]+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $posMatches = [];
        $negMatches = [];
        $criticalMatches = [];

        foreach ($words as $word) {
            if (in_array($word, self::POSITIVE_KEYWORDS, true)) {
                $posMatches[] = $word;
            }
            if (in_array($word, self::NEGATIVE_KEYWORDS, true)) {
                $negMatches[] = $word;
            }
            if (in_array($word, self::CRITICAL_FLAG_WORDS, true)) {
                $criticalMatches[] = $word;
            }
        }

        $posCount = count($posMatches);
        $negCount = count($negMatches);
        $totalEvaluated = $posCount + $negCount;

        // Base text score from -1.0 to 1.0
        if ($totalEvaluated > 0) {
            $textScore = ($posCount - $negCount) / $totalEvaluated;
        } else {
            $textScore = 0.0;
        }

        // Star rating normalized to -1.0 to 1.0 (1= -1.0, 3= 0.0, 5= 1.0 or 10= 1.0)
        $maxRating = $starRating > 5 ? 10.0 : 5.0;
        $ratingNorm = (($starRating - ($maxRating / 2)) / ($maxRating / 2));

        // Combined score weighted 60% text, 40% user rating
        $finalScore = round(($textScore * 0.6) + ($ratingNorm * 0.4), 2);
        $finalScore = max(-1.0, min(1.0, $finalScore));

        $sentiment = match (true) {
            $finalScore >= 0.35  => 'positive',
            $finalScore <= -0.25 => 'negative',
            default              => 'neutral',
        };

        $flagReasons = [];
        if (!empty($criticalMatches)) {
            $flagReasons[] = 'Contains critical words: ' . implode(', ', array_unique($criticalMatches));
        }
        if ($finalScore <= -0.6) {
            $flagReasons[] = 'Extremely negative sentiment score (' . $finalScore . ')';
        }

        return [
            'sentiment'    => $sentiment,
            'score'        => $finalScore,
            'is_flagged'   => !empty($flagReasons),
            'flag_reasons' => $flagReasons,
            'highlights'   => [
                'positive' => array_values(array_unique($posMatches)),
                'negative' => array_values(array_unique($negMatches)),
            ],
        ];
    }
}
