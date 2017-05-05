/*******************************************************
**PROTOTYPES (NODES)                                  **
**prototypes for named and unnamed nodes in a simple  **
**non-binary tree structure. includes naming and      **
**display functions.                                  **
********************************************************/

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

/*********************************************************************
**MAP FUNCTIONS                                                     **
**functions for setting up, accessing and modifying token tree-maps **
**to look up a token called "abc xyz" you would call:               **
**map.get("abc").get("xyz").get("")                                 **
**********************************************************************/

function addToMap(map, name, value)
{
	var wrds=name.split(" ");
	var tmp=map;
	for (var j=0; j<wrds.length; ++j){
		if(tmp.get(wrds[j]) === undefined){
			tmp.set(wrds[j], new Map);
		}
		tmp=tmp.get(wrds[j]);
	}
	tmp.set("", value);
}

function getFromMap(map, name)
{
	var wrds=name.split(" ");
	var tmp=map;
	var ret;
	for (var j=0; j<wrds.length; ++j){
		nxt=tmp.get(wrds[j]);
		if(nxt!==undefined){
			tmp=tmp.get(wrds[j]);
		}else{
			return;
		}
	}
	return tmp.get("");
}

function setupMap(arr){
	var ret=new Map();
	for (var i=0; i<arr.length; ++i){
		if(arr[i]!==""){
			addToMap(ret, arr[i], i);
		}
	}
	return ret;
}

function appendMap(mapbase, mapapp, type, flag)
{
	for( var [key, value] of mapapp )
	{
		if(typeof value === "number"){
			if(mapbase.get(key)===undefined){
				mapbase.set(key, []);
			}
			if( type!==undefined && flag!==undefined){
				mapbase.get(key)[type]=[value, flag];
			}else{
				mapbase.get(key)=value;
			}
		}else{
			if(mapbase.get(key)===undefined){
				mapbase.set(key, new Map);
			}
			appendMap(mapbase.get(key), value, type, flag);
		}
	}
}

function addSynonym(map, name, syn){
	var value = getFromMap(map, name);
	addToMap(map, syn, value);
}

/********************************************************
** TOKEN LISTS                                         **
** Lists of all the possible tokens of different types **
*********************************************************/
divisions = ["per pale", "per fess", "per bend", "per bend sinister", "per chevron", "per saltire", "", "", "", "", "", "", "",""];
plurals = ["paly", "barry", "bendy", "bendy sinister", "chevronny", "gyronny", "quarterly", "chequy", "lozengy", "barry bendy", "paly bendy", "pily", "pily bendy", "pily bendy sinister"]

//"undefined" is not a real tincture (duh), but is used as a placeholder. It must be in position 0.
tinctures = ["undefined", "argent", "or", "gules", "azure", "vert", "purpure", "sable", "tenny", "sanguine", "vair", "countervair", "potent", "counterpotent", "ermine", "ermines", "erminois", "pean"];

//chevron is immovable, while "chevrons" is moveable: strange hack, but it might work
immovables = ["chief", "pale", "fess", "bend", "bend sinister", "chevron", "saltire", "pall", "cross", "pile", "bordure", "orle", "tressure", "canton", "flanches", "gyron", "fret"];
implurals = ["", "pallets", "bars", "bendlets", "bendlets sinister", "chevronels", "", "", "", "pile", "", "", "", "", "", "", ""];

movables = ["mullet", "phrygian cap", "fleur-de-lis", "pheon", "moveable-chevron", "inescutcheon", "billet", "lozenge", "key", "phrygian cap with bells on"];
movplurals = ["mullets", "phrygian caps", "fleurs-de-lis", "pheons", "chevrons", "inescutcheons", "billets", "lozenges", "keys", "phrygian caps with bells on"];

beasts = ["lion", "eagle"];
attitudes = ["statant", "rampant", "couchant", "passant", "salient", "sejant", "cowed"];
facings = ["guardant", "reguardant"];

conjunctions = ["and"];
prepositions = ["on", "in", "between", "above", "below", "within", "overall"];
numbers=["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve"];

orientations=["palewise", "fesswise", "bendwise"]
orientmods=["sinister", "reversed", "inverted"]
arrangements=["addorsed", "confronte", "saltire", "pale", "fess", "bend"]

