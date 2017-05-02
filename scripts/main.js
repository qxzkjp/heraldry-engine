function Node()
{
	this.subnode= [];
}

Node.prototype.getName= function (){
	return "(unnamed)";
}

//depth puts hyphens before the name, for tree-printing
Node.prototype.display=function (depth=0)
{
	//console.log("name="+this.getName()+", depth="+depth+", # of subnodes: "+this.subnode.length);
	var str="";
	//apparently the fastest way of creating a series of dashes
	for(var i=0;i<depth;++i)
	{
		str+="-";
	}
	str+=this.getName();
	str+="<br>";
	//ALWAYS USE VAR TO DECLARE ITERATION VARIABLES
	for (var i = 0; i < this.subnode.length; ++i)
	{
		//console.log("Printing subnode #"+i);
		str+=this.subnode[i].display(depth+1);
	}
	return str;
}

Node.prototype.append=function(newNode)
{
	this.subnode.push(newNode);
}

Node.prototype.at=function(index)
{
	return this.subnode[index];
}

function NamedNode(name)
{
	Node.call(this);
	this.name=name;
}

//set up inheritance
NamedNode.prototype=Object.create(Node.prototype);
NamedNode.prototype.constructor=NamedNode;

NamedNode.prototype.getName= function (){
	return this.name;
}

divisions = ["pale", "fess", "bend", "bend sinister", "chevron", "saltire", "", "", "", "", "", "", "",""];
plurals = ["paly", "barry", "bendy", "bendy sinister", "chevronny", "gyronny", "quarterly", "chequy", "lozengy", "barry bendy", "paly bendy", "pily", "pily bendy", "pily bendy sinister"]
//"undefined" is not a real tincture (duh), but is used as a placeholder. It must be in position 0.
tinctures = ["undefined", "argent", "or", "gules", "azure", "vert", "purpure", "sable", "tenny", "sanguine", "vair", "countervair", "potent", "counterpotent", "ermine", "ermines", "erminois", "pean"];
ordinaries = ["chief", "pale", "fess", "bend", "bend sinister", "chevron", "saltire", "pall", "cross", "pile"];
subordinaries = ["bordure", "inescutcheon", "orle", "tressure", "canton", "flanches", "billet", "lozenge", "gyron", "fret"];
movables = ["mullet", "phrygian cap", "fleur-de-lis", "lozenge", "pheon"];
movplurals = ["mullets", "phrygian caps", "fleurs-de-lis", "lozenges", "pheons"];
beasts = ["lion", "eagle"];
attitudes = ["statant", "rampant", "couchant", "passant", "salient", "sejant", "cowed"];
facings = ["guardant", "reguardant"];
conjunctions = ["and"];
prepositions = ["on", "between", "above", "below", "within", "overall"];
linkings=conjunctions+prepositions; 
numbers=["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve"];

function Division(type,number=2)
{
	Node.call(this);
	if(type===5 || type===6) //saltire, quarterly
	{
		if(number<=4) //if there's less than or equal four divisions...
			number=0;//mark it zero (for default/arbitrary)
	}
	this.type=type;
	this.number=number;
}

//set up inheritance
Division.prototype=Object.create(Node.prototype);
Division.prototype.constructor=Division;

Division.prototype.getName= function (){
	var out="";
	//special case for threes, normally only even numbers
	if(this.number===3)
	{
		out+="tierced ";
	}
	//we use the singular case for a two or three-field division, or a four-field if it is a saltire
	if(this.number===2||this.number===3||(this.number===4 && this.type===5))
	{
		out+="per " + divisions[this.type];
	}else{//we use the plural otherwise, and output "of X" if the number is not default
		out+=plurals[this.type]
		if(this.number!==0)
		{out+=" of "+this.number;}
	}
	return out;
}

function Field(tincture)
{
	Node.call(this);
	this.tincture=tincture;
}

//set up inheritance
Field.prototype=Object.create(Node.prototype);
Field.prototype.constructor=Field;

Field.prototype.getName= function (){
	return tinctureName(this.tincture);
}

function Ordinary(kind,tincture)
{
	Node.call(this);
	this.tincture=tincture;
	this.kind=kind;
}

//set up inheritance
Ordinary.prototype=Object.create(Node.prototype);
Ordinary.prototype.constructor=Ordinary;

Ordinary.prototype.getName= function (){
	var out = "a "+ordinaries[this.kind]
	if(this.tincture > 0)
	{out += " "+tinctureName(this.tincture);}
	return out;
}

function Subordinary(kind,tincture)
{
	Node.call(this);
	this.tincture=tincture;
	this.kind=kind;
}

//set up inheritance
Subordinary.prototype=Object.create(Node.prototype);
Subordinary.prototype.constructor=Subordinary;

