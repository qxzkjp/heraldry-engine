SVG_URI = "http://www.w3.org/2000/svg";
shieldA = "M 20 5 L 180 5 C 180 5 182.113598 84.825528 167.003707 137.362438 C 151.893806 189.899348 102.105477 195.000018 100 195 C 97.894524 195 48.106195 189.899348 32.996295 137.362438 C 17.886404 84.825528 20 5 20 5 Z";
shieldB = "M 20 12.5 L 179.999996 12.5 L 179.999996 81.366166 C 179.999996 157.611827 99.999998 187.500003 99.999998 187.500003 C 99.999998 187.500003 20 157.611827 20 81.366166 Z";
blazon = "";

scratch=document.createElementNS(SVG_URI,"svg");
SVG=document.getElementById("escutcheonContainer");
SVG_HEIGHT = 200;
SVG_WIDTH = 200;

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

Point.difference = function(p1, p2){
	var p3 = new Point( 0, 0 );
	p3.x = p2.x - p1.x;
	p3.y = p2.y - p1.y;
	return p3;
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
	return ret;
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

function clonePathData(pathData){
	var newPD=[];
	for( var seg of pathData ){
		newPD.push( {type:seg.type, values:seg.values.slice(0)} );
	}
	return newPD;
}

function roundPathData(pathData, prec=6){
	var powten = Math.pow(10,prec);
	var ret = clonePathData(pathData);
	for( var seg of ret ){
		for(var i=0; i<seg.values.length; ++i){
				seg.values[i]=Math.round(seg.values[i]*powten)/powten;
		}
	}
	return ret;
}

//path data must be normalised!
function shiftPathData(pathData, dx=0, dy=0){
	var ret = clonePathData(pathData);
	for( var seg of ret ){
		for(var i=0; i<seg.values.length; ++i){
			if(i%2==0){
				seg.values[i]+=dx;
			}else{
				seg.values[i]+=dy;
			}
		}
	}
	return ret;
}

//path data must be normalised!
function scalePathData(pathData, sx, sy, cx=0, cy=0){
	if(sy===undefined){//if only one scaling factor supplied, scale uniformly
		sy=sx;
	}
	var ret = clonePathData(pathData);
	for( var seg of ret ){
		for(var i=0; i<seg.values.length; ++i){
			var tmp=seg.values[i];
			if(i%2==0){
				seg.values[i]=(tmp-cx)*sx+cx;
			}else{
				seg.values[i]=(tmp-cy)*sy+cy;
			}
		}
	}
	return ret;
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

//c comes back containing [centre x, centre y, rect x, rect y, rect height, rect width]
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
	c.push(rx);
	c.push(ry);
	c.push(rw);
	c.push(rh);
	var pale=document.createElementNS(SVG_URI, "rect");
	pale.setAttribute("x", rx);
	pale.setAttribute("y", ry);
	pale.setAttribute("width", rw);
	pale.setAttribute("height", rh);
	pale.setAttribute("class", "heraldry-"+tinctures[tinct]);
	pale.setAttribute("id", elem.id+"-pale");
	return pale;
}

function createFess(elem, tinct, rh=0.34, c=[]){
	var box=elem.getBBox();
	var h=box.height;
	var w=box.width;
	rh=rh*h;
	var ry=box.y+(h-rh)/2;
	var rx=box.x;
	rw=w;
	c.push(null);
	c.push(null);
	c.push(rx);
	c.push(ry);
	c.push(rw);
	c.push(rh);
	var fess=document.createElementNS(SVG_URI, "rect");
	fess.setAttribute("x", rx);
	fess.setAttribute("y", ry);
	fess.setAttribute("width", rw);
	fess.setAttribute("height", rh);
	fess.setAttribute("class", "heraldry-"+tinctures[tinct]);
	fess.setAttribute("id", elem.id+"-fess");
	return fess;
}

function getPoints(c1, c2, c3, c4){
	var p1=SVG.createSVGPoint();
	p1.x=c1;
	p1.y=c2;
	var p3=SVG.createSVGPoint();
	p3.x=p1.x+c3;
	p3.y=p1.y+c4;
	var p2=SVG.createSVGPoint();
	p2.x=p3.x;
	p2.y=p1.y;
	var p4=SVG.createSVGPoint();
	p4.x=p1.x;
	p4.y=p3.y;
	return [p1, p2, p3, p4];
}

function addFess(id, tinct, rw=0.3){
	var r=[];
	var elem=document.getElementById(id);
	var fess=createFess(elem, tinct, rw, r);
	setClip(fess, id);
	//insert just after given element
	elem.parentNode.insertBefore(fess, elem.nextSibling);
	var P=getPoints(...r.slice(2));
}

function addPale(id, tinct, rw=0.3){
	var r=[];
	var elem=document.getElementById(id);
	var pale=createPale(elem, tinct, rw, r);
	setClip(pale, id);
	//insert just after given element
	elem.parentNode.insertBefore(pale, elem.nextSibling);
	var P=getPoints(...r.slice(2));
}

function addBend(id, tinct, rw=0.3){
	var elem=document.getElementById(id);
	var c=[];
	var pale=createPale(elem, tinct, rw, c);
	pale.setAttribute("transform", "rotate(-45 "+c[0]+" "+c[1]+")");
	pale.setAttribute("id", id+"-bend");
	var g=document.createElementNS(SVG_URI, "g");
	g.setAttribute("class", "bend");
	g.appendChild(pale);
	setClip(g,id);
	//insert just after given element
	elem.parentNode.insertBefore(g, elem.nextSibling);
	var m=pale.getCTM();
	//turn rectangle x,y,h,w into four SVG points (clockwise from top left)
	var P=getPoints(...c.slice(2));
	//apply 45 deg rotation to get true points
	for( var i=0; i<P.length; ++i ){
		P[i]=P[i].matrixTransform(m);
	}
	var pathGroup1 = [];
	var pathGroup2 = [];
	var pathData = elem.getPathData();
	pathLineIntersection(pathData, P[1], P[2], pathGroup1);
	pathLineIntersection(pathData, P[0], P[3], pathGroup2);
	var topRightPath = pathGroup1[1];
	var bottomLeftPath = pathGroup2[1];
	var topRight =  document.createElementNS(SVG_URI, "path");
	topRight.setPathData(topRightPath);
	var bottomLeft =  document.createElementNS(SVG_URI, "path");
	bottomLeft.setPathData(bottomLeftPath);
	return(topRight, bottomLeft);
}

//TODO: implement this function
function getIntersection(path, p1 ,p2){
	var pos = new Point(0,0);
	var fistPos = new Point(0,0);
	for( var seg of path ){
		if(seg.type==="M" || seg.type==="L"){
			var start = pos.clone();
			pos = new Point(seg.values[0], seg.values[0]);
			var end = pos.clone();
			if(seg.type==="M"){
				firstPos = pos.clone();
			}
		}
	}
}

function addSinister(id, tinct, rw=0.3){
	var elem=document.getElementById(id);
	var c=[];
	var pale=createPale(elem, tinct, rw, c);
	pale.setAttribute("transform", "rotate(45 "+c[0]+" "+c[1]+")");
	pale.setAttribute("id", id+"-sinister");
	var g=document.createElementNS(SVG_URI, "g");
	g.setAttribute("class", "sinister");
	g.appendChild(pale);
	setClip(g,id);
	//insert just after given element
	elem.parentNode.insertBefore(g, elem.nextSibling);
	var m=pale.getCTM();
	var P=getPoints(...c.slice(2));
	for( var i=0; i<P.length; ++i ){
		P[0]=P[0].matrixTransform(m);
	}
}

var tree = parseString("Azure, a bend or");

tinctureClasses = ["heraldry-unspecified", "heraldry-argent", "heraldry-or", "heraldry-gules", "heraldry-azure", "heraldry-vert", "heraldry-purpure", "heraldry-sable", "heraldry-tenny", "heraldry-sanguine", "heraldry-vair", "heraldry-countervair", "heraldry-potent", "heraldry-counterpotent", "heraldry-ermine", "heraldry-ermines", "heraldry-erminois", "heraldry-pean"]

//immovables = ["chief", "pale", "fess", "bend", "bend sinister", "chevron", "saltire", "pall", "cross", "pile", "bordure", "orle", "tressure", "canton", "flanches", "gyron", "fret"];
//divisions = ["per pale", "per fess", "per bend", "per bend sinister", "per chevron", "per saltire", "", "", "", "", "", "", "",""];

function applyTree(shieldId, tree){
	//var shield=document.getElementById(shieldId);
	if( tree instanceof Field ){
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
				}else if(tree.at(0).index===4){//bend sinister
					addSinister(shieldId, tree.at(0).at(0).tincture);
				}
			}
		}
	}else if(tree instanceof Division){
		var elem = document.getElementById(shieldId);
		var pathData = elem.getPathData();
		var shieldBox = elem.getBBox();
		if( tree.type === 0){
			var midx = shieldBox.x + shieldBox.width/2;
			//partyPerPoints(elem, new Point(midx, 0), new Point(midx, SVG_HEIGHT), tree);
			var sections = [];
			pathLineIntersection(pathData, new Point(midx, 0), new Point(midx, SVG_HEIGHT), sections);
			var firstHalf = document.createElementNS(SVG_URI, "path");
			var secondHalf = document.createElementNS(SVG_URI, "path");
			//firstHalf.setAttribute("visibility", "hidden");
			//secondHalf.setAttribute("visibility", "hidden");
			firstHalf.setAttribute("id", shieldId+"-first");
			secondHalf.setAttribute("id", shieldId+"-second");
			firstHalf.setPathData(sections[0]);
			secondHalf.setPathData(sections[1]); 
			//insert just after given element
			elem.parentNode.insertBefore(secondHalf, elem.nextSibling);
			//insert just after given element, which is then just *before* secondHalf
			elem.parentNode.insertBefore(firstHalf, elem.nextSibling);
			applyTree(shieldId+"-first", tree.at(0));
			applyTree(shieldId+"-second", tree.at(1));
		}else{
			console.error("division not implemented");
		}
	}
}