//the cardinal directions, going anticlockwise in 45deg jumps
var ORIENT_PALE=0;
var ORIENT_BEND=1;
var ORIENT_FESS=2;
var ORIENT_SINISTER_INV=3;
var ORIENT_PALE_INV_REV=4;
var ORIENT_BEND_INV_REV=5;
var ORIENT_FESS_INV_REV=6;
var ORIENT_SINISTER_REV=7;

//the cardinal directions for a mirrrored charge
var ORIENT_PALE_REV=0;
var ORIENT_BEND_REV=1;
var ORIENT_FESS_INV=2;
var ORIENT_SINISTER_INV_REV=3;
var ORIENT_PALE_INV=4;
var ORIENT_BEND_INV=5;
var ORIENT_FESS_REV=6;
var ORIENT_SINISTER=7;

//definitions of whether an orientation is mirrored or not

var PALE_REV_MIRRORED=true;
var BEND_REV_MIRRORED=true;
var FESS_INV_MIRRORED=true;
var SINISTER_INV_REV_MIRRORED=true;
var PALE_INV_MIRRORED=true;
var BEND_INV_MIRRORED=true;
var FESS_REV_MIRRORED=true;
var SINISTER_MIRRORED=true;

var PALE_MIRRORED=true;
var BEND_MIRRORED=true;
var FESS_MIRRORED=true;
var SINISTER_INV_MIRRORED=true;
var PALE_INV_REV_MIRRORED=true;
var BEND_INV_REV_MIRRORED=true;
var FESS_INV_REV_MIRRORED=true;
var SINISTER_REV_MIRRORED=true;

//type constants for charges
TYPE_IMMOVABLE = 0;
TYPE_MOVABLE = 1;


//set up token maps
divisionsMap = new Map();
appendMap(divisionsMap, setupMap(divisions), 0, false);
appendMap(divisionsMap, setupMap(plurals), 0, true);

immovableMap = setupMap(immovables);
impluralMap = setupMap(implurals);

movableMap = setupMap(movables);
movpluralMap = setupMap(movplurals);

orientationsMap = setupMap(orientations);
omodsMap = setupMap(orientmods);
arrangementsMap = setupMap(arrangements);

//set up synonyms for certain tokens
addSynonym(arrangementsMap, "confronte", "confronté");
addSynonym(arrangementsMap, "confronte", "combatant");
addSynonym(arrangementsMap, "confronte", "respectant");

addSynonym(movableMap, "fleur-de-lis", "fleur-de-lys");
addSynonym(movableMap, "fleur-de-lis", "fleur de lis");
addSynonym(movableMap, "fleur-de-lis", "fleur de lys");

addSynonym(movpluralMap, "fleurs-de-lis", "fleurs-de-lys");
addSynonym(movpluralMap, "fleurs-de-lis", "fleurs de lis");
addSynonym(movpluralMap, "fleurs-de-lis", "fleurs de lys");

chargeMap=new Map;
appendMap(chargeMap, movableMap, TYPE_MOVABLE, false);
appendMap(chargeMap, movpluralMap, TYPE_MOVABLE, true);
appendMap(chargeMap, immovableMap, TYPE_IMMOVABLE, false);
appendMap(chargeMap, impluralMap, TYPE_IMMOVABLE, true);

/*******************************************************
**PROTOTYPES (CHARGES ETC.)                           **
**prototypes for all of the objects used in heraldry: **
**charges (ordinary and moveable), divisions, fields  **
**all inheriting from the Node prototype              **
********************************************************/

