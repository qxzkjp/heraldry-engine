/*******************************************************
**PROTOTYPES (NODES)                                  **
**prototypes for named and unnamed nodes in a simple  **
**non-binary tree structure. includes naming and      **
**display functions.                                  **
********************************************************/

function TreeNode()
{
	this.subnode= [];
}

TreeNode.prototype.getName= function (){
	return "(unnamed)";
}

//depth puts hyphens before the name, for tree-printing
TreeNode.prototype.display=function (depth=0)
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

TreeNode.prototype.append = function(newTreeNode)
{
	this.subnode.push(newTreeNode);
}

TreeNode.prototype.at = function(index)
{
	return this.subnode[index];
}

TreeNode.prototype.clone = function(newTreeNode)
{
	var ret;
	if(newTreeNode instanceof TreeNode){
		ret = newTreeNode;
	}else{
		ret = new TreeNode();
	}
	ret.subnode = cloneSubnode(this.subnode);
}

function cloneSubnode(arr){
	var ret=[];
	for (var node of arr){
		ret.push(node.clone());
	}
	return ret;
}

function NamedTreeNode(name)
{
	TreeNode.call(this);
	this.name=name;
}

//set up inheritance
NamedTreeNode.prototype=Object.create(TreeNode.prototype);
NamedTreeNode.prototype.constructor=NamedTreeNode;

NamedTreeNode.prototype.getName= function (){
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
			addToMap(ret, arr[i], [i]);
		}
	}
	return ret;
}

function appendMap(mapbase, mapapp, type, flag)
{
	for( var [key, value] of mapapp )
	{
		if(value instanceof Array){
			if(mapbase.get(key)===undefined){
				mapbase.set(key, []);
			}
			if( type!==undefined && flag!==undefined){
				mapbase.get(key)[type]=[value[0], flag];
			}else{
				mapbase.get(key)=[value[0]];
			}
		}else{
			if(mapbase.get(key)===undefined){
				mapbase.set(key, new Map);
			}
			appendMap(mapbase.get(key), value, type, flag);
		}
	}
}

function mapScale(A, c){
	var B = new map();
	for (var [keyA, valueA] of A){
		if(keyA===""){
			B.set(keyA, valueA * c);
		}else{
			B.set(keyA, mapScale(valueA, c));
		}
	}
	return B;
}

function mapShift(A, c){
	var B = new Map();
	for (var [keyA, valueA] of A){
		if(keyA===""){
			B.set(keyA, [valueA[0] + c]);
		}else{
			B.set(keyA, mapShift(valueA, c));
		}
	}
	return B;
}

