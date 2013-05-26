

//$(document).ready(function(){
	//settings
	var settings = {
		domEls : {
			content : {'h1':{},'h2':{},'h3':{},'h4':{},'h5':{},'h6':{},'p':{}/*,'div':{}*/,'td':{}},
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
	
	var results = {};
	for(var x in settings.domEls){
	  if(x === 'content'){
		  for(var el in settings.domEls[x]){
			  $(el).each(function(){
				 //get the words
				 var words = settings.utils.getWords($(this).text());
				 
				 //meta searching
				 for(var word in words){
					 if(typeof results[words[word]] == 'undefined')
						 results[words[word]] = new count(words[word]);
					 
					 results[words[word]].add(el);
				 }
			  });
		  }
	  }
  };
  
  chrome.extension.sendRequest(results);
//});
