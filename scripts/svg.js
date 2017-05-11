SVG_URI = "http://www.w3.org/2000/svg";

//encode safely to base64 using open source libraries (included in head)
function base64EncodingUTF8(str) {
    var encoded = new TextEncoderLite('utf-8').encode(str);        
    var b64Encoded = base64js.fromByteArray(encoded);
    return b64Encoded;
}

function getEncodedSVG(name){
	return base64EncodingUTF8(document.getElementById(name).contentDocument.childNodes[0].outerHTML);
}

function analyzePath(d){
	var words=d.split(" ");
	var comm=[];
	var cur=[];
	var currentCommand;
	var lowerCase = "abcdefghijklmnopqrstuvwxyz";
	var upperCase = lowerCase.toUpperCase();
	var isRel = false;
	var pos = new Point( 0, 0 );
	var firstPoint;
	for ( var w of words ){
		if( w.length === 1 ){
			cur=[w.toUpperCase()]
			currentCommand=w;
			isRel = ( lowerCase.indexOf(currentCommand) > -1 );
		}else{
			var xy=w.split(",");
			var x, y;
			if(xy.length===2){
				x=Number(xy[0]);
				y=Number(xy[1]);
				if( isRel ){
					x += pos.x;
					y += pos.y;
				}
				cur.push( new Point( x, y ) );
			}else{
				xy=Number(w);
				if(isRel){
					if(currentCommand.toUpperCase()==="V"){
						xy += pos.y;
					}else if( currentCommand.toUpperCase()==="H" ){
						xy += pos.x;
					}
				}
				cur.push(xy);
			}
		}
		if( isEnd(currentCommand, cur.length) )
		{	//slice(-1)[0] is last element of array
			if( cur.slice(-1)[0] instanceof Point ){
				pos = cur.slice(-1)[0];
				if(currentCommand.toUpperCase()==="M"){
					firstPoint=pos;
				}
			}else if(currentCommand.toUpperCase()==="V"){
				pos = new Point(pos.x, cur.slice(-1)[0]);
			}else if( currentCommand.toUpperCase()==="H" ){
				pos = new Point(cur.slice(-1)[0], pos.y);
			}else if(currentCommand.toUpperCase()==="Z"){
				pos=firstPoint;
			}else{
				console.error("Couldn't find next point!");
			}
			comm.push(cur);
			cur=[currentCommand.toUpperCase()];
		}
	}
	return comm;
}

function isEnd(command, pos){
	if( (command==="m" || command==="M") && pos===2 ){
		return true;
	}
	if( (command==="l" || command==="L") && pos===2 ){
		return true;
	}
	if( (command==="h" || command==="H") && pos===2 ){
		return true;
	}
	if( (command==="v" || command==="V") && pos===2 ){
		return true;
	}
	if( (command==="z" || command==="Z") && pos===1 ){
		return true;
	}
	if( (command==="c" || command==="C") && pos===4 ){
		return true;
	}
	if( (command==="s" || command==="S") && pos===3 ){
		return true;
	}
	if( (command==="q" || command==="Q") && pos===3 ){
		return true;
	}
	if( (command==="t" || command==="T") && pos===2 ){
		return true;
	}
	if( (command==="a" || command==="A") && pos===6 ){
		return true;
	}
}

function reassemblePath(arr){
	var path="";
	for (var i of arr){
		for(var j of i){
			if(typeof j === "number" && j<Math.pow(10,-6)){
				j=0;
			}
			path+=j;
			path+=" ";
		}
	}
	path=path.slice(0,-1);//trim the last char (it's a space)
	return path;
}

function Point(x, y){
	this.x=x;
	this.y=y;
}

Point.prototype.clone = function(){
	return new Point(this.x, this.y);
}

Point.prototype.toString = function(){
	return ""+this.x+","+this.y;
}

Point.prototype.scale = function (c)
{
	this.x *= c;
	this.y *= c;
}

Point.prototype.max = function (A)
{
	return new Point( Math.max( this.x, A.x ), Math.max( this.y, A.y ) );
}

Point.prototype.min = function (A)
{
	return new Point( Math.min( this.x, A.x ), Math.min( this.y, A.y ) );
}

function bezier(c0, c1, c2, c3, t){
	var ret = Math.pow(1-t, 3)*c0
	+ 3*Math.pow(1-t, 2)*t*c1
	+ 3*(1-t)*Math.pow(t,2)*c2
	+ Math.pow(t,3)*c3;
	return ret;
}

