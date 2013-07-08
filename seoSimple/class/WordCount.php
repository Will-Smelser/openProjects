<?php
require_once 'Word.php';

function WordSort($a, $b){
	if($a->count < $b->count)
		return 1;
	elseif($a->count == $b->count)
		return 0;
	else
		return -1;
}

/**
 * Finds all the words in an HTML string and removes them if they are in the stop list.
 * Ignores all HTML tags.
 * @author Will
 *
 */
class WordCount{
	/**
	 * Parse a string for words
	 * @param String $str The string to parse for words and count them
	 * @return Array A sorted array of Word objects based on frequency
	 */
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
					$result[$normal] = new Word($normal);
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
	 * @param String $tag
	 * @param String $str
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
	
	/**
	 * Add a space to all end tags
	 * @param String $str A reference to the string to be altered
	 */
	private function addSpaces(&$str){
		$str = str_replace('</',' </',$str);
	}
	
	/**
	 * Remove elements from the string such as iframe, frame, embed, script, etc...
	 * @param String $str The string object to be altered.
	 */
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
	
	/**
	 * Normalize a word.  Performs actions such as removing non-character symbols
	 * and removing es, s, ed, e from the end of words
	 * @param String $word The word to be normalized
	 * @return String the "normalized" version of the word.
	 */
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
	
	/**
	 * @var Array An array of string of stop words to be removed.
	 */
	private $stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
}
?>