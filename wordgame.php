<?php

class WordGame
{

	//assume a-z
	private string $baseString;
	//load word list from txt
	private array $wordList;
	//highScore[] - word, score
	private array $highScore;

	public function __construct($stringLength, $wordListFilename)
	{
		$this->baseString = $this->generateRandomBaseString($stringLength);
		$this->wordList = $this->loadWordList($wordListFilename);
		$this->highScores = array();
	}

	private function loadWordList($filename)
	{
		//TODO: file loader 
		$handle = fopen($filename, "r");
		if ($handle)
			while ($line = fgets($handle) !== false)
				$wordList[] = $line; // assume no whitespace need to take care
		fclose($handle);
		return $wordList;
	}

	private function generateRandomBaseString($length)
	{
		$characters = 'abcdefghijklmnopqrstuvwxyz';
		$baseString = '';
		// $baseString = "areallylongword"
		$charactersLength = strlen($characters);

		for ($i = 0; $i < $length; $i++) {
			$baseString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $baseString;
	}

	/*
	Submit a word on behalf of a player. A word is accepted if its letters are
	contained in the base string used to construct the game AND if it is in the
	word list provided: wordlist.txt.
	
	If the word is accepted and its score is high enough, the submission should
	be added to the high score list. If there are multiple submissions with the
	same score, all are accepted, BUT the first submission with that score
	should rank higher.
	
	A word can only appear ONCE in the high score list. If the word is already
	present in the high score list the submission should be rejected.
	
	@parameter word. The player's submission to the game. All submissions may
	be assumed to be lowercase and contain no whitespace or special characters.

	@returns the score for the submitted word if the submission is accepted. And 0 otherwise.
	*/
	public function submitWord($word)
	{
		//The game is constructed with a random base string of letters a to z

		//The player attempts to create words out of the letters, and scores one point for each letter used.

		//The maximum score is therefore the length of the starting string (IF a valid English word can be made using ALL the letters).

		//Individual letters can be used as many times as they appear in the base string.

		//To score, a submission must be a valid English word.
		//Validity should be checked by loading the word list file wordlist.txt and confirming that the submitted word is present in the list.

		$score = 0;
		if (!$this->isOnWordList($this->baseString, $word) || !in_array($word, $this->wordList)) {
            return 0; 
        }
		$score = $this->basicScore($word);

		
		//The game should maintain in memory a list of the top ten highest-scoring submissions (word and score).

		//The addition of words with the same score as an existing entry should rank lower

		//Duplicate words are NOT allowed in the high score list i.e. a word can only appear once in the high score list.
        // Keep only the top 10 high scores
        // Check for duplicates before adding to high scores
        foreach ($this->highScores as $entry) {
            if ($entry['word'] === $word) {
                return 0; 
            }
        }

        // Assign high score
        $this->highScores[] = array('word' => $word, 'score' => $score);

        // Sort the array in descending order
        usort($this->highScores, function ($a, $b) {
            if ($b['score'] === $a['score']) {
                return array_search($a, $this->highScores) - array_search($b, $this->highScores);
            }
            return $b['score'] - $a['score'];
        });

        // Top 10 high scores
        $this->highScores = array_slice($this->highScores, 0, 10);

		return  $score;
	}
    
    /**
     * basicScore
     *
     * @param  string $word
     * @return int
     */
    private function basicScore($word) {
        return strlen($word);
    }
	
	/**
	 * isOnWordList
	 *
	 * @param  string $baseString
	 * @param  string $word
	 * @return boolean
	 */
	private function isOnWordList($baseString, $word) {
		$baseStringCount = array_count_values(str_split($baseString));
        $wordCount = array_count_values(str_split($word));

        foreach ($wordCount as $letter => $count) {
            if (!isset($baseStringCount[$letter]) || $baseStringCount[$letter] < $count) {
                return false;
            }
        }
		return true;
	}

	/*
	Return word entry at given position in the high score list, position 0 being the
	highest (best score) and position 9 the lowest. You may assume that this method will
	never be called with position > 9.

	@parameter position Index position in high score list

	@return the word entry at the given position in the high score list, or null
	if there is no entry at the position requested
	*/
	public function getWordEntryAtPosition($position)
	{
		return  isset($this->highScores[$position]) && $position <= 9 ? $this->highScores[$position]['word'] : null;
	}

	/*
	Return the score at the given position in the high score list, position 0 being the
	highest (best score) and position 9 the lowest. You may assume that this method will
	never be called with position > 9.

	What is your favourite colour? Please put your answer in your submission
	(this is for testing if you have read the comments).
	 
	@parameter position Index position in high score list

	@return the score at the given position in the high score list, or null if
	there is no entry at the position requested
	*/
	public function getScoreAtPosition($position)
	{
		return  isset($this->highScores[$position]) && $position <= 9 ? $this->highScores[$position]['score'] : null;
	}

	public function startGame()
	{
		foreach ($this->highScores as $i => $score)
			echo ($i + 1) . "|word:" . $score['word'] . "|points:" . $score['score'];
	}
}


// Usage
$stringLength = 10;
$filename = "wordlist.txt";

$game = new WordGame($stringLength, $filename);

$game->submitWord("no");
$game->submitWord("grow");
$game->submitWord("bold");
$game->submitWord("glly");
$game->submitWord("woolly");
$game->submitWord("adder");

$game->startGame();

$position = 0;
echo "Word at position {$position}: " . $game->getWordEntryAtPosition($position) . "\n";
echo "Score at position {$position}: " . $game->getScoreAtPosition($position) . " points\n";
