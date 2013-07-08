<?php

/**
 * Hold information about a word
 * @used-by WordCount
 * @author Will
 *
 */
class Word{
	
	/**
	 * @var String the "normalized" version of the word
	 */
	public $normal;
	
	/**
	 * @var int The number of occurences of this word
	 */
	public $count;
	
	/**
	 * @var Array An array of String containing the variations of this page
	 */
	public $words=array();

	/**
	 * Initialization does not add the word to the wrods list
	 * @param String $normal
	*/
	public function Word($normal){
		$this->normal = $normal;
		$this->count = 0;
	}

	/**
	 * Add to this word a variation and increment the count
	 * @param String $word The word to add
	 */
	public function addWord($word){
		if(!in_array($word, $this->words))
			array_push($this->words, $word);
		$this->count++;
	}
}
?>