function bezierExtrema(c0, c1, c2, c3){
	var detsq = c1*c1 - c0*c2 - c1*c2 + c2*c2 + c0*c3 - c1*c3;
	var denom = c0 - 3*c1 + 3*c2 - c3;
	//if degenerate case with no exrema, return undefined for both points
	if(denom === 0){
		return [undefined, undefined];
	}
	var bsqu = c0 - 2*c1 +c2;
	var det = Math.sqrt(detsq);
	var t = [(bsqu - det)/denom, (bsqu + det)/denom];
	var r1,r2;
	if( t[0] >= 0 && t[0] <= 1){
		r1 = bezier(c0, c1, c2, c3, t[0]);
	}
	if( t[1] >= 0 && t[1] <= 1){
		r2 = bezier(c0, c1, c2, c3, t[1]);
	}
	return [ r1, r2 ];
}

function bezierBound(c0, c1, c2, c3){
	var c = [ c0, c3 ];
	var d = bezierExtrema(c0, c1, c2, c3);
	if( d[0] !== undefined ){
		c.push(d[0]);
	}
	if( d[1] !== undefined ){
		c.push(d[1]);
	}
	var low = Math.min(...c);
	var high = Math.max(...c);
	return [low, high];
}

function bezierBox(p0, p1, p2, p3){
	var x = bezierBound(p0.x, p1.x, p2.x, p3.x);
	var y = bezierBound(p0.y, p1.y, p2.y, p3.y);
	return [ new Point(x[0], y[0]), new Point(x[1], y[1]) ];
}

function bezierInvert(p0, p1, p2, p3){
	return [p3.clone(), p2.clone(), p1.clone(), p0.clone()];
}

function qBezierInvert(p0, p1, p2){
	return [p2.clone(), p1.clone(), p0.clone()];
}

//b must be 1-a, option included for computational accuracy
function bezierTruncate(p0, p1, p2, p3, a, b){
	if(b===undefined){
		b=1-a;
	}
	var ret=qBezierTruncate(p0, p1, p2, a, b);
	var r3=new Point( b*b*b*p0.x + 3*a*b*b*p1.x + 3*a*a*b*p2.x + a*a*a*p3.x,
		b*b*b*p0.y + 3*a*b*b*p1.y + 3*a*a*b*p2.y + a*a*a*p3.y );
	ret.push(r3);
	return [r0, r1, r2, r3];
}

//b must be 1-a, option included for computational accuracy
function qBezierTruncate(p0, p1, p2, a, b){
	if(b===undefined){
		b=1-a;
	}
	var r0=p0.clone();
	var r1=new Point( b*p0.x + a*p1.x, b*p0.y + a*p1.y );
	var r2=new Point( b*b*p0.x + 2*a*b*p1.x + a*a*p2.x,
		b*b*p0.y + 2*a*b*p1.y + a*a*p2.y );
	return [r0, r1, r2];
}

function bezierSplit(p0, p1, p2, p3, a){
	var r1 = bezierTruncate(p0, p1, p2, p3, a);
	var r2 = bezierInvert(...bezierTruncate(...bezierInvert(p0, p1, p2, p3), 1-a, a));
	var ret = r1.concat(r2.slice(1));
	return ret;
}

function qBezierSplit(p0, p1, p2, a){
	var r1 = qBezierTruncate(p0, p1, p2, a);
	var r2 = qBezierInvert(...qBezierTruncate(...qBezierInvert(p0, p1, p2), 1-a, a));
	var ret = r1.concat(r2.slice(1));
	return ret;
}

function qBezier(c0, c1, c2, t){
	var ret = Math.pow(1-t, 2)*c0
	+ 2*(1-t)*t*c1
	+ Math.pow(t,2)*c2;
	return ret;
}

function qBezierExtremum(c0, c1, c2){
	var denom = 2*c1 - (c0 + c2);
	var num = c1 - c0;
	if(denom === 0){
		return;
	}
	var t = num/denom;
	var ret;
	if( t >= 0 && t <= 1){
		ret = qBezier(c0, c1, c2, t);
	}
	return ret;
}

function qBezierBound(c0, c1, c2){
	var c = [ c0, c2 ];
	var d = qBezierExtremum(c0, c1, c2);
	if( d !== undefined ){
		c.push(d);
	}
	var low = Math.min(...c);
	var high = Math.max(...c);
	return [low, high];
}

function qBezierBox(p0, p1, p2){
	var x = qBezierBound(p0.x, p1.x, p2.x);
	var y = qBezierBound(p0.y, p1.y, p2.y);
	return [ new Point(x[0], y[0]), new Point(x[1], y[1]) ];
}

