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
beasts = ["lion", "eagle"];
attitudes = ["statant", "rampant", "couchant", "passant", "salient", "sejant", "cowed"];
facings = ["guardant", "reguardant"];
conjunctions = ["and"];
prepositions = ["on", "between", "above", "below", "within"];
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

function Moveable(kind,tincture,number=1)
{
	Node.call(this);
	this.tincture=tincture;
	this.kind=kind;
	this.number=number;
}

//set up inheritance
Moveable.prototype=Object.create(Node.prototype);
Moveable.prototype.constructor=Moveable;

Moveable.prototype.getName= function (){
	var out="";
	if (this.number===1)
	{
		out+="a ";
	}else{
		out+=this.number.toString()+" ";
	}
	out+=movables[this.kind];
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
	return (ordinaries.indexOf(tok.value) > -1)
}

function isTincture(tok)
{
	return (tinctures.indexOf(tok.value) > -1)
}

function isLinking(tok)
{
	return (linkings.indexOf(tok.value) > -1)
}

function getOrdinary(str)
{
	var num=0; //not possible, error if this does not change
	var type; //defults to undefined
	var tincture=0; //defaults to "specified later"
	if(str.peek().type===TOK_NUM)
	{
		num=str.pop();
		if( str.peek().type===TOK_WORD && isOrdinary(str.peek()) )
		{
			type=ordinaries.indexOf(str.pop().value);
			while( str.peek()!==undefined && str.peek().type===TOK_WORD && !isLinking(str.peek()) ){
				var tmp=str.pop();
				//tincture is optional, but ends the charge if it exists
				if( isTincture(tmp) )
				{
					tincture=tinctures.indexOf(tmp.value);
					break;
				}
			}
			return new Ordinary(type,tincture);
		}else{
			console.error("Word "+str.pos.toString()+": '" +str.peek().value+"' is not an ordinary");
			str.rewind(); //un-pop the number
			return;
		}
	}else{
		console.error("Word "+str.pos.toString()+": no number where one was expected");
	}
}

//takes a token stream
function getEscutcheon(str)
{
	var tmp=getField(str);
	if(tmp!==undefined)
	{
		if(str.peek().type===TOK_PUNCT)
		{str.pop();}else{
			console.error("Word "+str.pos.toString()+": no comma where one was expected");
		}
		var crg=getOrdinary(str);
		tmp.append(crg);
		return tmp;
	}
	tmp=getDivision(str)
	if(tmp!==undefined)
	{
		return tmp;
	}
}

function tokStrFromStr(str)
{
	return new TokenStream(new Buffer(str));
}

function parseString(str)
{
	tokstr=tokStrFromStr(str);
	return getEscutcheon(tokstr);
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

root=new Division(0);
root.append(new Field(1))
root.subnode[0].append(new Moveable(1,3));
root.append(new Field(3))
root.subnode[1].append(new Moveable(0,2,3));
//this works
document.getElementById("displayPara").innerHTML=root.display();
