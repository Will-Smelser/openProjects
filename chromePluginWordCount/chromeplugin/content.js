	var stop_words = {
		'a':'',
		'about':'',
		'above':'',
		'across':'',
		'after':'',
		'again':'',
		'against':'',
		'all':'',
		'almost':'',
		'alone':'',
		'along':'',
		'already':'',
		'also':'',
		'although':'',
		'always':'',
		'among':'',
		'an':'',
		'and':'',
		'another':'',
		'any':'',
		'anybody':'',
		'anyone':'',
		'anything':'',
		'anywhere':'',
		'are':'',
		'area':'',
		'areas':'',
		'around':'',
		'as':'',
		'ask':'',
		'asked':'',
		'asking':'',
		'asks':'',
		'at':'',
		'away':'',
		'b':'',
		'back':'',
		'backed':'',
		'backing':'',
		'backs':'',
		'be':'',
		'became':'',
		'because':'',
		'become':'',
		'becomes':'',
		'been':'',
		'before':'',
		'began':'',
		'behind':'',
		'being':'',
		'beings':'',
		'best':'',
		'better':'',
		'between':'',
		'big':'',
		'both':'',
		'but':'',
		'by':'',
		'c':'',
		'came':'',
		'can':'',
		'cannot':'',
		'case':'',
		'cases':'',
		'certain':'',
		'certainly':'',
		'clear':'',
		'clearly':'',
		'come':'',
		'could':'',
		'd':'',
		'did':'',
		'differ':'',
		'different':'',
		'differently':'',
		'do':'',
		'does':'',
		'done':'',
		'down':'',
		'down':'',
		'downed':'',
		'downing':'',
		'downs':'',
		'during':'',
		'e':'',
		'each':'',
		'early':'',
		'either':'',
		'end':'',
		'ended':'',
		'ending':'',
		'ends':'',
		'enough':'',
		'even':'',
		'evenly':'',
		'ever':'',
		'every':'',
		'everybody':'',
		'everyone':'',
		'everything':'',
		'everywhere':'',
		'f':'',
		'face':'',
		'faces':'',
		'fact':'',
		'facts':'',
		'far':'',
		'felt':'',
		'few':'',
		'find':'',
		'finds':'',
		'first':'',
		'for':'',
		'four':'',
		'from':'',
		'full':'',
		'fully':'',
		'further':'',
		'furthered':'',
		'furthering':'',
		'furthers':'',
		'g':'',
		'gave':'',
		'general':'',
		'generally':'',
		'get':'',
		'gets':'',
		'give':'',
		'given':'',
		'gives':'',
		'go':'',
		'going':'',
		'good':'',
		'goods':'',
		'got':'',
		'great':'',
		'greater':'',
		'greatest':'',
		'group':'',
		'grouped':'',
		'grouping':'',
		'groups':'',
		'h':'',
		'had':'',
		'has':'',
		'have':'',
		'having':'',
		'he':'',
		'her':'',
		'here':'',
		'herself':'',
		'high':'',
		'high':'',
		'high':'',
		'higher':'',
		'highest':'',
		'him':'',
		'himself':'',
		'his':'',
		'how':'',
		'however':'',
		'i':'',
		'if':'',
		'important':'',
		'in':'',
		'interest':'',
		'interested':'',
		'interesting':'',
		'interests':'',
		'into':'',
		'is':'',
		'it':'',
		'its':'',
		'itself':'',
		'j':'',
		'just':'',
		'k':'',
		'keep':'',
		'keeps':'',
		'kind':'',
		'knew':'',
		'know':'',
		'known':'',
		'knows':'',
		'l':'',
		'large':'',
		'largely':'',
		'last':'',
		'later':'',
		'latest':'',
		'least':'',
		'less':'',
		'let':'',
		'lets':'',
		'like':'',
		'likely':'',
		'long':'',
		'longer':'',
		'longest':'',
		'm':'',
		'made':'',
		'make':'',
		'making':'',
		'man':'',
		'many':'',
		'may':'',
		'me':'',
		'member':'',
		'members':'',
		'men':'',
		'might':'',
		'more':'',
		'most':'',
		'mostly':'',
		'mr':'',
		'mrs':'',
		'much':'',
		'must':'',
		'my':'',
		'myself':'',
		'n':'',
		'necessary':'',
		'need':'',
		'needed':'',
		'needing':'',
		'needs':'',
		'never':'',
		'new':'',
		'new':'',
		'newer':'',
		'newest':'',
		'next':'',
		'no':'',
		'nobody':'',
		'non':'',
		'noone':'',
		'not':'',
		'nothing':'',
		'now':'',
		'nowhere':'',
		'number':'',
		'numbers':'',
		'o':'',
		'of':'',
		'off':'',
		'often':'',
		'old':'',
		'older':'',
		'oldest':'',
		'on':'',
		'once':'',
		'one':'',
		'only':'',
		'open':'',
		'opened':'',
		'opening':'',
		'opens':'',
		'or':'',
		'order':'',
		'ordered':'',
		'ordering':'',
		'orders':'',
		'other':'',
		'others':'',
		'our':'',
		'out':'',
		'over':'',
		'p':'',
		'part':'',
		'parted':'',
		'parting':'',
		'parts':'',
		'per':'',
		'perhaps':'',
		'place':'',
		'places':'',
		'point':'',
		'pointed':'',
		'pointing':'',
		'points':'',
		'possible':'',
		'present':'',
		'presented':'',
		'presenting':'',
		'presents':'',
		'problem':'',
		'problems':'',
		'put':'',
		'puts':'',
		'q':'',
		'quite':'',
		'r':'',
		'rather':'',
		'really':'',
		'right':'',
		'right':'',
		'room':'',
		'rooms':'',
		's':'',
		'said':'',
		'same':'',
		'saw':'',
		'say':'',
		'says':'',
		'second':'',
		'seconds':'',
		'see':'',
		'seem':'',
		'seemed':'',
		'seeming':'',
		'seems':'',
		'sees':'',
		'several':'',
		'shall':'',
		'she':'',
		'should':'',
		'show':'',
		'showed':'',
		'showing':'',
		'shows':'',
		'side':'',
		'sides':'',
		'since':'',
		'small':'',
		'smaller':'',
		'smallest':'',
		'so':'',
		'some':'',
		'somebody':'',
		'someone':'',
		'something':'',
		'somewhere':'',
		'state':'',
		'states':'',
		'still':'',
		'still':'',
		'such':'',
		'sure':'',
		't':'',
		'take':'',
		'taken':'',
		'than':'',
		'that':'',
		'the':'',
		'their':'',
		'them':'',
		'then':'',
		'there':'',
		'therefore':'',
		'these':'',
		'they':'',
		'thing':'',
		'things':'',
		'think':'',
		'thinks':'',
		'this':'',
		'those':'',
		'though':'',
		'thought':'',
		'thoughts':'',
		'three':'',
		'through':'',
		'thus':'',
		'to':'',
		'today':'',
		'together':'',
		'too':'',
		'took':'',
		'toward':'',
		'turn':'',
		'turned':'',
		'turning':'',
		'turns':'',
		'two':'',
		'u':'',
		'under':'',
		'until':'',
		'up':'',
		'upon':'',
		'us':'',
		'use':'',
		'used':'',
		'uses':'',
		'v':'',
		'very':'',
		'w':'',
		'want':'',
		'wanted':'',
		'wanting':'',
		'wants':'',
		'was':'',
		'way':'',
		'ways':'',
		'we':'',
		'well':'',
		'wells':'',
		'went':'',
		'were':'',
		'what':'',
		'when':'',
		'where':'',
		'whether':'',
		'which':'',
		'while':'',
		'who':'',
		'whole':'',
		'whose':'',
		'why':'',
		'will':'',
		'with':'',
		'within':'',
		'without':'',
		'work':'',
		'worked':'',
		'working':'',
		'works':'',
		'would':'',
		'x':'',
		'y':'',
		'year':'',
		'years':'',
		'yet':'',
		'you':'',
		'young':'',
		'younger':'',
		'youngest':'',
		'your':'',
		'yours':'',
		'z':''
	};