Subordinary.prototype.getName= function (){
	var out = "a "+subordinaries[this.kind]
	if(this.tincture > 0)
	{out += " "+tinctureName(this.tincture);}
	return out;
}

function Movable(kind,tincture,number=1)
{
	Node.call(this);
	this.tincture=tincture;
	this.kind=kind;
	this.number=number;
}

//set up inheritance
Movable.prototype=Object.create(Node.prototype);
Movable.prototype.constructor=Movable;

Movable.prototype.getName= function (){
	var out="";
	if (this.number===1)
	{
		out+="a ";
		out+=movables[this.kind];
	}else{
		out+=this.number.toString()+" ";
		out+=movplurals[this.kind];
	}
	if(this.tincture > 0)
	{out += " "+tinctureName(this.tincture);}
	return out;
}

/*dynamic SVG?
var svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
svg.setAttribute('style', 'border: 1px solid black');
svg.setAttribute('width', '600');
svg.setAttribute('height', '250');
svg.setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:xlink", "http://www.w3.org/1999/xlink");
document.body.appendChild(svg);
*/

function Buffer(buf="")
{
	this.buf=buf;
	this.pos=0;
}

Buffer.prototype.pop=function()
{
	if(this.pos<this.buf.length)
	{//return buf[pos] and then increment pos
		return this.buf[this.pos++]
	}
}

Buffer.prototype.peek=function()
{
	if(this.pos<this.buf.length)
	{//return buf[pos]
		return this.buf[this.pos]
	}
}

//mainly for informational purposes
TOK_WORD=0;
TOK_NUM=1;
TOK_PUNCT=2;

function Token(value, type)
{
	this.value=value;
	this.type=type;
}

function getToken(buf)
{
	var alph="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-";
	var punct=" ,;:.";
	var num="0123456789";
	var tok="";
	var type=TOK_WORD;
	//first, get fid of leading spaces
	while(buf.peek() === " ")
	{
		buf.pop();
	}
	//if the token begins with a letter, read a string of letters
	if (alph.indexOf(buf.peek()) > -1)
	{
		while (punct.indexOf(buf.peek()) === -1 && buf.peek() !== undefined )
		{
			tok+=buf.pop();
		}
		tok=tok.toLowerCase();
	//if it begins with a digit, read a string of digits
	}else if(num.indexOf(buf.peek()) > -1){
		while (punct.indexOf(buf.peek()) === -1 && buf.peek() !== undefined )
		{
			tok+=buf.pop();
		}
		//store number as a number
		tok=parseInt(tok,10);
		type=TOK_NUM;
	//if it begins with a mark, read a mark
	}else if(punct.indexOf(buf.peek()) > -1)
	{
		tok+=buf.pop();
		type=TOK_PUNCT;
	}
	//if there was no token, return nothing
	if(tok==="")
	{return}
	//treat the word "a" like the number one
	if(tok==="a")
	{return new Token(1,TOK_NUM);}
	//treat spelled-out numbers the same as numerals
	var num=numbers.indexOf(tok);
	if(num > -1)
		{return new Token(num,TOK_NUM);}
	return new Token(tok,type);
}

function TokenStream(buf)
{
	this.buf=buf;
	this.tokens=[];
	this.pos=0;
}

TokenStream.prototype.peek=function()
{
	//if we're past the end of the list, read new token
	if(this.pos === this.tokens.length)
	{
		this.tokens.push(getToken(this.buf));
	}
	//if we just failed to read a token, don't add undefined to the list
	if( this.tokens[this.pos] === undefined)
	{
		this.tokens.pop();
		return;
	}
	//return tokens[pos]
	return this.tokens[this.pos]
}

TokenStream.prototype.pop=function()
{
	var ret=this.peek();
	//if we successfully read a token, increment counter
	if(ret!==undefined)
	{
		++this.pos;
	}
	return ret;
}

TokenStream.prototype.rewind=function(i=1)
{
	this.pos-=i;
}

TokenStream.prototype.reset=function(i=1)
{
	this.pos=0;
}

TokenStream.prototype.savePos=function()
{
	return this.pos;
}

TokenStream.prototype.loadPos=function(i)
{
	this.pos=i;
}

//either return a field, or leave the stream semantically unchanged
function getField(str)
{
	var nxt=str.peek();
	//if the next word is a tincture, we have a field
	if(nxt===undefined)
	{
		console.error("Word "+str.pos.toString()+": unexpected end of blazon")
	}else if(nxt.type === TOK_WORD && tinctures.indexOf(nxt.value) > -1){
		str.pop();
		return new Field(tinctures.indexOf(nxt.value));
	}
}