function BoundingBox(){
	this.min = new Point( Infinity, Infinity );
	this.max = new Point( -Infinity, -Infinity );
}

BoundingBox.prototype.addPoint = function( P ){
	if(P instanceof Point){
		this.min = this.min.min(P);
		this.max = this.max.max(P);
	}
}

BoundingBox.prototype.addPoints = function(){
	for ( arg of arguments ) {
		this.addPoint(arg);
	}
}

//NOTE: this function cannot deal with arc commands
//TODO: make this function deal with arc commands
function getBoundingBox(arr){
	var bBox = new BoundingBox;
	var firstPoint = arr[0][1].clone();
	//var prevPoint = new Point( 0, 0 )
	var pos = new Point( 0, 0 )
	for (var p of arr){
		if( p[0] === "V" ){//"Z" does nothing to alter the bounding box
			pos.y = p[1];
			bBox.addPoint(pos);
		}else if( p[0] === "H" ){
			pos.x = p[1];
			bBox.addPoint(pos);
		}else if( p[0] === "Z" ){
			pos = firstPoint;//don't need to add first point to bbox -- it's already there
		}else if(p[0] === "C"){
			bBox.addPoints( ...bezierBox( pos, p[1], p[2], p[3] ) );
		}else if(p[0] === "Q"){
			bBox.addPoints( ...qBezierBox( pos, p[1], p[2] ) );
		}else if( p[0] === "M" || p[0] === "L" ){
			bBox.addPoint(p[1]);
			pos = p[1].clone();
		} 
	}
	return [bBox.min, bBox.max];
}


//has to be window.onLoad: objects are not loaded at DOMready
//window.onload = function () { document.getElementById("idOut").value = getEncodedSVG("dots") }

function drawBox(container, shape, boxID){
	var esc=document.getElementById(container);
	var d=document.getElementById(shape).getAttribute("d");
	var boxElem = document.getElementById(boxID);
	var b=analyzePath(d);
	var box=getBoundingBox(b);
	if(boxElem !== null){
		esc.removeChild(boxElem);
	}
	rect=document.createElementNS(SVG_URI, "rect");
	rect.setAttribute("x", box[0].x);
	rect.setAttribute("y", box[0].y);
	rect.setAttribute("width", box[1].x - box[0].x);
	rect.setAttribute("height", box[1].y - box[0].y);
	rect.setAttribute("fill", "transparent");
	rect.setAttribute("stroke", "black");
	rect.setAttribute("id", boxID);
	esc.appendChild(rect);
}

function changeErmine(arr){
	var erminePath=document.getElementById("ermineSpot");
	var path=reassemblePath(arr);
	erminePath.setAttribute("d", path);
	//drawBox("escutcheon", "ermineSpot", "ermineBox");
}

function getWidthAndHeight(path){
	var box=getBoundingBox(path);
	var w = box[1].x - box[0].x;
	var h = box[1].y - box[0].y;
	return [w,h];
}

function getCentre(path){
	var box=getBoundingBox(path);
	var cx = (box[0].x + box[1].x)/2;
	var cy = (box[0].y + box[1].y)/2;
	return new Point(cx, cy);
}

function shiftPath(path, dx=0, dy=0){
	var newPath = clonePath(path);
	for(var i=0; i<newPath.length; ++i){
		for(var j=0; j<newPath[i].length; ++j){
			if( newPath[i][j] instanceof Point){
				newPath[i][j].x=newPath[i][j].x+dx;
				newPath[i][j].y=newPath[i][j].y+dy;
			}else if( typeof newPath[i][j] ==="string" ){
				//do nothing
			}else if( typeof newPath[i][j] === "number" ){
				if( newPath[i][0] === "H" ){
					newPath[i][j] += dx;
				}else if(newPath[i][0] === "V"){
					newPath[i][j] += dy;
				}
			}
		}
	}
	return newPath;
}

function roundToSix(n){
	return Math.round(n*1000000)/1000000;
}

function roundPath(path){
	var newPath = clonePath(path);
	for(var i=0; i<newPath.length; ++i){
		for(var j=0; j<newPath[i].length; ++j){
			if( newPath[i][j] instanceof Point){
				newPath[i][j].x = roundToSix( newPath[i][j].x );
				newPath[i][j].y = roundToSix( newPath[i][j].y );
			}else if( typeof newPath[i][j] ==="string" ){
				//do nothing
			}else if( typeof newPath[i][j] === "number" ){
				newPath[i][j] = roundToSix( newPath[i][j] );
			}
		}
	}
	return newPath;
}