function mapProduct(A, B, dimB)
{
	var C = new Map();
	for (var [keyA, valueA] of A){
		if(keyA===""){
			C.set(keyA, [valueA[0] * (dimB+1)]);
			for (var [keyB, valueB] of B){
				C.set(keyB, mapShift(valueB, valueA[0] * (dimB+1) + 1)); //+1 because get("") is already +0
			}
		}else{
			C.set(keyA, mapProduct(A.get(keyA), B, dimB));
		}
	}
	return C;
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

//"unspecified" is not a real tincture (duh), but is used as a placeholder. It must be in position 0.
tinctures = ["unspecified", "argent", "or", "gules", "azure", "vert", "purpure", "sable", "tenny", "sanguine", "vair", "countervair", "potent", "counterpotent", "ermine", "ermines", "erminois", "pean"];

//chevron is immovable, while "chevrons" is moveable: strange hack, but it might work
immovables = ["chief", "pale", "fess", "bend", "bend sinister", "chevron", "saltire", "pall", "cross", "pile", "bordure", "orle", "tressure", "canton", "flanches", "gyron", "fret"];
implurals = ["", "pallets", "bars", "bendlets", "bendlets sinister", "chevronels", "", "", "", "pile", "", "", "", "", "", "", ""];

movables = ["mullet", "phrygian cap", "fleur-de-lis", "pheon", "moveable-chevron", "inescutcheon", "billet", "lozenge", "key", "phrygian cap with bells on"];
movplurals = ["mullets", "phrygian caps", "fleurs-de-lis", "pheons", "chevrons", "inescutcheons", "billets", "lozenges", "keys", "phrygian caps with bells on"];

beasts = ["lion", "eagle", "bear", "dragon", "stag"];
beastplurals = ["lions", "eagles", "bears", "dragons", "stags"];

attitudes = ["rampant", "statant", "couchant", "passant", "salient", "sejant", "sejant erect", "cowed", "displayed"];

ATT_RAMPANT = 0;
console.assert(attitudes[ATT_RAMPANT]==="rampant")

facings = ["guardant", "reguardant"];
directions = ["to dexter", "to sinister", "affronte", "en arriere"];
//affronté, arrière

DIR_DEXTER = 0;
DIR_SINISTER = 1;
console.assert(directions[DIR_DEXTER]==="to dexter")
console.assert(directions[DIR_SINISTER]==="to sinister")

//armed=claws, langued=tongue, attired=antlers, unguled=hooves
beastColours=["armed", "langued", "attired", "unguled"];

conjunctions = ["and"];
prepositions = ["on", "between", "above", "below", "within", "overall"];
numbers=["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve"];

arrangements=["unspecified", "combined", "two and one", "one and two", "addorsed", "confronte", "in saltire", "in pale", "in fess", "in bend", "in bend sinister"];

ARR_COMBINED=1;
ARR_FESS=8;
ARR_BEND=9;
ARR_SINISTER=10;
console.assert(arrangements[ARR_COMBINED]==="combined")
console.assert(arrangements[ARR_FESS]==="in fess")
console.assert(arrangements[ARR_BEND]==="in bend")
console.assert(arrangements[ARR_SINISTER]==="in bend sinister")

//set up "dual" arrangements -- those which involve two charges in diferent orientations
ARR_SALTIRE=6;
console.assert(arrangements[ARR_SALTIRE]==="in saltire")
ARR_ADDORSED=4;
console.assert(arrangements[ARR_ADDORSED]==="addorsed")
ARR_CONFRONTE=5;
console.assert(arrangements[ARR_CONFRONTE]==="confronte")
dualArrangements=[ARR_SALTIRE, ARR_ADDORSED, ARR_CONFRONTE]

function isDualArrangement(arrangement){
	if( dualArrangements.indexOf(arrangement) > -1 ){
		return true;
	}else{
		return false;
	}
}

orientations=["palewise", "bendwise", "fesswise", "bendwise sinister inverted", "palewise inverted and reversed", "bendwise inverted and reversed", "fesswise inverted and reversed", "bendwise sinister reversed"];
mirror_orients=["palewise reversed", "bendwise reversed", "fesswise inverted", "bendwise sinister inverted and reversed", "palewise inverted", "bendwise inverted", "fesswise reversed", "bendwise sinister"];

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

var PALE_MIRRORED=false;
var BEND_MIRRORED=false;
var FESS_MIRRORED=false;
var SINISTER_INV_MIRRORED=false;
var PALE_INV_REV_MIRRORED=false;
var BEND_INV_REV_MIRRORED=false;
var FESS_INV_REV_MIRRORED=false;
var SINISTER_REV_MIRRORED=false;

//type constants for charges
TYPE_IMMOVABLE = 0;
TYPE_MOVABLE = 1;
TYPE_BEAST = 2;
TYPE_GROUP = 3;

//set up token maps
divisionsMap = new Map();
appendMap(divisionsMap, setupMap(divisions), 0, false);
appendMap(divisionsMap, setupMap(plurals), 0, true);

immovableMap = setupMap(immovables);
impluralMap = setupMap(implurals);

movableMap = setupMap(movables);
movpluralMap = setupMap(movplurals);

beastsMap = setupMap(beasts);
beastpluralsMap = setupMap(beastplurals);

attMap=setupMap(attitudes);
facMap=setupMap(facings);

directionsMap=setupMap(directions);

halfOrientMap=setupMap(orientations);
mirrorOrientMap=setupMap(mirror_orients);
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

addSynonym(attMap, "rampant", "forcené");
addSynonym(attMap, "rampant", "forcene");
addSynonym(attMap, "sejant erect", "sejant rampant");
addSynonym(attMap, "sejant erect", "sejant-rampant");

//possibly not the most elegant way of handling the orientations, but we only need eight manual synonyms so it'll do
addSynonym(mirrorOrientMap, "palewise inverted", "inverted");
addSynonym(mirrorOrientMap, "palewise reversed", "reversed");
addSynonym(halfOrientMap, "palewise inverted and reversed", "inverted and reversed");
addSynonym(halfOrientMap, "palewise inverted and reversed", "reversed and inverted");

addSynonym(halfOrientMap, "fesswise inverted and reversed", "fesswise reversed and inverted");
addSynonym(halfOrientMap, "palewise inverted and reversed", "palewise reversed and inverted");
addSynonym(halfOrientMap, "bendwise inverted and reversed", "bendwise reversed and inverted");
addSynonym(mirrorOrientMap, "bendwise sinister inverted and reversed", "bendwise sinister reversed and inverted");


//combine charge maps
chargeMap=new Map;
appendMap(chargeMap, movableMap, TYPE_MOVABLE, false);
appendMap(chargeMap, movpluralMap, TYPE_MOVABLE, true);
appendMap(chargeMap, immovableMap, TYPE_IMMOVABLE, false);
appendMap(chargeMap, impluralMap, TYPE_IMMOVABLE, true);
appendMap(chargeMap, beastsMap, TYPE_BEAST, false);
appendMap(chargeMap, beastpluralsMap, TYPE_BEAST, true);

//combine orientation maps
orientationsMap = new Map();
appendMap(orientationsMap, halfOrientMap, 0, false);
appendMap(orientationsMap, mirrorOrientMap, 0, true);

//combine attitude and sub-attitude maps in a special way
attitudesMap=mapProduct(attMap, facMap, facings.length);

/*******************************************************
**PROTOTYPES (CHARGES ETC.)                           **
**prototypes for all of the objects used in heraldry: **
**charges (ordinary and moveable), divisions, fields  **
**all inheriting from the TreeNode prototype          **
********************************************************/

function Division(type,number=2)
{
	TreeNode.call(this);
	this.type=type;
	this.number=number;
}

//set up inheritance
Division.prototype=Object.create(TreeNode.prototype);
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

Division.prototype.clone = function(){
	var ret = new Division(this.type, this.number);
	TreeNode.prototype.clone.call(this, ret);
	//ret.subnode = cloneSubnode(this.subnode);
}

function Field(tincture)
{
	TreeNode.call(this);
	this.tincture=tincture;
}

//set up inheritance
Field.prototype=Object.create(TreeNode.prototype);
Field.prototype.constructor=Field;

Field.prototype.getName= function (){
	return tinctureName(this.tincture);
}

Field.prototype.clone = function(){
	var ret = new Field(this.tincture);
	TreeNode.prototype.clone.call(this, ret);
	return ret;
}

function Charge(type, index, tincture, number = 1, orientation=0, mirrored=false, arrangement=0)
{
	TreeNode.call(this);
	this.setatt(type, index, number, orientation, mirrored, arrangement);
	//if we have a charge group, there is no tincture
	if( type!==TYPE_GROUP ){
		if(tincture===undefined){
			this.append( new Field(0) )
		}else if(tincture!==null){
			this.append( tincture );
		}
	}
	//tincture === null means "do not append tincture".
	//Do not use this unless you are manually constructing a tincture immediately afterwards
}

//set up inheritance
Charge.prototype=Object.create(TreeNode.prototype);
Charge.prototype.constructor=Charge;

Charge.prototype.setatt = function(type, index, number, orientation, mirrored, arrangement)
{
	if( type!==undefined ){
		this.type = type;
	}
	if( index!==undefined ){
		this.index = index;
	}
	if( number!==undefined ){
		this.number = number;
	}
	if( orientation!== undefined ){
		this.orientation = orientation;
	}
	if( mirrored!==undefined ){
		this.mirrored = mirrored;
	}
	if( arrangement!==undefined ){
		this.arrangement = arrangement;
	}
}

Charge.prototype.clone= function(){
	var ret = new Charge(this.type, this.index, null, this.number, this.orientation, this.mirrored, this.arrangement);
	TreeNode.prototype.clone.call(this, ret);
	return ret;
}

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
	if( this.orientation!==0 || this.mirrored){
		out+=" ";
		if(this.mirrored){
			out+=mirror_orients[this.orientation];
		}else{
			out+=orientations[this.orientation];
		}
	}
	if( this.arrangement !== 0 )
	{
		out += " " + arrangements[this.arrangement];
	}
	return out;
}