function Division(type,number=2)
{
	Node.call(this);
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
		out+=divisions[this.type];
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

function Charge(type, index, tincture, number = 1)
{
	Node.call(this);
	this.type = type;
	this.index = index;
	this.number = number;
	this.append( tincture );
}

//set up inheritance
Charge.prototype=Object.create(Node.prototype);
Charge.prototype.constructor=Charge;

Charge.prototype.getName= function (){
	var out="";
	var list;
	var plist;
	if(this.type===TYPE_IMMOVABLE){
		list=immovables;
		plist=implurals;
	}else if(this.type===TYPE_MOVABLE){
		list=movables;
		plist=movplurals;
	}else{
		console.error("Display error: unknown charge category");
		return;
	}
	if (this.number===1)
	{
		out+="a ";
		out+=list[this.index];
	}else{
		out+=this.number.toString()+" ";
		out+=plist[this.index];
	}
	return out;
}

/**********************************
**TOKEN AND BUFFER FUNCTIONS     **
**Simple string buffer and token **
**generation therefrom           **
***********************************/

//mainly for informational purposes
TOK_WORD=0;
TOK_NUM=1;
TOK_PUNCT=2;

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

/**************************************
**TOKEN STREAM FUNCTIONS             **
**everything to do with token streams**
***************************************/

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

TokenStream.prototype.reset=function()
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

/*******************************
**IS-FUNCTIONS                **
**check if a token matches a  **
**particular semantic category**
********************************/

function isSingleDivision(tok)
{
	if( !isWord(tok) )
		{return false}
	if( (divisions.indexOf(tok.value) > -1) )
		{return true;}
	return false;
}

function isPluralDivision(tok)
{
	if( !isWord(tok) )
		{return false}
	if( (plurals.indexOf(tok.value) > -1) )
		{return true;}
	return false;
}

function isDivision(tok)
{
	if( !isWord(tok) )
		{return false}
	if( (divisions.indexOf(tok.value) > -1) )
		{return true;}
	if( (plurals.indexOf(tok.value) > -1) )
		{return true;}
	return false;
}

function isMovable(tok)
{
	if( !isWord(tok) )
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

function isCharge(tok)
{
	if( !isWord(tok) )
		{return false}
	if( (immovables.indexOf(tok.value) > -1) )
		{return true;}
	if( (implurals.indexOf(tok.value) > -1) )
		{return true;}
	if( (movables.indexOf(tok.value) > -1) )
		{return true;}
	if( (movplurals.indexOf(tok.value) > -1) )
		{return true;}
	return false;
}

function isImmovable(tok)
{
	if( !isWord(tok) )
		{return false}
	if( (immovables.indexOf(tok.value) > -1) )
		{return true;}
	if( (implurals.indexOf(tok.value) > -1) )
		{return true;}
	return false;
}

function immovableIndex(tok)
{
	var tmp=immovables.indexOf(tok.value);
	if( tmp > -1 )
		{return tmp;}
	var tmp=implurals.indexOf(tok.value);
	if( tmp > -1 )
		{return tmp;}
}

function isCharge(tok)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_WORD )
		{return false}
	if( (ordinaries.indexOf(tok.value) > -1) )
		{return true;}
	if( (subordinaries.indexOf(tok.value) > -1) )
		{return true;}
	if( (movables.indexOf(tok.value) > -1) )
		{return true;}
	if( (movplurals.indexOf(tok.value) > -1) )
		{return true;}
	return false;
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

function tinctureName(i){
	return titleCase(tinctures[i]);
}

//a linking word is a conjunction or preposition
function isLinking(tok)
{
	if( tok === undefined )
		{return false}
	if( tok.type !== TOK_WORD )
		{return false}
	if ( conjunctions.indexOf(tok.value) > -1 )
		{return true}
	if ( prepositions.indexOf(tok.value) > -1 )
		{return true}
	return false;
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

/**********************************************************
**PARSING FUNCTIONS                                      **
**functions to attempt to read a semantic construct      **
**of a given type (movable, ordinary, etc.) from a       **
**token string. On success they return a node            **
**representing the parsed object and move the token      **
**stream forward to after the object. On failure,        **
**they return undefined and leave the string unchanged   **
**semantically (internal state of the string may change) **
***********************************************************/

function getDefinedLength(arr){
	var count=0;
	for(var i=0; i<arr.length; ++i)
	{
		if(arr[i]!==undefined){
			++count;
		}
	}
	return count;
}

function getIndexToReturn(arr,type){
	//if there is no array, abort
	if(arr===undefined){
		return;
	}
	//if we've specified a type, return that type's ID, the index and the flag
	if(type!==undefined && arr[type]!==undefined){
		return [type].concat(arr[type]);
	}
	//if we've not specified a type, and there is no ambiguity, return [type,index,flag]
	if(type===undefined && getDefinedLength(arr)===1){
			for( var i=0; i<arr.length; ++i){
				if(arr[i]!==undefined){
					return [i].concat(arr[i]);
				}
			}
	}
	//otherwise, return undefined
	return;
}

function tryName(str, map, type){
	var stack=[];
	stack.push(map);
	var stackpos = 0;
	//var pos = str.savePos();
	while(str.peek()!==undefined && stack[stackpos].get(str.peek().value)!==undefined){
		stack.push( stack[stackpos].get(str.pop().value) );
		++stackpos;
	}

	//we check if there is non-ambiguous correct-type index to return
	var ret = getIndexToReturn(stack[stackpos].get(""), type);
	if( ret!==undefined ){
		return ret;
	}
	//wind back through the stream, looking for a valid name
	while( stackpos > 0 )
	{
		var ret = getIndexToReturn(stack[stackpos].get(""), type);
		if( ret!==undefined ){
			return ret;
		}
		--stackpos;
		str.rewind();
	}
	//if we made it to the bottom of the stack without finding anything, return undefined
	return;
}

function tryChargeName(str, type)
{
	return tryName(str, chargeMap, type);
}

function tryDivisionName(str)
{
	return tryName(str, divisionsMap, 0);
}

//either return a field, or leave the stream semantically unchanged
function getField(str)
{
	var nxt=str.peek();
	//if the next word is a tincture, we have a field
	if( isTincture(nxt) ){
		str.pop();
		return new Field( tinctureIndex(nxt) );
	}else if(nxt===undefined){
		console.error("Word "+str.pos.toString()+": unexpected end of blazon")
	}
}

//either return a division, or leave the stream semantically unchanged
function getDivision(str)
{
	if(str.peek()===undefined)
	{
		console.error("Word "+str.pos.toString()+": unexpected end of blazon")
		return
	}
	var number=0;
	var type=TOK_WORD;
	var pos=str.savePos();
	if( isThisWord(str.peek(), "tierced") ){
		str.pop();
		var tmp=getDivision(str);
		if(tmp !== undefined)
		{
			tmp.number=3;
			return tmp;
		}else{
			console.error("Word "+str.pos.toString()+": no such division")
			str.rewind(); //un-pop "tierced" if no division
			return;
		}
	}
	var name=tryDivisionName(str);
	if( name!==undefined ){
		type=name[1];
		if( !name[2] )//if this is a single division
		{
			//number defaults to two, saltire as a special case handled later
			number=2;
			return new Division(type,number);
		}else if( isThisWord(str.peek(), "of") ){ //if we have "of X"
			str.pop();//take "of" off the stack
			if( isNumber(str.peek()) )
			{
				number=str.pop().value; //we don't pop unless we see a number
			}else{
				console.error("Word "+str.pos.toString()+": not a number")
				//we return a division anyway if there is no number, disregarding the "of"
				//default number is 0, which means "an arbitrary amount"
			}
		}
		return new Division(type,number);
	}
}

//takes a token stream
function getFieldOrDivision(str, bare=false)
{
	var pos=str.savePos();
	
	var tmp=getField(str);
	if(tmp!==undefined)
	{
		return tmp;
	}
	tmp=getDivision(str)
	if(tmp!==undefined)
	{
		var subf=getEscutcheon(str); //get first tincture/sub-blazon
		if(subf!==undefined){
			//if we're parsing a bare division and find a sub-escutcheon, abort
			if(bare===true && subf.subnode.length!==0){
				str.loadPos(pos);
				return;
			}
			tmp.append(subf);
			i=2;
			while(i>0){ //if we have a tierced field, check for *two* more fields
				//if we have a linking word or punctuation, look for a second/third tincture
				if( isLinking(str.peek()) || isPunct(str.peek()) ){
					str.pop();
					//if the previous sub-blazon was only a tincture, we only get a tincture. Otherwise an entire sub-blazon.
					//note that if bare===true then the previous sub-blazon was a bare tincture or we'd have aborted
					if(subf.subnode.length > 0){
						subf=getEscutcheon(str);
					}else{
						subf=getField(str);
					}
					if(subf!==undefined){
						//if this is a tierced shield, the *middle* field comes first, then the dexter
						if(tmp.number===3 && i===2){
							tmp.append(tmp.subnode[0]);
							tmp.subnode[0]=subf;
						}else{
							tmp.append(subf);
						}
					}else{
						//if we can't parse enough fields, reset the token stream and abort
						str.loadPos(pos);
						return;
					}
				}else{
					//if there is no linking word, reset the token stream and abort
					str.loadPos(pos);
					return;
				}
				//don't check for a third field if not tierced
				if(tmp.number===3){
					i-=1;
				}else{
					i=0;
				}
			}
		}else{
			//if there is no valid tincture, reset the token stream and abort
			str.loadPos(pos);
			return;
		}
		return tmp;
	}
}

//type is optional and should not generally be used explicitly
function getCharge(str, type)
{
	var number=0; //not possible, error if this does not change
	var type; //defults to undefined
	var index;
	var tincture=new Field(0); //defaults to "specified later"
	var ret;
	var pos=str.savePos(); //save our place in the token stream so we can exit without changing anything
	if( isNumber(str.peek()) ){
		number=str.pop().value;
		var name = tryChargeName(str, type);
		if( name!==undefined ){
			type=name[0];
			index=name[1];
			while( isWord(str.peek()) ){
				//if we see a linking word, end the charge
				if( isLinking(str.peek()) ){
					break;
				}
				//checmk for a colouring
				var tmp=getFieldOrDivision(str,true);
				//colouring is optional, but ends the charge if it exists
				if( tmp!==undefined )
				{
					tincture=tmp;
					break;
				}
				str.pop();//ensure we don't get stuck in an infinite loop
			}
			ret = new Charge(type, index, tincture, number);
			//if we see a linking word
			if( isLinking( str.peek() ) )
			{
				var linkpos=str.savePos();
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
							str.pop();//un-pop the conjunction
						}
					}
					
					//if the ordinary is amidst one group of charges, split the group in half as  evenly as possible
					if(scrg===undefined){
						var hlf=Math.floor(crg.number/2);
						var gtr=crg.number-hlf;
						//place larger half above/dexter of the ordinary, lesser half below/sinister
						ret.append( new Charge(TYPE_MOVABLE, crg.index, crg.subnode[0], gtr) );
						ret.append( new Charge(TYPE_MOVABLE, crg.index, crg.subnode[0], hlf) );
					}else{ //otherwise just place it beteeen the two groups of charges
						ret.append( crg );
						ret.append( scrg );
					}

				}else if( isThisWord(lwrd, "and") ){
					str.loadPos(linkpos); //rewind to the word "and", deal with second charge later
				}else{
					str.loadPos(linkpos); //rewind to linking word
					console.error("Word "+str.pos.toString()+": preposition not implemented");
				}
				
			}
			return ret;
		}else{
			//console.error("Word "+str.pos.toString()+": '" +str.peek().value+"' is not an ordinary");
			str.rewind(); //un-pop the number
			return;
		}
	}else if( isThisWord(str.peek(), "on") ){
		str.pop();
		ret=getCharge(str);
		if( ret===undefined ){
			str.rewind();//un-pop the "on"
			return;
		}
		//charge on the ordinary itself becomes a subnode of its tincture
		var mov=getMovable(str);
		if(mov!==undefined){
			ret.subnode[0].append(mov);
			return ret;
		}else{
			console.error("Word "+str.pos.toString()+": no movable charge where one was expected");
		}
	}else{
		//console.error("Word "+str.pos.toString()+": no number where one was expected");
		return;
	}
}