function clonePath(path){
	var newPath=[];
	for( var i of path ){
		var newLine=[];
		newLine.push(i[0]);
		for(var j=1; j<i.length; ++j){
			if( i[j] instanceof Point ){
				newLine.push( new Point( i[j].x, i[j].y ) );
			}else{
				newLine.push(i[j]);
			}
		}
		newPath.push(newLine);
	}
	return newPath;
}

//TODO: fix this function
function scalePath(path, c, cx, cy){
	var newPath = clonePath(path);
	if(cx===undefined || cy===undefined){
		cx=0;
		cy=0;
	}
	centre = new Point(cx, cy);
	for(var i of newPath){
		for(var j=1; j<i.length; ++j){
			if( i[j] instanceof Point){
				i[j].x= (i[j].x-centre.x)*c + centre.x;
				i[j].y= (i[j].y-centre.y)*c + centre.y;
			}else if( typeof i[j] ==="string" ){
				console.error("error: command in path where point was expected")
			}else if( typeof i[j] === "number" ){
				if( i[0] === "H" ){
					i[j] = (i[j]-centre.x)*c + centre.x;
				}else if(i[0] === "V"){
					i[j] = (i[j]-centre.y)*c + centre.y;
				}
			}
		}
	}
	return newPath;
}

function addPathToSVG(container, path){
	var d = reassemblePath(path);
	var pathElem=document.createElementNS(SVG_URI, "path");
	pathElem.setAttribute("d", d)
	container.appendChild(pathElem);
}

function getPathFromElem(id)
{
	var elem = document.getElementById(id);
	var d = elem.getAttribute("d");
	var arr = analyzePath(d);
	return arr;
}

function setPathOnElem(id, arr){
	var elem = document.getElementById(id);
	var d = reassemblePath(arr);
	elem.setAttribute("d", d);
}

function roundElem(id){
	var path = getPathFromElem(id);
	path = roundPath(path);
	setPathOnElem(id, path);
}

function changeTincture(id,tinct){
	var elem = document.getElementById(id);
	elem.setAttribute("class", "heraldry-"+tinct);
}

function changeHeraldryCSS(fileName){
	var elem = document.getElementById("heraldry-css");
	elem.setAttribute("href","styles/"+fileName);
}

function recentrePath(arr, x, y){
	var c = getCentre(arr);
	var dx = x - c.x;
	var dy = y - c.y;
	var newPath = shiftPath(arr, dx, dy);
	return newPath;
}

function recentreElem(id, x, y){
	var elem = document.getElementById(id);
	var d = elem.getAttribute("d");
	var arr = analyzePath(d);
	arr = recentrePath(arr, x, y);
	d = reassemblePath(arr);
	elem.setAttribute("d", d)
}

//recentreElem("shield", 100, 100);
//roundElem("shield");
//var shield = document.getElementById("shield");
//var path=analyzePath(shield.getAttribute("d"));
//path=scalePath(path, 1.5);
//path=recentrePath(path, 150, 150);
//path=roundPath(path);
//shield.setAttribute("d", reassemblePath(path));

//var root = document.getElementById("root");
//var path = document.getElementById("path");
//var point = root.createSVGPoint();
//point.x = 0;  // replace this with the x co-ordinate of the path segment
//point.y = 0;  // replace this with the y co-ordinate of the path segment
//var matrix = path.getTransformToElement(root);
//var position = point.matrixTransform(matrix);

path=document.getElementById("shield");
box=path.getBBox();
centre=new Point(box.x+(box.width/2), box.y+(box.height/2));

function addPale(id, tinct, rw=0.3){
	var elem=document.getElementById(id);
	var pale=createPale(elem, tinct, rw);
	setClip(pale, id);
	//insert just after given element
	elem.parentNode.insertBefore(pale, elem.nextSibling);
}

function addBend(id, tinct, rw=0.3){
	var elem=document.getElementById(id);
	var c=[];
	var pale=createPale(elem, tinct, rw, c);
	pale.setAttribute("transform", "rotate(-45 "+c[0]+" "+c[1]+")");
	var g=document.createElementNS(SVG_URI, "g");
	g.setAttribute("class", "bend");
	g.appendChild(pale);
	setClip(g,id);
	//insert just after given element
	elem.parentNode.insertBefore(g, elem.nextSibling);
}