//"orientation" is attitude
function ChargeGroup(arrangement=0)
{
	Charge.call(this, TYPE_GROUP, 0, null, 0, 0, false, arrangement);
}

//set up inheritance
ChargeGroup.prototype=Object.create(Charge.prototype);
ChargeGroup.prototype.constructor=ChargeGroup;

ChargeGroup.prototype.clone = function(){
	var ret = new ChargeGroup(this.arrangement);
	TreeNode.prototype.clone.call(this, ret);
	return ret;
}

ChargeGroup.prototype.getName = function(){
	var out="";
	out+="(charge group";
	if( this.arrangement!==0 ){
		out+=" " + arrangements[this.arrangement]; 
	}
	out+=")"
	return out;
}

//"orientation" is attitude
function Beast(index, tincture, number = 1, orientation=0, direction=0, arrangement=0)
{
	Charge.call(this, TYPE_BEAST, index, tincture, number, orientation, false, arrangement);
	this.direction = direction;
}

//set up inheritance
Beast.prototype=Object.create(Charge.prototype);
Beast.prototype.constructor=Beast;

Beast.prototype.clone = function(){
	var ret = new Beast(this.index, null, this.number, this.orientation, this.direction, this.arrangement);
	TreeNode.prototype.clone.call(this, ret);
	return ret;
}