function getMovable(str)
{
	return getCharge(str, TYPE_MOVABLE);
}

function getImmovable(str)
{
	return getCharge(str, TYPE_IMMOVABLE);
}

function getEscutcheon(str){
	var tmp=getFieldOrDivision(str);
	var flag=false;
	//skip a comma if one is present after the field
	if( isPunct(str.peek()) ){
		str.pop();
		flag=true;
	}
	var crg=getCharge(str);
	if(crg!==undefined){
		tmp.append(crg);
	}else if(flag) {
		str.rewind(); //un-pop the comma if there was no charge and one was popped
	}
	return tmp;
}

/*****************
**MISC FUNCTIONS**
******************/

function titleCase(str){
	return str.charAt(0).toUpperCase() + str.slice(1);
}

/******************
**DEBUG FUNCTIONS**
*******************/

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

function displayTree(root){
	document.getElementById("displayPara").innerHTML=root.display();
}

root=new Field(14);
root.append(new Charge(0, 1, new Field(4), 1));
//root.subnode[0].append(new Node());
root.subnode[0].subnode[0].append(new NamedNode("the words \"Syntax Test\" Argent"));
root.subnode[0].subnode[0].append(new Charge(0, 10, new Field(2), 1));
root.subnode[0].subnode[0].subnode[1].append(new NamedNode("a console Sable"));
//this works
document.getElementById("displayPara").innerHTML=root.display();
