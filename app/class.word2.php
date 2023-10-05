<?php

class WordGame {
    private $baseString;
    private $wordList;
    private $highScores;

    public function __construct($baseString, $wordListFilename) {
        $this->baseString = strtolower($baseString);
        $this->wordList = $this->loadWordList($wordListFilename);
        $this->highScores = array();
    }

    private function loadWordList($filename) {
        return array_map('trim', file($filename));
    }

    private function calculateScore($word) {
        return strlen($word);
    }

    private function canFormWord($baseString, $word) {
        $baseStringCount = array_count_values(str_split($baseString));
        $wordCount = array_count_values(str_split($word));

        foreach ($wordCount as $letter => $count) {
            if (!isset($baseStringCount[$letter]) || $baseStringCount[$letter] < $count) {
                return false;
            }
        }

        return true;
    }

    public function submitWord($word) {
        $word = strtolower($word);

        if (!$this->canFormWord($this->baseString, $word) || !in_array($word, $this->wordList)) {
            return 0; // Submission rejected due to invalid word
        }

        $score = $this->calculateScore($word);

        // Check for duplicates before adding to high scores
        foreach ($this->highScores as $entry) {
            if ($entry['word'] === $word) {
                return 0; // Submission rejected due to duplication
            }
        }

        // Add the word and score to the high scores array
        $this->highScores[] = array('word' => $word, 'score' => $score);

        // Sort the high scores array by score in descending order
        usort($this->highScores, function ($a, $b) {
            if ($b['score'] === $a['score']) {
                // Maintain the order of the first submission with the same score
                return array_search($a, $this->highScores) - array_search($b, $this->highScores);
            }
            return $b['score'] - $a['score'];
        });

        // Keep only the top 10 high scores
        $this->highScores = array_slice($this->highScores, 0, 10);

        return $score; // Submission accepted
    }

    public function getWordEntryAtPosition($position) {
        return isset($this->highScores[$position]) ? $this->highScores[$position]['word'] : null;
    }

    public function getScoreAtPosition($position) {
        return isset($this->highScores[$position]) ? $this->highScores[$position]['score'] : null;
    }

    public function playGame() {
        echo "Top 10 High Scores:\n";
        foreach ($this->highScores as $index => $score) {
            echo ($index + 1) . ". {$score['word']} - {$score['score']} points\n";
        }
    }
}

// Usage
$baseString = "areallylongword";
$wordListFilename = "wordlist.txt";

$game = new WordGame($baseString, $wordListFilename);
$game->submitWord("no");
$game->submitWord("grow");
$game->submitWord("woolly");
$game->submitWord("no"); // Duplicate submission
$game->playGame();

$position = 0;
echo "Word at position {$position}: " . $game->getWordEntryAtPosition($position) . "\n";
echo "Score at position {$position}: " . $game->getScoreAtPosition($position) . " points\n";