Beast.prototype.getName= function (){
	var out="";
	if (this.number===1)
	{
		out+="a ";
		out+=beasts[this.index];
	}else{
		out+=this.number.toString()+" ";
		out+=beastplurals[this.index];
	}
	var N=facings.length+1;
	var att = Math.floor(this.orientation/N);
	var fac = this.orientation % N;
	if(att !== 0){
		out += " " + attitudes[att];
	}
	if(fac !== 0){
		out += " " + facings[fac - 1];
	}
	if(this.direction !== 0){
		out += " " + directions[this.direction];
	}
	if(this.arrangement !== 0 )
	{
		out += " " + arrangements[this.arrangement];
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
	while(true){
		var tmp;
		if(str.peek()!==undefined){
			//if we've popped a number, turn it into a word
			if(str.peek().type===TOK_NUM){
				tmp=numbers[str.peek().value];
			}else{
				tmp=str.peek().value;
			}
		}else{
			break;
		}
		//if the next word is in the tree, climb down and continue looping
		if( stack[stackpos].get(tmp)!==undefined ){
			str.pop();
			stack.push( stack[stackpos].get(tmp) );
			++stackpos;
		}else{
			break;
		}
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

function tryOrientationName(str)
{
	return tryName(str, orientationsMap, 0);
}

function tryAttitudeName(str)
{
	return tryName(str, attitudesMap, 0);
}

function tryDirectionName(str)
{
	return tryName(str, directionsMap, 0);
}

function tryArrangementName(str)
{
	return tryName(str, arrangementsMap, 0);
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
	var ret;
	var number=0; //not possible, error if this does not change
	var index;
	var tincture=new Field(0); //defaults to "specified later"
	var orientation;
	var mirrored=false;
	var direction=0;
	var arrangement=0;
	var pos=str.savePos(); //save our place in the token stream so we can exit without changing anything
	if( isNumber(str.peek()) ){
		number=str.pop().value;
		var name = tryChargeName(str, type);//if type is undefined, we'll get any charge
		if( name!==undefined ){
			//if we found a charge, set type and index...
			type=name[0];
			index=name[1];
			//...then parse through modifiers in a loop
			while( isWord(str.peek()) ){
				//check for a colouring
				var tmp=getFieldOrDivision(str,true);
				//colouring is optional, but ends the charge if it exists
				if( tmp!==undefined )
				{
					tincture=tmp;
					break;
				}
				
				tmp=tryArrangementName(str);
				if( tmp!==undefined )
				{
					if(type===TYPE_IMMOVABLE){
						console.error("Semantic error: immovable charges cannot be arranged");
					}else{
						arrangement=tmp[1];
						//if a group is arranged bendwise or bendwise sinister, by default the charges should be oriented likewise
						if(orientation === undefined){
							if(arrangement === ARR_BEND){
								orientation = ORIENT_BEND;
							}else if(arrangement === ARR_SINISTER){
								orientation = ORIENT_SINISTER;
							}
						}
					}
					continue;
				}
				
				tmp=tryOrientationName(str);
				if( tmp!==undefined )
				{
					if(type!==TYPE_MOVABLE){
						console.error("Semantic error: beasts and immovable charges cannot be oriented");
					}else{
						orientation=tmp[1];
						mirrored=tmp[2];
					}
					continue;
				}
				
				tmp=tryAttitudeName(str);
				if( tmp!==undefined )
				{
					if(type!==TYPE_BEAST){
						console.error("Semantic error: only beasts can have attitude");
					}else{
						orientation=tmp[1];
					}
					continue;
				}
				
				tmp=tryDirectionName(str);
				if( tmp!==undefined )
				{
					if(type!==TYPE_BEAST){
						console.error("Semantic error: only beasts can have attitude");
					}else{
						direction=tmp[1];
					}
					continue;
				}
				
				//if we see a linking word, end the charge
				if( isLinking(str.peek()) ){
					break;
				}
				//if no token was recognised, end the charge
				break;
			}
			//if we have a dual arrangement, create a charge group with two charges
			if( isDualArrangement(arrangement) ){
				ret=new ChargeGroup();
				var orientA, orientB, mirrorA, mirrorB, directA, directB;
				if( arrangement===ARR_SALTIRE ){
					orientA = ORIENT_BEND;
					mirrorA = BEND_MIRRORED;
					orientB = ORIENT_SINISTER;
					mirrorB = SINISTER_MIRRORED;
					ret.arrangement=ARR_COMBINED;
					if(type===TYPE_BEAST){
						console.error("Semantic error: beasts cannot be in saltire");
						str.loadPos(pos);
						return;
					}
				}else if( arrangement===ARR_ADDORSED ){
					orientA = ORIENT_PALE;
					mirrorA = PALE_MIRRORED;
					directA = DIR_DEXTER;
					orientB = ORIENT_PALE_REV;
					mirrorB = PALE_REV_MIRRORED;
					directB = DIR_SINISTER;
					ret.arrangement=ARR_FESS;
				}else if( arrangement===ARR_CONFRONTE ){
					orientA = ORIENT_PALE_REV;
					mirrorA = PALE_REV_MIRRORED;
					directA = DIR_SINISTER;
					orientB = ORIENT_PALE;
					mirrorB = PALE_MIRRORED;
					directB = DIR_DEXTER;
					ret.arrangement=ARR_FESS;
				}
				ret.append( createCharge(type, index, tincture, 1, orientA, mirrorA, direction, 0) );
				ret.append( createCharge(type, index, tincture, 1, orientB, mirrorB, direction, 0) );
			}else{
				//all attributes read, create the actual charge
				ret = createCharge(type, index, tincture, number, orientation, mirrored, direction, arrangement);
			}
			//if we see a linking word
			if( isLinking( str.peek() ) )
			{
				var linkpos=str.savePos();
				var lwrd = str.pop();
				var crg=getMovableOrBeast(str);
				var scrg;
				if( crg===undefined ){
					str.rewind();//un-pop the linking word
				}else if( isThisWord(lwrd, "between") ){
					//check for a second charge for it to be between
					if( isThisWord(str.peek(), "and") ){
						str.pop();
						var scrg=getMovableOrBeast(str);
						if( scrg===undefined )
						{
							str.pop();//un-pop the conjunction
						}
					}
					
					//if the ordinary is amidst one group of charges, split the group in half as  evenly as possible
					if(scrg===undefined){
						//if we have a charge group, take the individual charges
						if(crg.type===TYPE_GROUP){
							if(crg.subnode.length === 2){
								scrg = crg.at(1);
								crg=crg.at(0);
							}else{
								console.error("Parsing error: can only split group with two charges");
								scrg=newChargeGroup();
							}
						}else{
							var hlf=Math.floor(crg.number/2);
							var gtr=crg.number-hlf;
							scrg = crg.clone();
							//place larger half above/dexter of the ordinary, lesser half below/sinister
							crg.number = gtr;
							scrg.number = hlf;
						}
					}
					//immovable charges can just have sub-charges on them, otherwise create a charge group
					if(type===TYPE_IMMOVABLE){
						ret.append( crg );
						ret.append( scrg );
					}else{
						tmp=ret;
						ret=new ChargeGroup(ARR_FESS);
						ret.append( crg );
						ret.append( tmp );
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
		var mov=getMovableOrBeast(str);
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

function createCharge(type, index, tincture, number, orientation, mirrored, direction, arrangement){
	var ret;
	if(type!==TYPE_BEAST){
		ret = new Charge(type, index, tincture, number, orientation, mirrored, arrangement);
	}else{
		ret = new Beast(index, tincture, number, orientation, direction, arrangement);
	}
	return ret;
}

function getMovable(str)
{
	return getCharge(str, TYPE_MOVABLE);
}

function getBeast(str)
{
	return getCharge(str, TYPE_BEAST);
}

function getMovableOrBeast(str)
{
	var ret = getMovable(str);
	if(ret === undefined){
		ret = getBeast(str)
	}
	return ret;
}

function getImmovable(str)
{
	return getCharge(str, TYPE_IMMOVABLE);
}

function getEscutcheon(str){
	var tmp=getFieldOrDivision(str);
	if(tmp===undefined){
		console.error("Syntax error: no field (or division) where one was expected");
		return undefined;
	}
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
	if(root===undefined){
		console.error("Debug error: no tree to print (probably due to a catastrophic parsing error)");
	}else{
		document.getElementById("displayPara").innerHTML=root.display();
	}
}

function displayTree(root){
	document.getElementById("displayPara").innerHTML=root.display();
}

var root;