//either return a division, or leave the stream semantically unchanged
function getDivision(str)
{
	var nxt=str.peek();
	var number=0;
	var type=TOK_WORD;
	if(nxt===undefined)
	{
		console.error("Word "+str.pos.toString()+": unexpected end of blazon")
	}else if(nxt.type === TOK_WORD && nxt.value==="per"){
		str.pop();
		if(str.peek()!==undefined && str.peek().type === TOK_WORD && divisions.indexOf(str.peek().value) > -1)
		{
			var tmp=str.pop();
			type=divisions.indexOf(tmp.value);
			//if the division is "per saltire" we have four segments, otherwise two (per pale, etc)
			if(type===divisions.indexOf("saltire"))
			{
				number=4;
			}else{
				number=2;
			}
			return new Division(type,number);
		}else{
			console.error("Word "+str.pos.toString()+": no such division")
			str.rewind()//un-pop the "per" if we haven't found a division
		}
	}else if(nxt.type === TOK_WORD && nxt.value==="tierced"){
		str.pop();
		var tmp=getDivision(str);
		if(tmp !== undefined)
		{
			tmp.number=3;
			return tmp;
		}else{
			console.error("Word "+str.pos.toString()+": no such division")
			str.rewind(); //un-pop "tierced" if no division
		}
	}else if(str.peek()!==undefined && str.peek().type === TOK_WORD && plurals.indexOf(str.peek().value) > -1){
		type=plurals.indexOf(str.pop().value);
		if(str.peek()!==undefined && str.peek().type === TOK_WORD && str.peek === "of")//if we have "of X"
		{
			str.pop();//take "of" off the stack
			if(str.peek()!==undefined && str.peek().type===TOK_NUM)
			{
				number=str.pop().value; //we don't pop unless we see a number
			}else{
				console.error("Word "+str.pos.toString()+": not a number")
				//we return a division anyway if there is no number, disregarding the "of"
			}
		}
		return new Division(type,number);
	}
}

function isOrdinary(tok)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_WORD )
		{return false}
	if( (ordinaries.indexOf(tok.value) > -1) )
		{return true;}
	if( (plurals.indexOf(tok.value) > -1) )
		{return true;}
	return false;
}

function ordinaryIndex(tok)
{
	var tmp=ordinaries.indexOf(tok.value);
	if( tmp > -1 )
		{return tmp;}
	var tmp=plurals.indexOf(tok.value);
	if( tmp > -1 )
		{return tmp;}
}

function isMovable(tok)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_WORD )
		{return false}
	if( (movables.indexOf(tok.value) > -1) )
		{return true;}
	if( (movplurals.indexOf(tok.value) > -1) )
		{return true;}
	return false;
}

function movableIndex(tok)
{
	var tmp=movables.indexOf(tok.value);
	if( tmp > -1 )
		{return tmp;}
	var tmp=movplurals.indexOf(tok.value);
	if( tmp > -1 )
		{return tmp;}
}

function isTincture(tok)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_WORD )
		{return false}
	return (tinctures.indexOf(tok.value) > -1);
}

function tinctureIndex(tok)
{
	var tmp=tinctures.indexOf(tok.value);
	if( tmp > -1 )
		{return tmp;}
}

function isLinking(tok)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_WORD )
		{return false}
	return (linkings.indexOf(tok.value) > -1);
}

function isNumber(tok)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_NUM )
		{return false}
	return true;
}

function isWord(tok)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_WORD )
		{return false}
	return true;
}

function isThisWord(tok, word)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_WORD )
		{return false}
	if( tok.value !== word )
		{return false}
	return true;
}

function isPunct(tok)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_PUNCT )
		{return false}
	return true;
}

//TODO: implement this function
function getMovable(str)
{
	var num=0; //not possible, error if this does not change
	var type; //defults to undefined
	var tincture=0; //defaults to "specified later"
	//var ret;
	if( isNumber(str.peek()) ){
		num=str.pop().value;
		if( isMovable(str.peek()) )
		{
			type=movableIndex(str.pop());
			while( isWord(str.peek()) && !isLinking(str.peek()) ){
				var tmp=str.pop();
				//tincture is optional, but ends the charge if it exists
				if( isTincture(tmp) )
				{
					tincture=tinctureIndex(tmp);
					break;
				}
			}
			return new Movable(type, tincture, num);
		}else{
			str.rewind();//un-pop the number
			console.error("Word "+str.pos.toString()+": not a movable charge");
			return;
		}
	}else{
		console.error("Word "+str.pos.toString()+": no number where one was expected");
		return;
	}
}

