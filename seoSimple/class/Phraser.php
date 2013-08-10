<?php

interface Phraser{
	public function addWord($word, $normal, $isStopWord);
}

class phrase {
	public $normal;
	public $actual = array();
	public $count = 0;
}

function PhraseSort($a, $b){
	if($a->count < $b->count)
		return 1;
	elseif($a->count == $b->count)
		return 0;
	else
		return -1;
}

class SimplePhraser implements Phraser{
	
	public $d = 'MYDELIMITER';
	
	/**
	 * Number of words per phrase
	 * @var unknown
	 */
	public $plength = 3;
	
	private $buffer = array();
	private $buffer2 = array();
	private $stops = array();
	
	private $phrases = array();
	
	//private $index=0;
	
	
	public function SimplePhraser($length = 3){
		$this->plength = $length;
	}
	
	public function closePhrase(){
		$phrase = implode(' ',$this->buffer);
		$phrase2 = implode(' ',$this->buffer2);
		
		if(empty($phrase)) return;
		
		$this->buffer = array();
		$this->buffer2 = array();
		$this->stops = array();
		
		if(!isset($this->phrases[$phrase])){
			$this->phrases[$phrase] = new Phrase();
			$this->phrases[$phrase]->normal = $phrase;
		}
		
		$this->phrases[$phrase]->count++;
		array_push($this->phrases[$phrase]->actual, $phrase2);
			
		return;
	}
	
	public function addWord($word, $normal, $isStopWord=false){
		if(empty($word) || empty($normal)) return;
		
		array_push($this->buffer2,$word);
		if($isStopWord){
			array_push($this->stops,$word);
		}else{
			array_push($this->buffer,$normal);
		}
		
		
		if($this->plength === count($this->buffer)){
			$phrase = implode(' ',$this->buffer);
			$phrase2 = implode(' ',$this->buffer2);
			
			if(!isset($this->phrases[$phrase])){
				$this->phrases[$phrase] = new Phrase();
				$this->phrases[$phrase]->normal = $phrase;
			}
			
			$this->phrases[$phrase]->count++;
			array_push($this->phrases[$phrase]->actual, $phrase2);
			
			array_shift($this->buffer);
			$temp = array_shift($this->buffer2);
			
			while(count($this->stops) > 0 && $temp === $this->stops[0]){
				array_shift($this->stops);
				$temp = array_shift($this->buffer2);
			}
			
		}
	}
	
	public function getTotalPhrases(){
		return count($this->phrases);
	}
	
	public function getSortedPhrases(){
		usort($this->phrases, "PhraseSort");
		return $this->phrases;
	}
	
	public function getPhrasesWithWord($word){
		$result = array();
		foreach($this->phrases as $p){
			foreach($p->actual as $a)
				if(stripos($a, $word) !== false)
					array_push($result, $a);
		}
		return $result;
	}
	
	/*
	private function getNext(){
		$pos = $this->index++;
		
		//$pos is post the last element
		if($pos >= count($this->phrases)){
			$this->index === 0;
			return false;
		}
		
		return $this->phrases[$pos];
	}
	*/
	/*
	private function cleanBuffer(){
		$phrase = implode(' ',$this->buffer);
		$phrase2 = implode(' ',$this->buffer2);
		
		$this->buffer = array();
		$this->buffer2 = array();
		
		if(!isset($phrase[$this->phrases])){
			$this->phrases[$phrase] = new Phrase();
		}
		
		$this->phrases[$phrase]->count++;
		array_push($this->phrases[$phrase]->actual, $phrase2);
	}
	*/
}

?>