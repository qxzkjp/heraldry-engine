/*******************************************************
**PROTOTYPES (NODES)                                  **
**prototypes for named and unnamed nodes in a simple  **
**non-binary tree structure. includes naming and      **
**display functions.                                  **
********************************************************/

function TreeNode(a)
{
    if (a instanceof TreeNode) {
        var hasActive = false;
        var activePos = [];
        if (a.active === a) {
            this.active = this;
        } else if (a.active == null) {
            this.active = null;
        } else {
            hasActive = true;
            activePos = a.saveActiveNode();
        }
        this.subnode = []
        if (a.subnode instanceof Array) {
            for (var node of a.subnode) {
                this.append(node.clone());
            }
        }
        if (hasActive) {
            this.restoreActiveNode(activePos);
        }
        this.parent = null;
    } else {
        this.active = this;
        this.subnode = [];
        this.parent = null;
    }
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

TreeNode.prototype.append = function(other)
{
    if (other.parent !== null) {
        var index = other.parent.subnode.indexOf(other);
        other.parent.subnode.splice(index, 1);
    }
    other.parent = this;
	this.subnode.push(other);
}

TreeNode.prototype.push = TreeNode.prototype.append;

TreeNode.prototype.pop = function () {
    var ret = this.subnode.pop();
    ret.parent = null;
    return ret;
}

TreeNode.prototype.replace = function (index, nodeIn) {
    if (index instanceof TreeNode) {
        var parent = index.parent;
        parent.replace(parent.subnode.indexOf(index), nodeIn);
    } else {
        if (this.subnode[index] !== undefined) {
            this.subnode[index].parent = null;
        }
        this.subnode[index] = nodeIn;
        this.subnode[index].parent = this;
    }
}

TreeNode.prototype.at = function(index)
{
    if (index >= 0) {
        return this.subnode[index];
    } else {
        return this.subnode[this.subnode.length + index];
    }
}

TreeNode.prototype.clone = function(other)
{
    if (other === undefined) {
        return new this.constructor(this);
    }
    return other.cloneOnto(this);
}

TreeNode.prototype.cloneOnto = function(other) {
    this.constructor.call(other, this);
    Object.setPrototypeOf(other, this.constructor.prototype);
}

TreeNode.prototype.findNode = function (child) {
    var ret = [];
    var currentNode = child;
    var nextNode;
    while (true) {
        if (currentNode == this) {
            return ret;
        }
        nextNode = currentNode.parent;
        if (nextNode === null) {
            return false;
        }
        ret.push(nextNode.subnode.indexOf(currentNode));
        currentNode = nextNode;
    }
}

TreeNode.prototype.isParentOf = function (child) {
    if (!(child instanceof TreeNode)) {
        return false;
    }
    var currentNode = child;
    while (true) {
        if (currentNode.parent == this) {
            return true;
        }
        if (currentNode.parent === null) {
            return false;
        }
        currentNode = currentNode.parent;
    }
}

TreeNode.prototype.saveActiveNode = function () {
    return this.findNode(this.active);
}

TreeNode.prototype.restoreActiveNode = function (arr) {
    var tmp = arr.slice(0);
    var currentNode = this;
    while (true) {
        if (tmp.length === 0) {
            this.active = currentNode;
            return;
        }
        currentNode = currentNode.at(tmp.pop());
    }
}

TreeNode.prototype.setActiveNode = function(child) {
    if (child instanceof Array) {
        this.restoreActiveNode(child);
    } else if( this.isParentOf(child) || child === this) {
        this.active = child;
    }
}

TreeNode.prototype.getActiveNode = function () {
    return this.active;
}

function NamedTreeNode(name)
{
    if (name instanceof NamedTreeNode) {
        TreeNode.call(this, name);
        this.name = name.name;
    } else {
        TreeNode.call(this);
        this.name = name;
    }
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
tinctures = ["unspecified", "argent", "or", "gules", "azure", "vert", "purpure", "sable", "tenny", "brown", "sanguine", "vair", "countervair", "potent", "counterpotent", "ermine", "ermines", "erminois", "pean", "counterchanged"];

var TINCT_UNSPECIFIED = 0;

//chevron is immovable, while "chevrons" is moveable: strange hack, but it might work
immovables = ["chief", "pale", "fess", "bend", "bend sinister", "chevron", "saltire", "pall", "cross", "pile", "bordure", "orle", "tressure", "canton", "flanches", "gyron", "fret"];
implurals = ["", "pallets", "bars", "bendlets", "bendlets sinister", "chevronels", "", "", "", "pile", "", "", "", "", "", "", ""];

var CHARGE_BEND = 3;
var CHARGE_SINISTER = 4;

movables = ["mullet", "phrygian cap", "fleur-de-lis", "pheon", "moveable-chevron", "inescutcheon", "billet", "lozenge", "key", "phrygian cap with bells on", "roundel", "mullet of six points", "ermine spot"];
movplurals = ["mullets", "phrygian caps", "fleurs-de-lis", "pheons", "chevrons", "inescutcheons", "billets", "lozenges", "keys", "phrygian caps with bells on", "roundels", "mullets of six points", "ermine spots"];

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
    if (type instanceof Division) {
        TreeNode.call(this, type);
        this.type = type.type;
        this.number = type.number;
    } else {
        TreeNode.call(this);
        this.type = type;
        this.number = number;
    }
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

function Field(tincture)
{
    if (tincture instanceof Field) {
        TreeNode.call(this, tincture);
        this.tincture = tincture.tincture;
    } else {
        TreeNode.call(this);
        this.tincture = tincture;
    }
}

//set up inheritance
Field.prototype=Object.create(TreeNode.prototype);
Field.prototype.constructor=Field;

Field.prototype.getName= function (){
	return tinctureName(this.tincture);
}

function Charge(type, index, tincture, number = 1, orientation=0, mirrored=false, arrangement=0)
{
    if (type instanceof Charge) {
        TreeNode.call(this, type);//this clones all the subnodes as well
        this.setatt(type.type, type.index, type.number, type.orientation, type.mirrored, type.arrangement);
    } else {
        TreeNode.call(this);
        this.setatt(type, index, number, orientation, mirrored, arrangement);
        //if we have a charge group, there is no tincture
        if (type !== TYPE_GROUP) {
            if (tincture === undefined) {
                this.append(new Field(0))
            } else if (typeof tincture === "number") {
                this.append(new Field(tincture));
            } else if (tincture !== null) {
                this.append(tincture);
            }
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
    if (arrangement instanceof ChargeGroup) {
        Charge.call(this, arrangement);
    } else {
        Charge.call(this, TYPE_GROUP, 0, null, 0, 0, false, arrangement);
    }
}

//set up inheritance
ChargeGroup.prototype=Object.create(Charge.prototype);
ChargeGroup.prototype.constructor=ChargeGroup;

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
    if (index instanceof Beast) {
        Charge.call(this, index);
        this.direction = index.direction;
    } else {
        Charge.call(this, TYPE_BEAST, index, tincture, number, orientation, false, arrangement);
        this.direction = direction;
    }
}

//set up inheritance
Beast.prototype=Object.create(Charge.prototype);
Beast.prototype.constructor=Beast;

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
function getField(str, tree, success)
{
    success[0] = false;
	var nxt=str.peek();
	//if the next word is a tincture, we have a field
    if (isTincture(nxt)) {
        var tinct = tinctureIndex(nxt);
        str.pop();
        var tIdx;
        if (tinct != TINCT_UNSPECIFIED) {
            tinctureStack.push(tinct);
            tIdx = tinctureStack.length - 1;
        } else {
            //if tincture is unspecified, we give the index to the *next* tincture to be parsed (one after the current end of the stack)
            tIdx = tinctureStack.length;
        }
        if (tree !== undefined) {
            tree.getActiveNode().push(new Field(tIdx));
            //tree.getActiveNode().push(new Field(nxt));
            //tree.setUnspecifiedTinctures(nxt);
        } else {
            tree = new Field(tIdx);
            //tree = new Field(nxt);
        }
        success[0] = true;
	}else if(nxt===undefined){
		console.error("Word "+str.pos.toString()+": unexpected end of blazon")
    }
    return tree;
}

//either return a division, or leave the stream semantically unchanged
function getDivision(str, tree, success)
{
    success[0] = false;
	if(str.peek()===undefined)
	{
		console.error("Word "+str.pos.toString()+": unexpected end of blazon")
        return tree;
	}
	var number=0;
	var type=TOK_WORD;
    var pos = str.savePos();
    var oldStack = tinctureStack.slice(0);
    if (isThisWord(str.peek(), "tierced")) {
        str.pop();
        var succ = [false];
        tree = getDivision(str, tree, succ);
        if (succ[0]) {
            tree.getActiveNode.at(-1).number = 3;
            success[0] = true;
        } else {
            console.error("Word " + str.pos.toString() + ": no such division")
            str.rewind(); //un-pop "tierced" if no division
        }
    } else {
        var name = tryDivisionName(str);
        if (name !== undefined) {
            type = name[1];
            if (!name[2])//if this is a single division
            {
                //number defaults to two, saltire as a special case to be handled later
                number = 2;
            } else if (isThisWord(str.peek(), "of")) { //if we have "of X"
                str.pop();//take "of" off the stack
                if (isNumber(str.peek())) {
                    number = str.pop().value; //we don't pop unless we see a number
                } else {
                    console.error("Word " + str.pos.toString() + ": not a number")
                    //we return a division anyway if there is no number, disregarding the "of"
                    //default number is 0, which means "an arbitrary amount"
                }
            }
            if (tree !== undefined) {
                tree.getActiveNode().push(new Division(type, number));
            } else {
                tree=new Division(type, number);
            }
            success[0] = true;
        }
    }
    if (!success[0]){
        str.loadPos(pos);
        tinctureStack = oldStack.slice(0);
    }
    return tree;
}

function isBareFieldOrDivision(tree) {
    if (tree instanceof Field) {
        return tree.subnode.length === 0;
    } else if (tree instanceof Division) {
        flag = true;
        for (field of tree.subnode) {
            flag = flag && (field instanceof Field) && isBareFieldOrDivision(field);
        }
        return flag;
    } else {
        return false;
    }
}

function isBareField(tree) {
    if (tree instanceof Field) {
        return tree.subnode.length === 0;
    } else {
        return false;
    }
}

//takes a token stream
function getFieldOrDivision(str, tree, success, bare=false)
{
    success[0] = true;
    var oldStack = tinctureStack.slice(0);
	var pos=str.savePos();
    var ret;
    var oldActive;
    var wasEmpty = true;
    var isBare;
    var oldActive;
    if (tree !== undefined) {
        ret = tree;
        wasEmpty = false;
        oldActive = ret.saveActiveNode();
    }
    var succ = [false];
    ret = getField(str, ret, succ);
    if (!succ[0]){ //if no field, try for a division
        ret = getDivision(str, ret, succ);
        if (succ[0]) {
            ret.setActiveNode(ret.getActiveNode().at(-1)); //set gotten division as active
            ret = getEscutcheon(str, ret, succ); //get first tincture/sub-blazon
            isBare = isBareField(ret.getActiveNode().at(-1)); //did we fetch a bare field?
            //skip a comma if one is present after the first tincture/sub-blazon
            if (succ[0]) {
                //if we're parsing a bare division and find a sub-escutcheon, abort
                if (bare === true && !isBare) {
                    success[0] = false;
                } else {
                    var i;
                    //if we have a tierced field, check for *two* more fields
                    if (ret.getActiveNode().number === 3) {
                        i = 2;
                    } else {
                        i = 1;
                    }
                    for (; i > 0; --i) {
                        //if we have a linking word, look for a second/third tincture
                        if (isLinking(str.peek()) || isPunct(str.peek())) {
                            str.pop();
                            // if we have a string like "first, and second", we pop both the punctuation and the "and"
                            if (isLinking(str.peek())) {
                                str.pop();
                            }
                            //if the previous sub-blazon was only a tincture, we only get a tincture. Otherwise an entire sub-blazon.
                            //note that if bare===true then the previous sub-blazon was a bare tincture or we'd have aborted
                            if (isBare) {
                                ret = getField(str, ret, succ);
                            } else {
                                ret = getEscutcheon(str, ret, succ);
                            }
                            if (succ[0]) {
                                //if this is a tierced shield, the *middle* field comes first, then the dexter
                                if (ret.getActiveNode().number === 3 && i === 2) {
                                    var tmp1 = ret.getActiveNode().pop();
                                    var tmp2 = ret.getActiveNode().pop();
                                    ret.getActiveNode().push(tmp1);
                                    ret.getActiveNode().push(tmp2);
                                }
                            } else {
                                //if we can't parse enough fields, reset the token stream and abort
                                success[0] = false;
                                break;
                            }
                        } else {
                            //if there is no linking word, reset the token stream and abort
                            success[0] = false;
                            break;
                        }
                    }
                }
            } else {
                //if there is no valid tincture, reset the token stream and abort
                success[0] = false;
            }
            //pop empty division if we added it and then failed to parse its fields
            if (success[0] === false) {
                active.pop();
            }
        } else { //if fetching both a field and a division have failed, then fail
            success[0] = false;
        }
    }
    if (oldActive !== undefined) {
        ret.restoreActiveNode(oldActive);
    }
    if (success[0]) {
        return ret;
    } else {
        str.loadPos(pos);
        tinctureStack = oldStack.slice(0);
        return tree;
    }
}

//type is optional and should not generally be used explicitly
//default orient/mirror are to be used where the assumption of 
function getCharge(str, tree, success, type, defaultOrient=0, defaultMirror=false)
{
    success[0] = true;
    var ret = tree;
    var oldActive = ret.saveActiveNode();
	var number=0; //not possible, error if this does not change
	var index;
	var tincture=new Field(tinctureStack.length); //defaults to "specified later" (one past the end of the current tincture stack)
    var orientation = defaultOrient;
	var mirrored=defaultMirror;
	var direction=0;
	var arrangement=0;
    var pos = str.savePos(); //save our place in the token stream so we can exit without changing anything
    var oldStack = tinctureStack.slice(0);
    var chargeParent = tree.getActiveNode();
	if( isNumber(str.peek()) ){
		number=str.pop().value;
		var name = tryChargeName(str, type);//if type is undefined, we'll get any charge
        if (name !== undefined) {
			//if we found a charge, create empty node (with type and index) and set it as active
            type = name[0];
            index = name[1];
            if (type === TYPE_BEAST) {
                //index, tincture, number = 1, orientation=0, direction=0, arrangement=0
                ret.getActiveNode().push(new Beast(name[1], null, number, 0, direction, 0));//beasts not affected by default orientation
            } else {
                //type, index, tincture, number = 1, orientation = 0, mirrored = false, arrangement = 0)
                ret.getActiveNode().push(new Charge(name[0], name[1], null, number, orientation, mirrored, 0));
            }
            ret.setActiveNode(ret.getActiveNode().at(-1));
			//then parse through modifiers in a loop
			while( isWord(str.peek()) || isNumber(str.peek()) ){
				//check for a colouring
                var succ = [false];
                var ret = getFieldOrDivision(str, ret, succ, true);
                var tmp;
				//colouring is optional, but ends the charge if it exists
				if( succ[0] )
                {
                    tincture = ret.getActiveNode().at(0);
					break;
				}
				
				tmp=tryArrangementName(str);
				if( tmp!==undefined )
				{
					if(type===TYPE_IMMOVABLE){
						console.error("Semantic error: immovable charges cannot be arranged");
					}else{
                        arrangement = tmp[1];
                        ret.getActiveNode().arrangement = arrangement;
						//if a group is arranged bendwise or bendwise sinister, by default the charges should be oriented likewise
						if(orientation === undefined){
							if(arrangement === ARR_BEND){
                                orientation = ORIENT_BEND;
                                ret.getActiveNode().orientation = orientation;
							}else if(arrangement === ARR_SINISTER){
								orientation = ORIENT_SINISTER;
                                ret.getActiveNode().orientation = orientation;
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
                        mirrored = tmp[2];
                        ret.getActiveNode().orientation = orientation;
                        ret.getActiveNode().mirrored = mirrored;
					}
					continue;
				}
				
				tmp=tryAttitudeName(str);
				if( tmp!==undefined )
				{
					if(type!==TYPE_BEAST){
						console.error("Semantic error: only beasts can have attitude");
					}else{
                        orientation = tmp[1];
                        ret.getActiveNode().orientation = orientation;
					}
					continue;
				}
				
				tmp=tryDirectionName(str);
				if( tmp!==undefined )
				{
					if(type!==TYPE_BEAST){
						console.error("Semantic error: only beasts can have attitude");
					}else{
                        direction = tmp[1];
                        ret.getActiveNode().direction = direction;
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
            //if we didn't pick up a tincture, push the undefined tincture
            if (ret.getActiveNode().at(0) === undefined) {
                ret.getActiveNode().push(tincture);
            }
			//if we have a dual arrangement, replace the single charge with a charge group of two charges
            if (isDualArrangement(arrangement)) {
                var newChrg = new ChargeGroup();
                ret.setActiveNode(ret.getActiveNode().parent);//set active node to parent of apended charge
				var orientA, orientB, mirrorA, mirrorB, directA, directB;
				if( arrangement===ARR_SALTIRE ){
					orientA = ORIENT_BEND;
					mirrorA = BEND_MIRRORED;
					orientB = ORIENT_SINISTER;
					mirrorB = SINISTER_MIRRORED;
					newChrg.arrangement=ARR_COMBINED;
					if(type===TYPE_BEAST){
						console.error("Semantic error: beasts cannot be in saltire");
                        success[0] = false;
					}
				}else if( arrangement===ARR_ADDORSED ){
					orientA = ORIENT_PALE;
					mirrorA = PALE_MIRRORED;
					directA = DIR_DEXTER;
					orientB = ORIENT_PALE_REV;
					mirrorB = PALE_REV_MIRRORED;
					directB = DIR_SINISTER;
					newChrg.arrangement=ARR_FESS;
				}else if( arrangement===ARR_CONFRONTE ){
					orientA = ORIENT_PALE_REV;
					mirrorA = PALE_REV_MIRRORED;
					directA = DIR_SINISTER;
					orientB = ORIENT_PALE;
					mirrorB = PALE_MIRRORED;
					directB = DIR_DEXTER;
					newChrg.arrangement=ARR_FESS;
                }
                if (success[0]) {
                    newChrg.append(createCharge(type, index, tincture, 1, orientA, mirrorA, directA, 0));
                    newChrg.append(createCharge(type, index, tincture, 1, orientB, mirrorB, directB, 0));
                    ret.getActiveNode().pop();
                    ret.getActiveNode().push(newChrg);
                    ret.setActiveNode(ret.getActiveNode().at(-1)); //set appended charge group as active
                }
			}
			//if we see a linking word
			if( isLinking( str.peek() ) )
			{
				var linkpos=str.savePos();
                var lwrd = str.pop();
                var succ = [false];
                ret = getMovableOrBeast(str, ret, succ);
				if( !succ[0] ){
					str.rewind();//un-pop the linking word
                } else if (isThisWord(lwrd, "between") /*&& type === TYPE_IMMOVABLE*/) {
                    //this now only works for ordinaries!!
					//check for a second charge for it to be between
                    var andSuccess = false;
					if( isThisWord(str.peek(), "and") ){
						str.pop();
						ret=getMovableOrBeast(str, ret, succ);
						if( !succ[0] )
						{
                            str.rewind();//un-pop the conjunction
                        } else {
                            andSuccess = true;
                        }
                    }
                    if( ! andSuccess ){
                        //if the ordinary is amidst one group of charges, split the group in half as evenly as possible
						//if we have a charge group, take the individual charges
                        var crg = ret.getActiveNode().at(-1);
						if(crg.type===TYPE_GROUP){
                            if (crg.subnode.length === 2) {
                                var grp = ret.getActiveNode().pop();
                                ret.getActiveNode().push(grp.at(0));
                                ret.getActiveNode().push(grp.at(1));
							}else{
                                console.error("Parsing error: can only split group with two charges");
                                ret.getActiveNode().push(new ChargeGroup());
							}
						}else{
                            var hlf = Math.floor(crg.number/2);
                            var gtr = crg.number - hlf;
                            if (hlf > 0) {
                                var scrg = crg.clone();
                                //place larger half above/dexter of the ordinary, lesser half below/sinister
                                crg.number = gtr;
                                scrg.number = hlf;
                                ret.getActiveNode().push(scrg);
                            } else {
                                ret.getActiveNode().push(new ChargeGroup());
                            }
						}
                    }
                    //move secondary charges from children of primary to siblings within a charge group
                    if (type !== TYPE_IMMOVABLE) {
                        ret.setActiveNode(ret.getActiveNode().parent);
                        var superCharge = ret.getActiveNode().pop();
                        ret.getActiveNode().push(new ChargeGroup());
                        ret.setActiveNode(ret.getActiveNode().at(-1));
                        ret.getActiveNode().push(superCharge.at(1));
                        ret.getActiveNode().push(superCharge);
                        ret.getActiveNode().push(superCharge.at(1));
                    }
                } else if (isThisWord(lwrd, "and")) {
                    //TODO: construct a charge group in this case, if primary charge is immovable
                    str.loadPos(linkpos); //rewind to the word "and"
                    ret.getActiveNode().pop(); //discard secondary charge
				}else{
					str.loadPos(linkpos); //rewind to linking word
                    console.error("Word " + str.pos.toString() + ": preposition not implemented");
                    success[0] = false;
				}	
            }
            if (!success[0]) {
                //if we pushed a charge but failed, pop it off again
                chargeParent.pop();
            }
		}else{
            str.rewind(); //un-pop the number
            success[0] = false;
		}
	}else if( isThisWord(str.peek(), "on") ){
        str.pop();
        var succ=[false];
		ret=getCharge(str, ret, succ);
		if( !succ[0] ){
            str.rewind();//un-pop the "on"
            success[0] = false;
        }
        ret.setActiveNode(ret.getActiveNode().at(-1))
        var type = ret.getActiveNode().type;
        var index = ret.getActiveNode().index;
        var orient = 0;
        var mirror = false;
        //by default, charges on a bend are bendwise
        if (type === TYPE_IMMOVABLE && index === CHARGE_BEND) {
            orient = ORIENT_BEND;
            mirror = BEND_MIRRORED;
        }
        if (type === TYPE_IMMOVABLE && index === CHARGE_SINISTER) {
            orient = ORIENT_SINISTER;
            mirror = SINISTER_MIRRORED;
        }
		//charge on the ordinary itself becomes a subnode of its tincture
        //so set the tincture of the appended charge as the active node
        ret.setActiveNode(ret.getActiveNode().at(0));
        succ[0] = false;
        var ret = getMovableOrBeast(str, ret, succ, orient, mirror);
		if(!succ[0]){
            console.error("Word " + str.pos.toString() + ": no movable charge where one was expected");
        }
	}else{
		//console.error("Word "+str.pos.toString()+": no number where one was expected");
        success[0] = false;
    }
    ret.restoreActiveNode(oldActive);
    if (!success[0]){
        str.loadPos(pos);
        tinctureStack = oldStack.slice(0);
    }
    return ret;
}

function createCharge(type, index, tincture, number, orientation, mirrored, direction, arrangement){
    var ret;
    if (tincture instanceof Field) {
        tincture = tincture.tincture;
    } else {
        tincture = tincture.clone();
    }
	if(type!==TYPE_BEAST){
		ret = new Charge(type, index, tincture, number, orientation, mirrored, arrangement);
	}else{
		ret = new Beast(index, tincture, number, orientation, direction, arrangement);
	}
	return ret;
}

function getMovable(str, tree, success, defaultOrient = 0, defaultMirror = false)
{
    return getCharge(str, tree, success, TYPE_MOVABLE, defaultOrient, defaultMirror);
}

function getBeast(str, tree, success)
{
    return getCharge(str, tree, success, TYPE_BEAST);
}

function getMovableOrBeast(str, tree, success, defaultOrient = 0, defaultMirror = false)
{
    var succ = [false];
    var ret = getMovable(str, tree, succ, defaultOrient, defaultMirror);
	if(!succ[0]){
        ret = getBeast(str, tree, succ, defaultOrient, defaultMirror)
    }
    success[0] = succ[0];
	return ret;
}

function getImmovable(str, tree, success, defaultOrient = 0, defaultMirror = false)
{
    return getCharge(str, tree, success, TYPE_IMMOVABLE, defaultOrient, defaultMirror);
}

function getEscutcheon(str, tree, success=[]) {
    var succ = [false];
    var wasEmpty = (tree === undefined);
    var ret;
    var oldStack = tinctureStack.slice(0);
    if (!wasEmpty) {
        ret = tree;
    }
    ret = getFieldOrDivision(str, ret, succ);
    if (ret === undefined) {
        return; //return an empty blazon (as was passed in)
    }
    var oldActive = ret.saveActiveNode();
    if (!wasEmpty) {
        ret.setActiveNode(ret.getActiveNode().at(-1));//set new field/division as active
    }
    if (!succ[0]) {
        console.error("Syntax error: no field (or division) where one was expected");
    } else {
        var flag = false;
        //skip a comma if one is present after the field
        if (isPunct(str.peek())) {
            str.pop();
            flag = true;
        }
        ret = getCharge(str, ret, succ);
        if (flag && !succ[0]) {
            str.rewind(); //un-pop the comma if there was no charge and one was popped
        }
        success[0] = true;
    }

    ret.restoreActiveNode(oldActive);
    if (success[0]) {
    } else {
        str.loadPos(pos);
        tinctureStack = oldStack.slice(0);
    }
    return ret;
}

function tokStrFromStr(str) {
    return new TokenStream(new Buffer(str));
}

//get AST from source string
function parseString(str) {
    tinctureStack = [];
    var tokstr = tokStrFromStr(str);
    var root = getEscutcheon(tokstr);
    if (root !== undefined) {
        secondParse(root);
    }
    return root;
}

//performs second pass of syntax parsing
function secondParse(tree) {
    if (tree.tincture !== undefined) {
        if (tree.tincture < tinctureStack.length) {
            tree.tincture = tinctureStack[tree.tincture];
        } else {
            //this branch should only be reached if the last object in a blazon has an unspecified tincture
            tree.tincture = TINCT_UNSPECIFIED;
        }
    }
    for (node of tree.subnode) {
        secondParse(node);
    }
    return tree;
}

/*****************
**MISC FUNCTIONS**
******************/

var tinctureStack = [];

function titleCase(str){
	return str.charAt(0).toUpperCase() + str.slice(1);
}

/******************
**DEBUG FUNCTIONS**
*******************/

function parseStringAndDisplay(str)
{
    var root = parseString(str);
	if(root===undefined){
		console.error("Debug error: no tree to print (probably due to a catastrophic parsing error)");
	}else{
		document.getElementById("displayPara").innerHTML=root.display();
    }
    return root;
}

function displayTree(root){
	document.getElementById("displayPara").innerHTML=root.display();
}

var root;
