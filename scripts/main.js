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

divisions=["pale", "fess", "bend", "bend sinister", "chevron", "saltire", "", "", "", "", "", "", "",""];
plurals=["paly", "barry", "bendy", "bendy sinister", "chevronny", "gyronny", "quarterly", "chequy", "lozengy", "barry bendy", "paly bendy", "pily", "pily bendy", "pily bendy sinister"]
tinctures=["Argent", "Or", "Gules", "Azure", "Vert", "Purpure", "Sable", "Tenny", "Sanguine", "Vair", "Countervair", "Potent", "Counterpotent", "Ermine", "Ermines", "Erminois", "Pean"];
ordinaries=["chief", "pale", "fess", "bend", "bend sinister", "Chevron", "Saltire", "Pall", "Cross", "Pile"];
subordinaries=["bordure", "inescutcheon", "Orle", "Tressure", "Canton", "Flanches", "Billet", "Lozenge", "Gyron", "Fret"];
movables=["mullet", "phrygian cap", "fleur-de-lis", "lozenge", "pheon"];
beasts=["lion", "eagle"];
attitudes=["statant", "rampant", "couchant", "passant", "salient", "sejant", "cowed"];
facings=["guardant", "reguardant"];

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
	return tinctures[this.tincture];
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
	return "a "+ordinaries[this.kind]+" "+tinctures[this.tincture];
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
	out+=movables[this.kind]+" "+tinctures[this.tincture];
	return out;
}


/*
var root=new NamedNode("field Argent");
root.append(new NamedNode("phrygian cap Gules"));
root.append(new NamedNode("bend Azure"));
root.at(1).append(new NamedNode("mullet (6-point) Or (x3)"));
root.append(new NamedNode("phrygian cap Gules"));
*/

/*
root=new Field(0);
root.append(new Ordinary(3,3));
root.subnode[0].append(new Moveable(1,2));
root.subnode[0].append(new Moveable(0,1,3));
root.subnode[0].append(new Moveable(1,2));*/
root=new Division(0);
root.append(new Field(0))
root.subnode[0].append(new Moveable(1,2));
root.append(new Field(2))
root.subnode[1].append(new Moveable(0,1,3));
//this works
document.getElementById("displayPara").innerHTML=root.display();

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
tokenTypes=["word" , "number", "punctuation"];

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
	var type=0;
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
	//if it begins with a digit, read a string of digits
	}else if(num.indexOf(buf.peek()) > -1){
		while (punct.indexOf(buf.peek()) === -1 && buf.peek() !== undefined )
		{
			tok+=buf.pop();
		}
		//store number as a number
		tok=parseInt(tok,10);
		type=1
	//if it begins with a mark, read a mark
	}else if(punct.indexOf(buf.peek()) > -1)
	{
		tok+=buf.pop();
		type=2;
	}
	//if there was no token, return nothing
	if(tok==="")
	{return}
	//treat the word "a" like the number one
	if(tok==="a")
	{return new Token(1,1);}
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

//either return a field, or leave the stream semantically unchanged
function getField(str)
{
	var nxt=str.peek();
	//if the next word is a tincture, we have a field
	if(nxt.type === 0 && tinctures.indexOf(nxt.value) > -1)
	{
		str.pop();
		return new Field(tinctures.indexOf(nxt.value));
	}
}

//either return a division, or leave the stream semantically unchanged
function getDivision(str)
{
	var nxt=str.peek();
	var number=0;
	var type=0;
	if(nxt.type === 0 && nxt.value==="per")
	{
		str.pop();
		if(str.peek().type === 0 && divisions.indexOf(str.peek().value) > -1)
		{
			var tmp=str.pop();
			type=divisions.indexOf(tmp.value);
			if(type===5)
			{
				number=4;
			}else{
				number=2;
			}
			return new Division(type,number);
		}
		str.rewind()//un-pop the "per" if we haven't returned a division
	}else if(nxt.type === 0 && nxt.value==="tierced"){
		str.pop();
		var tmp=getFieldOrDivision(str);
		if(tmp !== undefined)
		{
			tmp.number=3;
			return tmp;
		}
		str.rewind(); //un-pop "tierced" if no division
	}else if(str.peek().type === 0 && plurals.indexOf(str.peek().value) > -1){
		type=plurals.indexOf(str.pop().value);
		if(str.peek().type === 0 && str.peek === "of")//if we have "of X"
		{
			str.pop();//take "of" off the stack
			if(str.peek().type===1)
			{
				number=str.pop().value; //we don't pop unless we see a number
			}else{str.rewind();} //un-pop "of" if we don't see a number
		}
		return new Division(type,number);
	}
}

//takes a token stream
function getFieldOrDivision(str)
{
	var tmp=getField(str);
	if(tmp!==undefined)
	{
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
//blaz=new Buffer("Per pale Gules and Azure a bend Argent");
blaz=new Buffer("Azure a bend Or");
tokstr=tokStrFromStr("Azure a bend Or");
tokstr2=tokStrFromStr("per fess azure and ermine");