function getOrdinary(str)
{
	var num=0; //not possible, error if this does not change
	var type; //defults to undefined
	var tincture=0; //defaults to "specified later"
	var ret;
	if( isNumber(str.peek()) )
	{
		num=str.pop();
		if( isOrdinary(str.peek()) )
		{
			type=ordinaryIndex(str.pop());
			while( isWord(str.peek()) && !isLinking(str.peek()) ){
				var tmp=str.pop();
				//tincture is optional, but ends the charge if it exists
				if( isTincture(tmp) )
				{
					tincture=tinctureIndex(tmp);
					break;
				}
			}
			ret = new Ordinary(type,tincture);
			//if we see a linking word
			if( isLinking( str.peek() ) )
			{
				var lwrd = str.pop();
				var crg=getMovable(str);
				var scrg;
				if( crg===undefined ){
					str.rewind();//un-pop the linking word
				}else if( isThisWord(lwrd, "between") ){
					//check for a second charge for it to be between
					if( isThisWord(str.peek(), "and") ){
						str.pop();
						var scrg=getMovable(str);
						if( scrg===undefined )
						{
							str.rewind(); //un-pop conjunction
						}
					}
					
					//blank spot for charges on the ordinary itself
					ret.append( new Node() );
					
					//if the ordinary is amidst one group of charges, split the group in half as  evenly as possible
					if(scrg===undefined){
						var hlf=Math.floor(crg.number/2);
						var gtr=crg.number-hlf;
						//place larger half above/dexter of the ordinary, lesser half below/sinister
						ret.append( new Movable(crg.kind, crg.tincture, gtr) );
						ret.append( new Movable(crg.kind, crg.tincture, hlf) );
					}else{ //otherwise just place it beteeen the two groups of charges
						ret.append( crg );
						ret.append( scrg );
					}

				}else{
					str.rewind(); //un-pop linking word
					console.error("Word "+str.pos.toString()+": preposition not implemented");
				}
				
			}
			return ret;
		}else{
			console.error("Word "+str.pos.toString()+": '" +str.peek().value+"' is not an ordinary");
			str.rewind(); //un-pop the number
			return;
		}
	}else if( isThisWord(str.peek(), "on") ){
		str.pop();
		ret=getOrdinary(str);
		if( ret===undefined ){
			str.rewind();//un-pop the "on"
			return;
		}
		//charge on the ordinary itself goes in first subnode always
		var mov=getMovable(str);
		if(mov!==undefined){
			ret.subnode[0]=mov;
			return ret;
		}else{
			console.error("Word "+str.pos.toString()+": no movable charge where one was expected");
		}
	}else{
		console.error("Word "+str.pos.toString()+": no number where one was expected");
		return;
	}
}

//takes a token stream
function getFieldOrDivision(str)
{
	var tmp=getField(str);
	if(tmp!==undefined)
	{
		if( isPunct(str.peek()) )
		{str.pop();}else{
			console.error("Word "+str.pos.toString()+": no comma where one was expected");
		}
		return tmp;
	}
	tmp=getDivision(str)
	if(tmp!==undefined)
	{
		var subf=getEscutcheon(str);//get first tincture/sub-blazon
		if(subf!==undefined){
			tmp.append(subf);
			//if we have a linking word, look for a second tincture
			if( isLinking(str.peek()) ){
				str.pop();
				//if the previous sub-blazon was only a tincture, we only get a tincture. Otherwise an entire sub-blazon.
				if(subf.subnode.length > 0){
					subf=getEscutcheon(str);
				}else{
					subf=getField(str);
				}
				if(subf!==undefined){
					tmp.append(subf);
				}else{
					str.rewind(); //un-pop the linking word
					tmp.append( new Field(0) ); //append a dummy field so the division has two fields
				}
			}
		}
		return tmp;
	}
}

function getEscutcheon(str){
	var tmp=getFieldOrDivision(str);
	var crg=getOrdinary(str);
	if(crg!==undefined){
		tmp.append(crg);
	}
	return tmp;
}

function tokStrFromStr(str)
{
	return new TokenStream(new Buffer(str));
}

function parseString(str)
{
	var tokstr=tokStrFromStr(str);
	return getEscutcheon(tokstr);
}

function parseStringAndDisplay(str)
{
	var tokstr=tokStrFromStr(str);
	var root=getEscutcheon(tokstr);
	document.getElementById("displayPara").innerHTML=root.display();
}

function titleCase(str){
	return str.charAt(0).toUpperCase() + str.slice(1);
}

function tinctureName(i){
	return titleCase(tinctures[i]);
}

tokstr=tokStrFromStr("Azure, a bend Or");
tokstr2=tokStrFromStr("per fess azure and ermine");
tokstr3=tokStrFromStr("tierced per fess Tenny, Argent and Azure");

root=new Field(14);
root.append(new Ordinary(1,4));
root.subnode[0].append(new Node());
root.subnode[0].subnode[0].append(new NamedNode("the words \"Syntax Test\" Argent"));
root.subnode[0].subnode[0].append(new Subordinary(0,2));
root.subnode[0].subnode[0].subnode[1].append(new NamedNode("a console Sable"));
//this works
document.getElementById("displayPara").innerHTML=root.display();