function partyPerPoints(elem, p1, p2, tree){
	//var elem = document.getElementById(shieldId);
	var pathData = elem.getPathData();
	var shieldId = elem.getAttribute("id");
	var sections = [];
	pathLineIntersection(pathData, p1, p1, sections);
	var firstHalf = document.createElementNS(SVG_URI, "path");
	var secondHalf = document.createElementNS(SVG_URI, "path");
	//firstHalf.setAttribute("visibility", "hidden");
	//secondHalf.setAttribute("visibility", "hidden");
	firstHalf.setAttribute("id", shieldId+"-first");
	secondHalf.setAttribute("id", shieldId+"-second");
	firstHalf.setPathData(sections[0]);
	secondHalf.setPathData(sections[1]); 
	//insert just after given element
	elem.parentNode.insertBefore(secondHalf, elem.nextSibling);
	//insert just after given element, which is then just *before* secondHalf
	elem.parentNode.insertBefore(firstHalf, elem.nextSibling);
	applyTree(shieldId+"-first", tree.at(0));
	applyTree(shieldId+"-second", tree.at(1));
}

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
	blazon=str;
	clearShield();
	if(str!=""){
		parseStringAndDisplay(str)
		applyTree("shield", parseString(str))
	}
}

function nearlyEqual(a, b, eps=0.000001){
	absA=Math.abs(a);
	absB=Math.abs(b);
	diff=Math.abs(a-b);
	if( a===b ){//shortcut for infinity
		return true;
	}else if( a===0 || b===0 ){
		//a or b are zero, relative error is meaningless
		return diff < eps;
	}else{ //use relative error
		return diff/ (Math.min(absA+absB, Number.MAX_VALUE)) < eps;
	}
}

