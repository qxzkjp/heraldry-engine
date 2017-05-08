SVG_URI = "http://www.w3.org/2000/svg";
SVG_dots="<svg version=\"1.1\" baseProfile=\"full\" width=\"200\" height=\"200\" xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"100%\" height=\"100%\" fill=\"white\" /><circle cx=\"100\" cy=\"100\" r=\"60\" fill=\"black\" /><circle cx=\"0\" cy=\"200\" r=\"60\" fill=\"black\" /><circle cx=\"200\" cy=\"0\" r=\"60\" fill=\"black\" /><circle cx=\"0\" cy=\"0\" r=\"60\" fill=\"black\" /><circle cx=\"200\" cy=\"200\" r=\"60\" fill=\"black\" /></svg>";

//encode safely to base64 using open source libraries (included in head)
function base64EncodingUTF8(str) {
    var encoded = new TextEncoderLite('utf-8').encode(str);        
    var b64Encoded = base64js.fromByteArray(encoded);
    return b64Encoded;
}

function getEncodedSVG(name){
	return base64EncodingUTF8(document.getElementById(name).contentDocument.childNodes[0].outerHTML);
}

//objectsdo not count as pard of the DOM for dom-ready status, so we wait on them manually.
document.getElementById("dots").onload = function(){
	document.getElementById("idOut").value = getEncodedSVG("dots")
	console.log("Loaded "+document.getElementById("dots").data)
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
	var firstPoint = arr[0][1];
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
			pos = p[1];
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

function scalePath(path, c){
	newPath = clonePath(path);
	var centre = getCentre(newPath);
	for(var i=0; i<newPath.length; ++i){
		for(var j=0; j<newPath[i].length; ++j){
			if( newPath[i][j] instanceof Point){
				newPath[i][j].x= (newPath[i][j].x-centre.x)*c + centre.x;
				newPath[i][j].y= (newPath[i][j].y-centre.y)*c + centre.y;
			}else if( typeof newPath[i][j] ==="string" ){
				//do nothing
			}else if( typeof newPath[i][j] === "number" ){
				if( newPath[i][0] === "H" ){
					newPath[i][j] = (newPath[i][j]-centre.x)*c + centre.x;
				}else if(newPath[i][0] === "V"){
					newPath[i][j] = (newPath[i][j]-centre.y)*c + centre.y;
				}
			}
		}
	}
	return newPath;
}

function addPathToSVG(container, path){
	d = reassemblePath(path);
	pathElem=document.createElementNS(SVG_URI, "path");
	pathElem.setAttribute("d", d)
	container.appendChild(pathElem);
}

ermineSVG=document.getElementById("escutcheon");
erminePath=analyzePath(document.getElementById("ermineSpot").getAttribute("d"));
//drawBox("escutcheon", "ermineSpot", "ermineBox");
C = getCentre(erminePath);
dx = 50 - C.x;
dy = 50 - C.y;
centrePath = shiftPath(erminePath, dx, dy);
centrePath = scalePath(centrePath, 0.5);
changeErmine(centrePath);
topLeftPath=shiftPath(centrePath,-50,-50);
topRightPath=shiftPath(centrePath,50,-50);
bottomLeftPath=shiftPath(centrePath,-50,50);
bottomRightPath=shiftPath(centrePath,50,50);

addPathToSVG(ermineSVG, topLeftPath);
addPathToSVG(ermineSVG, topRightPath);
addPathToSVG(ermineSVG, bottomLeftPath);
addPathToSVG(ermineSVG, bottomRightPath);