function createClip(id){
	var clipId=id+"-clip";
	var clip=document.getElementById(clipId);
	if(clip == null){
		clip=document.createElementNS(SVG_URI, "clipPath");
		var elem=document.getElementById(id);
		var d=elem.getAttribute("d");
		var defEl=document.getElementById("SVGDefs");
		var path=document.createElementNS(SVG_URI, "path");
		path.setAttribute("d",d);
		clip.appendChild(path);
		clip.setAttribute("id", clipId);
		defEl.appendChild(clip);
	}
	return clipId;
}

function setClip(elem, clipToId){
	clipId=createClip(clipToId);
	elem.setAttribute("clip-path","url(#"+clipId+")");
}

function createPale(elem, tinct, rw=0.34, c=[]){
	//var path=document.getElementById(id);
	var box=elem.getBBox();
	var h=box.height;
	var w=box.width;
	rw=w*rw;
	var rh=Math.ceil(Math.sqrt(h*h+w*w));//diagonal length
	var rx=box.x+(w-rw)/2;
	var ry=box.y+(w-rh)/2;
	var cx=box.x+w/2;
	var cy=box.y+w/2;
	c.push(cx);
	c.push(cy);
	var pale=document.createElementNS(SVG_URI, "rect");
	pale.setAttribute("x", rx);
	pale.setAttribute("y", ry);
	pale.setAttribute("width", rw);
	pale.setAttribute("height", rh);
	pale.setAttribute("class", "heraldry-"+tinctures[tinct]);
	pale.setAttribute("id", elem.id+"-pale");
	return pale;
}

function createFess(elem, tinct, rh=0.34){
	var box=elem.getBBox();
	var h=box.height;
	var w=box.width;
	rh=rh*h;
	var ry=box.y+(h-rh)/2;
	var rx=box.x;
	rw=w;
	var fess=document.createElementNS(SVG_URI, "rect");
	fess.setAttribute("x", rx);
	fess.setAttribute("y", ry);
	fess.setAttribute("width", rw);
	fess.setAttribute("height", rh);
	fess.setAttribute("class", "heraldry-"+tinctures[tinct]);
	fess.setAttribute("id", elem.id+"-fess");
	return fess;
}

function addFess(id, tinct, rw=0.3){
	var elem=document.getElementById(id);
	var fess=createFess(elem, tinct, rw);
	setClip(fess, id);
	//insert just after given element
	elem.parentNode.insertBefore(fess, elem.nextSibling);
}

var tree = parseString("Azure, a bend or");

tinctureClasses = ["heraldry-unspecified", "heraldry-argent", "heraldry-or", "heraldry-gules", "heraldry-azure", "heraldry-vert", "heraldry-purpure", "heraldry-sable", "heraldry-tenny", "heraldry-sanguine", "heraldry-vair", "heraldry-countervair", "heraldry-potent", "heraldry-counterpotent", "heraldry-ermine", "heraldry-ermines", "heraldry-erminois", "heraldry-pean"]

//addBend("shield", 3, 0.34);

//immovables = ["chief", "pale", "fess", "bend", "bend sinister", "chevron", "saltire", "pall", "cross", "pile", "bordure", "orle", "tressure", "canton", "flanches", "gyron", "fret"];
function applyTree(shieldId, tree){
	//var shield=document.getElementById(shieldId);
	var tinct = tinctures[tree.tincture];
	changeTincture(shieldId, tinct);
	if( tree.subnode.length > 0 ){
		if(tree.at(0).type===TYPE_IMMOVABLE){
			if(tree.at(0).index===1){//pale
				addPale(shieldId, tree.at(0).at(0).tincture);
			}else if(tree.at(0).index===2){//fess
				addFess(shieldId, tree.at(0).at(0).tincture);
			}else if(tree.at(0).index===3){//bend
				addBend(shieldId, tree.at(0).at(0).tincture);
			}
		}
	}
}

scratch=document.createElementNS(SVG_URI,"svg");

function clearShield(){
	var shield = document.getElementById("shield");
	var outline = document.getElementById("shieldOutline");
	var esc = document.getElementById("escutcheon");
	scratch.appendChild(shield);
	scratch.appendChild(outline);
	while (esc.lastChild) {
		esc.removeChild(esc.lastChild);
	}
	esc.appendChild(shield);
	esc.appendChild(outline);
	changeTincture("shield", "ermine");
}

function setBlazon(str){
	clearShield();
	parseStringAndDisplay(str)
	applyTree("shield", parseString(str))
}