function twoDimCross(p1, p2){
	return (p1.x * p2.y - p1.y*p2.x);
}

//line 1 is p1->p2, line 2 is p3->p4
function fourPointIntersection(p1, p2, p3, p4){
	var r = Point.difference(p1, p2);
	var s = Point.difference(p3, p4);
	var rs = twoDimCross(r, s);
	var pq = Point.difference(p1, p3);
	var pqs = twoDimCross( pq, s );
	var pqr = twoDimCross( pq, r );
	if ( nearlyEqual(rs, 0) ){ //lines are colinear or parallel, intersection points are meaningless
			return [];
	}else{
		var t = pqs / rs;
		var u = pqr / rs;
		if( t>0 && t<1 && u>0 && u<1){ //intersection point is within segments
			var c = new Point(0, 0);
			c.x = p1.x + t*r.x;
			c.y = p1.y + t*r.y;
			return [c];
		}else{
			return [];
		}
	}
}

function bezierPoints(p0, p1, p2, p3, arr){
	ret = [];
	for(i of arr){
		ret.push( new Point( bezier(p0.x, p1.x, p2.x, p3.x, i), bezier(p0.y, p1.y, p2.y, p3.y, i) ) );
	}
	return ret;
}

//p0-p3 bezier control points, p4&p5 line segment end points
function bezierLineIntersection(p0, p1, p2, p3, p4, p5){
	var t = bezierIntersecitonParameters(p0, p1, p2, p3, p4, p5);
	return bezierPoints(p0, p1, p2, p3, t);
}

