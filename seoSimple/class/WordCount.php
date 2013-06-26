<?php
class Word{
	public $normal;
	public $count;
	public $word;
	public $words=array();
	
	/**
	 * Initialization does not add the word to the wrods list
	 * @param String $word
	 * @param String $normal
	 */
	public function Word($word, $normal){
		$this->word = $word;
		$this->normal = $normal;
		$this->count = 0;
	}
	
	public function addWord($word){
		if(!in_array($word, $this->words))
		array_push($this->words, $word);
		$this->count++;
	}
}

function WordSort($a, $b){
	if($a->count < $b->count)
		return 1;
	elseif($a->count == $b->count)
		return 0;
	else
		return -1;
}

class WordCount{
	public function getCount($str){
		$result = array();
		
		$str = strtolower($str);
		$this->addSpaces($str);
		$this->cleanHTML($str);
		
		$words = str_word_count($str,1);//gives all unique workds
		
		foreach($words as $key=>$word){
			if(!in_array($word,$this->stopwords)){
				$normal = $this->normalize($word);
				if(!empty($normal) && array_key_exists($normal,$result)){
					$result[$normal]->count++;
				}else{
					$result[$normal] = new Word($word,$normal);
				}
				$result[$normal]->addWord($word);
			}
		}
		
		usort($result, "WordSort");
		
		return $result;
	}
	
	/**
	 * Look for a tag in the given string and remove the tag and its contents.
	 * Currently assumes non self closing tags and no spaces in closing tag. 
	 * @param unknown $tag
	 * @param unknown $str
	 */
	private function removeTag($tag, &$str){
		$start = strpos($str, "<$tag");
		while($start > 0){
			$end = strpos($str, "</$tag");
			while($str[$end] != '>') $end++;
			$str = substr($str, 0, $start) . substr($str, $end);//, strlen($str));
			$start = strpos($str, "<$tag");
		}
	}
	
	private function addSpaces(&$str){
		$str = str_replace('</',' </',$str);
	}
	
	private function cleanHTML(&$str){
		
		//get just body
		$start = strpos($str,"<body");
		$end = strpos($str,"</body");
		
		$str = substr($str, $start, $end-$start);
		
		//now we have to remove other bad tags
		foreach(array('script','style','embed','iframe','noscript','frame','frameset','object','video','track','progress') as $tag){
			$this->removeTag($tag, $str);
		}
		
		$str = strip_tags($str);
	}
	
	private function normalize($word){
		if(strlen($word) < 4)
			return $word;
		
		//make sure we remove "'" and such
		$word = preg_replace('/(\'|\"|\-|\_)/','',$word);
		
		$temp = rtrim($word, "esd");
		
		if($temp > 3)
			return $temp;
		
		return $word;
	}
	
	private $stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
}
?>