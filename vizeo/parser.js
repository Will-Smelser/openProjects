var Parser = function(html){
    var pos = 0;

    this.next = function(token){
        pos = html.indexOf(token, pos);

        return (pos >= 0 && pos < html.length) ? true : false;
    };

    this.skipWs = function(){
        while(html[++pos] === ' ');
    };

    this.skipNotWs = function(ch){
        while(html[++pos] !== ' ' && html[pos] !== '    ' && html[pos] !== ch);
    };

    this.skipTill = function(ch){
        while(html[++pos] !== ch);
    };

    this.skipTillEndOfTag = function(){
        var inQuotes = false;
        var quote = null;
        var cnt = 0;
        while(inQuotes || (html[pos] != '>' && !(html[pos-1] === '/' && html[pos] === '>'))){

            if(!inQuotes && (html[pos] === '"' || html[pos] === "'")){
                inQuotes = true;
                quote = html[pos];
            }

            if(inQuotes && html[pos] === quote){
                inQuotes = false;
                quote = null;
            }

            pos++;

            if(cnt++ > 1000) return;
        }
    }

    this.attributes = function(){
        if(html[pos] !== '<') throw new Exception("Expected starting tag, but got: '"+html.substr(pos,5)+"'");

        var start = pos;

        this.skipTillEndOfTag();

        var end = pos;

        //the full <tag ... /> or <tag ...> portion
        var tag = html.substr(start,end-start+1);

        //reset to begining
        pos = start;

        //move path the <tag
        this.skipNotWs();
        this.skipWs();

        var result = {};
        var good = true;
        while(good && pos <= end){
            var start = pos;
            this.skipNotWs('=');

            var attrNm = html.substr(start, pos - start);

            this.skipWs();

            var quote = html[pos];

            //stupid XML does not have quoted attributes!
            start = (quote === '"' || quote === "'") ? pos : ++pos;

            //just assume, not nested quotes
            if(quote === '"' || quote === "'"){
                this.skipTill(quote);
            }else{
                this.skipNotWs('>');
            }

            result[attrNm] = html.substr(start, pos-start);

            this.skipWs();

            good = (html[pos] === '>' || (html[pos] === '/' && html[pos+1] === '>')) ? false : true;
        }


        return result;
    };

    this.content = function(){
        var start = pos;

        this.skipNotWs('>');

        var tag = html.substr(start+1,pos-start-1)

        pos = start;
        this.skipTillEndOfTag();

        start = ++pos;

        this.next('</'+tag);

        return html.substr(start,pos-start);
    }

    this._info = function(){
        return html.substr(pos-5,50);
    }

    this.parse = function(path){
        var parts = path.split('/');
        var result = null;
        for(var x in parts){
            result = this.next('<'+parts[x]);
            console.log('<'+parts[x],pos);
            pos++;
        }
        pos--;
        return result;
    }
};