function bezierIntersectionParameters(p0, p1, p2, p3, p4, p5){
	var ldx = p4.x-p5.x;
	var ldy = p5.y-p4.y;
	var d = ldy*p0.x + ldx*p0.y - ldy*p4.x - ldx * p4.y;
	var c = -3*( (p0.x - p1.x)*ldy + (p0.y-p1.y)*ldx );
	var b = 3*( (p0.x - 2*p1.x + p2.x)*ldy + (p0.y - 2*p1.y + p2.y)*ldx );
	var a = -( (p0.x - 3*p1.x + 3*p2.x - p3.x)*ldy + (p0.y - 3*p1.y + 3*p2.y - p3.y)*ldx );
	var t = bezierRoots(a, b, c, d);
	return t;
}

//path data *must* be normalised, or garbage will result
function pathLineIntersection(pathData, p0, p1, paths=[]){
	var pos = new Point(0, 0);
	var fistPos = new Point(0, 0);
	var iPoints = [];
	//var paths=[];
	var currentPath=[];
	for( var seg of pathData ){
		if( seg.type === "M" || seg.type === "L"){
			var tmp=new Point(seg.values[0], seg.values[1]);
			if(seg.type === "L"){//calculate intersection with line between previous and new positions
				var tmpPoints = fourPointIntersection(p0, p1, pos, tmp);
				iPoints=iPoints.concat(tmpPoints);
				if(tmpPoints.length > 0){
					currentPath.push({ type:"L", values:[tmpPoints[0].x, tmpPoints[0].y] });
					currentPath.push({ type:"Z", values:[] });
					paths.push(currentPath);
					currentPath=[{ type:"M", values:[tmpPoints[0].x, tmpPoints[0].y] }];
					
				}
				currentPath.push({ type:"L", values:[seg.values[0], seg.values[1]] });
			}else{
				firstPos=tmp; //"M" command  resets the start point of the path
				//if we have a non-empty path, add it to the list
				if(currentPath.length>0){
					currentPath.push({type:"Z", values:[]});
					paths.push(currentPath);
				}
				//start a new path with this move command
				currentPath.push({ type:"M", values:[seg.values[0], seg.values[1]] });
			}
			pos=tmp;//update position
		}else if( seg.type === "C" ){//calculate intersecton of line and cubic bezier
			var tmp1 = new Point(seg.values[0], seg.values[1]);
			var tmp2 = new Point(seg.values[2], seg.values[3]);
			var tmp3 = new Point(seg.values[4], seg.values[5]);
			var ts=bezierIntersectionParameters(pos, tmp1, tmp2, tmp3, p0, p1);
			var oldBez=[pos.clone(), tmp1.clone(), tmp2.clone(), tmp3.clone()];
			var scale=1;
			var offset=0;
			for(var t of ts){
				var newBez=bezierSplit(...oldBez, (t-offset)/scale);
				oldBez=newBez.slice(-4);//last 4 elements
				scale=(1-t);
				offset=t;
				var bezObj=bezierPathSegmentFromPoints(...newBez.slice(0,4));//first four elements
				currentPath.push(bezObj);
				currentPath.push({ type:"Z", values:[] });
				paths.push(currentPath);
				currentPath=[ { type:"M", values:[newBez[3].x, newBez[3].y] } ]
			}
			//add the last bezier slice, iff it isn't infinitesimal (dropping it in this case gives minimal error)
			if(!nearlyEqual(ts.slice(-1)[0],1)){
				currentPath.push(bezierPathSegmentFromPoints(...oldBez));
			}
			var tmpPoints=bezierPoints(pos, tmp1, tmp2, tmp3, ts);
			iPoints=iPoints.concat(tmpPoints)
			pos = tmp3;//update position
		}else if( seg.type === "Z" ){//intersection using line between current position and start of path
			var tmpPoints=fourPointIntersection(p0, p1, pos, firstPos);
			iPoints=iPoints.concat(tmpPoints);
			if(tmpPoints.length>0){
				currentPath.push({type:"L", values:[tmpPoints[0].x, tmpPoints[0].y]});
				currentPath.push({type:"Z", values:[]});
				paths.push(currentPath);
				currentPath=[ { type:"M", values:[tmpPoints[0].x, tmpPoints[0].y] } ];
			}
			pos=firstPos.clone();
			currentPath.push({type:"L", values:[firstPos.x, firstPos.y]});//first point of cut up path probably won't be the same
		}
	}
	paths.push(currentPath);
	if(pathData.slice(-1)[0].type=="Z"){//if the path was closed, we append the first segment to the last
		var length=paths.length;
		var lastPath = paths[length-1];
		var firstPath = paths[0].slice(1);//we drop the initial move command, it's redundant
		paths[0]=lastPath.concat(firstPath);
		paths.pop();//drop last path, it is now part of the first
		//paths=paths.slice(1);//drop the original first path
	}
	return iPoints;
}

function bezierPathSegmentFromPoints(p0, p1, p2, p3){
	var ret={type:"C",values:[]};
	ret.values.push(p1.x);
	ret.values.push(p1.y);
	ret.values.push(p2.x);
	ret.values.push(p2.y);
	ret.values.push(p3.x);
	ret.values.push(p3.y);
	return ret;
}

function changeShield(d){
	var shield = document.getElementById("shield")
	var outline = document.getElementById("shieldOutline")
	var clip = document.getElementById("shield-clip")
	if(clip!=null){
		clip.parentNode.removeChild(clip);
	}
	shield.setAttribute("d", d);
	outline.setAttribute("d", d);
	setBlazon(blazon);
}

function setupMullet(){
	mullet=document.createElementNS(SVG_URI,"path");
	mullet.setAttribute("d",document.getElementById("mullet").contentDocument.getElementById("mullet").getAttribute("d"));
	mulletData=mullet.getPathData();
	mulletBox=mullet.getBBox();
	mulletData=shiftPathData(mulletData, -mulletBox.x, -mulletBox.y);
	mulletData=scalePathData(mulletData, 100/mulletBox.width);
	mulletData=roundPathData(mulletData);
	mullet.setPathData(mulletData);
}