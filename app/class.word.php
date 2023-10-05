<?php

class WordGame {
    private $baseString;
    private $wordList;

    public function __construct($baseString, $wordListFilename) {
        $this->baseString = strtolower($baseString);
        $this->wordList = $this->loadWordList($wordListFilename);
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

    public function playGame() {
        $highScores = array();

        foreach ($this->wordList as $word) {
            $word = strtolower($word);

            if ($this->canFormWord($this->baseString, $word)) {
                $score = $this->calculateScore($word);

                $highScores[] = array('word' => $word, 'score' => $score);

                usort($highScores, function ($a, $b) {
                    return $b['score'] - $a['score'];
                });

                $highScores = array_slice($highScores, 0, 10);
            }
        }

        echo "Top 10 High Scores:\n";
        foreach ($highScores as $index => $score) {
            echo ($index + 1) . ". {$score['word']} - {$score['score']} points\n";
        }
    }
}

// Usage
$baseString = "areallylongword";
$wordListFilename = "wordlist.txt";

$game = new WordGame($baseString, $wordListFilename);
$game->playGame();