//settings
var settings = {
	domEls : {
		content : {'h1':{},'h2':{},'h3':{},'h4':{},'h5':{},'h6':{},'p':{},'td':{},'li':{}},
		//all tags support "title" attribute
		meta : {'img':{attr:'alt'},'td':{attr:'abbr'},'th':{attr:'abbr'}}
	},
	utils : {
		getWords : function(txt){
			return txt.split(" ");
		}
	}
};

var type = ['content','meta'];

var count = function(wrd){
	var obj = {
		word : wrd,
		variations : [],
		context : {},
		count : 0,
		add : function(context){
			if(typeof this.context[context] == 'undefined')
				this.context[context] = {count:0};
			
			this.count++;
			this.context[context].count++;
		}
	};
	return obj;
};

var cleanWord = function(word){
	
	if(word == null || typeof word != "string" || word.length < 1) return "";
	
	word = word.toLowerCase();
	
	var clean = "";
	for(var ch=0; ch < word.length; ch++){
		var ascii = word.charCodeAt(ch);
		if(96 < ascii && ascii < 123) clean += word[ch];
	}
	
	if(typeof stop_words[clean] != 'undefined') return "";
	
	return clean;
};

var getData = function(){
	var results = {};
	
	//just parse the body
	/*
	var bodyWords = $('body').text().replace("  "," ").split(" ");
	for(var x in bodyWords){
		var theWord = cleanWord(bodyWords[x]);
		
		if(theWord.length < 2) continue;
		
		if(typeof results[theWord] == 'undefined'){
			results[theWord] = new count(theWord);
		}
		
		results[theWord].count++;
	}
	*/
	
	//do the dom walk
	for(var x in settings.domEls){
	  if(x === 'content'){
		  for(var el in settings.domEls[x]){
			  console.log("parsing nodes: "+el);
			  
			  $(el).each(function(){
				  
				 //get the words
				 var txt = $(this).text();
				 
				 var words = settings.utils.getWords(txt);
				 
				 //meta searching
				 for(var word in words){
					 var theWord = cleanWord(words[word]);
					 
					 if(theWord.length < 2) continue;
					 
					 if(typeof results[theWord] == 'undefined'){
						 results[theWord] = new count(theWord);
					 }
					 
					 results[theWord].add(el);
					 results[theWord].variations.push(words[word]);
				 }
			  });
		  }
	  }
  };
  console.log(results);
  return results;
};

function dataSort(a,b){
	return b.count - a.count;
}


  //chrome.extension.sendRequest(results);
  chrome.extension.onRequest.addListener(function(request, sender, sendResponse){
	  console.log('content page received request');
	  console.log('content page sending response');
	  
	  var data = getData();
	  var temp = [];
	  
	  for(var x in data)
		  temp.push(data[x]);
	  
	  //sendResponse(data.sort(dataSort));
	  sendResponse(temp.sort(dataSort));
  });

