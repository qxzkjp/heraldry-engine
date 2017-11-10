var SVG_URI = "http://www.w3.org/2000/svg";
var shieldA = "M 20 5 L 180 5 C 180 5 182.113598 84.825528 167.003707 137.362438 C 151.893806 189.899348 102.105477 195.000018 100 195 C 97.894524 195 48.106195 189.899348 32.996295 137.362438 C 17.886404 84.825528 20 5 20 5 Z";
var shieldB = "M 20 12.5 L 179.999996 12.5 L 179.999996 81.366166 C 179.999996 157.611827 99.999998 187.500003 99.999998 187.500003 C 99.999998 187.500003 20 157.611827 20 81.366166 Z";;
var ellipsePath="M 178.94395 100.00129523543559 C 178.9439486833327 152.46810013894694 143.36494999690836 195.00084836477822 99.47604000000001 195.00084836477822 C 55.58713000309162 195.00084836477822 20.008140316667298 152.46810013894694 20.008139 100.00129523543559 C 20.008137683332645 47.53448810594624 55.58712814104286 5.001736375628019 99.47604000000001 5.001736375628008 C 143.36495185895717 5.0017363756280115 178.94395131666735 47.53448810594624 178.94395 100.00129523543559 Z";
var lozengePath="M 100 5 L 195 100 L 100 195 L 5 100 Z";
var erminePath="M 53.275904,37.171262 C 53.275904,38.896261 51.875904,40.296261 50.150905,40.296261 C 48.425906,40.296261 47.025907,38.896261 47.025907,37.171262 C 47.025907,35.446262 48.425906,34.046263 50.150905,34.046263 C 51.875904,34.046263 53.275904,35.446262 53.275904,37.171262 Z M 48.775904,42.671262 C 48.775904,44.396261 47.375904,45.796261 45.650904,45.796261 C 43.925905,45.796261 42.525906,44.396261 42.525906,42.671262 C 42.525906,40.946262 43.925905,39.546263 45.650904,39.546263 C 47.375904,39.546263 48.775904,40.946262 48.775904,42.671262 Z M 57.775904,42.671262 C 57.775904,44.396261 56.375904,45.796261 54.650905,45.796261 C 52.925906,45.796261 51.525907,44.396261 51.525907,42.671262 C 51.525907,40.946262 52.925906,39.546263 54.650905,39.546263 C 56.375904,39.546263 57.775904,40.946262 57.775904,42.671262 Z M 49.236555,41.014512 M 50.712549,41.116305 C 50.763446,50.73572 58.092524,65.953737 58.092524,65.953737 L 53.486402,61.5512 C 52.213993,63.027195 49.949104,65.953737 49.949104,65.953737 C 49.949104,65.953737 48.32042,63.001747 47.149803,61.525752 L 41.907477,65.90284 C 41.907477,65.90284 49.134761,50.837513 49.236555,41.014512 Z";
var blazon = "";
var chargePaths=[];
var CHARGE_SPACING=0.3;

var scratch=document.createElementNS(SVG_URI,"svg");
var SVG;
var jqSVG;
var SVG_MOVABLES;
var path;
var box;
var centre;

var debugStarts=[];
var debugPaths=[];
var debugEnds=[];
var pageDisabled = false;

$(document).ready(function(){
	var debugCookie = getCookie("debug");
	if(debugCookie!=""){
		enableDebugging();
	}
	//AJAX request to get the movable charges
	var xhr = new XMLHttpRequest;
	xhr.open('get','movables.svg',true);
	xhr.onreadystatechange = function(){
		if (xhr.readyState != 4) return;//4 means completed
		//load SVG document from request
		SVG_MOVABLES = xhr.responseXML.documentElement;
		SVG_MOVABLES = document.importNode(SVG_MOVABLES,true);
		SVG=document.getElementById("escutcheonContainer");
		jqSVG=$("#escutcheonContainer");
		//once charges are set up, draw the initial arms
		$('#blazonText')[0].value = "Azure, a bend Or";
		setBlazon($('#blazonText')[0].value);
		//and finally, now that everything is ready, enable the button
		$("#blazonButton").attr("disabled",false);
	};
	xhr.send();
	//set up shield
	path=document.getElementById("shield");
	box=path.getBBox();
	centre=new Point(box.x+(box.width/2), box.y+(box.height/2));
	
	$("#sideMenu").hide();
	if(getSyntaxCookie()==""){
		$("#syntax").hide();
	}else{
		$("#toggleSyntax").addClass("showing");
	}
	$("#menuButton").on("click", function(){
		animateSideMenu();
		/*$("#sideMenu").fadeToggle();*/
	});
	$("#toggleSyntax").on("click", function(evt){
		$("#syntax").slideToggle();
		if(getSyntaxCookie()==""){
			setSyntaxCookie("show");
			$("#toggleSyntax").addClass("showing");
		}else{
			setSyntaxCookie("");
			$("#toggleSyntax").removeClass("showing");
		}
	});
	//draw blazon when enter is pressed
	$("#blazonText").keypress(function (e) {
        if(e.which == 13) {
			e.preventDefault();
			e.stopPropagation();
            drawUserBlazon();
        }
    });
	//hide menu when "focus" is lost
    $("html").on("click", function (evt) {
        var isMenuClick = $.contains($("#menuContainer")[0], evt.target);
       if (!isMenuClick && !pageDisabled){
			animateSideMenu("hide");
		}
    });
});

function animateSideMenu(method="toggle"){
	$("#sideMenu").animate({"width":method},350);
}

var SVG_HEIGHT = 200;
var SVG_WIDTH = 200;
var SVG_MOVABLES=undefined;
var styleClass="heraldry-colour";

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
	this.width = 0;
	this.height = 0;
	this.x=Infinity;
	this.y=Infinity;
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

BoundingBox.prototype.finalise= function(){
	this.x = this.min.x;
	this.y = this.min.y;
	this.width = this.max.x - this.min.x;
	this.height = this.max.y - this.min.y;
}

//NOTE: this function cannot deal with arc commands
//TODO: make this function deal with arc commands
function getBoundingBox(arr, transform=undefined, centred=false){
	var secElem = document.createElementNS(SVG_URI, "path");
	var ret;
	secElem.setPathData(arr);
	if(transform!==undefined){
		var cx;
		var cy;
		if(centred){
			$(SVG).append(secElem)
			var cBox=secElem.getBBox();
			cx=cBox.x+cBox.width/2;
			cy=cBox.y+cBox.height/2;
			secElem.transform.baseVal.appendItem(
				SVG.createSVGTransformFromMatrix(
					SVG.createSVGMatrix().translate(cx,cy)
					)
				);
		}
		secElem.transform.baseVal.appendItem(transform);
		if(centred){
			secElem.transform.baseVal.appendItem(
				SVG.createSVGTransformFromMatrix(
					SVG.createSVGMatrix().translate(-cx,-cy)
					)
				);
		}
		var g=document.createElementNS(SVG_URI, "g");
		g.appendChild(secElem);
		SVG.appendChild(g);
		ret=g.getBBox();
		SVG.removeChild(g);
	}else{
		SVG.appendChild(secElem);
		ret=secElem.getBBox();
		SVG.removeChild(secElem);
	}
	return ret;
}


function rot45ac(p){
	return new Point( (p.x-p.y)/Math.sqrt(2), (p.x+p.y)/Math.sqrt(2) ); //anticlockwise 45deg rotation in SVG (right-handed) co-ordinates
}

function rot45(p){
	return new Point( (p.y+p.x)/Math.sqrt(2), (p.y-p.x)/Math.sqrt(2) ); //clockwise 45deg rotation in SVG (right-handed) co-ordinates
}

//path data must be normalised!
function pathDataBoundingBox(pathData, func){
	if(func === undefined){
		func = function (x){return x;}
	}
	var bBox = new BoundingBox;
	var firstPoint = new Point( 0, 0 );
	//var prevPoint = new Point( 0, 0 )
	var pos = new Point( 0, 0 )
	for (var seg of pathData){
		if( seg.type === "M" || seg.type === "L" ){
			pos.x = seg.values[0];
			pos.y = seg.values[1];
			pos=func(pos);
			bBox.addPoint(pos);
			if( seg.type === "M" ){
				firstPoint = pos.clone(); //M command resets beginning of path
			}
		}else if( seg.type === "Z" ){
			pos = firstPoint.clone();//don't need to add first point to bbox -- it's already there
		}else if( seg.type === "C"){
			var p1 = func( new Point (seg.values[0], seg.values[1]) );
			var p2 = func( new Point (seg.values[2], seg.values[3]) );
			var p3 = func( new Point (seg.values[4], seg.values[5]) );
			bBox.addPoints( ...bezierBox( pos, p1, p2, p3 ) );
			pos = p3;
		} 
	}
	bBox.finalise();
	return bBox;
}

//has to be window.onLoad: objects are not loaded at DOMready
//window.onload = function () { document.getElementById("idOut").value = getEncodedSVG("dots") }

function drawBox(container, shape, boxID){
	var esc=document.getElementById(container);
	var d=document.getElementById(shape).getAttribute("d");
	var boxElem = document.getElementById(boxID);
	var b=d.getPathData();
	var box=getBoundingBox(b);
	if(boxElem !== null){
		esc.removeChild(boxElem);
	}
	rect=document.createElementNS(SVG_URI, "rect");
	rect.setAttribute("x", box.x);
	rect.setAttribute("y", box.y);
	rect.setAttribute("width", box.width);
	rect.setAttribute("height", box.height);
	rect.setAttribute("fill", "transparent");
	rect.setAttribute("stroke", "black");
	rect.setAttribute("id", boxID);
	esc.appendChild(rect);
}

function getWidthAndHeight(path){
	var box=getBoundingBox(path);
	var w = box.width;
	var h = box.height;
	return [w,h];
}

function getCentre(path){
	var box=getBoundingBox(path);
	var cx = box.x + box.width/2;
	var cy = box.y + box.height/2;
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

function changeTincture(elem,tinct){
	//var elem = document.getElementById(id);
	elem.setAttribute("class", "heraldry-"+tinct);
}

function changeHeraldryCSS(fileName){
	var elem = document.getElementById("heraldry-css");
	elem.setAttribute("href","styles/"+fileName);
	/*allElem = $(SVG).find("*");
	allElem.removeClass("heraldry-colour");
	allElem.removeClass("heraldry-line");
	allElem.addClass(className);
	styleClass=className;*/
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
function createPale(elem, tinct, rw = 0.34, c = [], angle) {
    //convert angle to radians
    if (angle !== undefined) {
        angle = angle * Math.PI / 180;
    }
	var box=elem.getBBox();
	var h=box.height;
	var w=box.width;
    rw = w * rw;
    var rh=h;
    if (angle !== undefined) {
        //the rw/... term is to make sure the bottom edge crosses the boundary
        rh = w / Math.sin(angle) + rw / (2 * Math.tan(angle));
    }
	var rx=box.x+(w-rw)/2;
	var ry=box.y;
    if (angle !== undefined) {
        ry = box.y + w * (Math.cos(angle) - 1) / (2 * Math.sin(angle));
    }
    var cx = box.x + w / 2;
    var cy = box.x + h / 2;
    if (angle !== undefined) {
        cy = box.y + (w / Math.tan(angle)) / 2;
    }
    //add a little bit of padding to make sure there's an intersection
    var paddingScale = 0.01;
    ry -= paddingScale * rh / 2;
    rh *= 1 + paddingScale;
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
	var rx=box.x-5;//add five units of "padding" either sied to the fess
	rw=w+10;
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
	var p1=scratch.createSVGPoint();
	p1.x=c1;
	p1.y=c2;
	var p3=scratch.createSVGPoint();
	p3.x=p1.x+c3;
	p3.y=p1.y+c4;
	var p2=scratch.createSVGPoint();
	p2.x=p3.x;
	p2.y=p1.y;
	var p4=scratch.createSVGPoint();
	p4.x=p1.x;
	p4.y=p3.y;
	return [p1, p2, p3, p4];
}

function addFess(id, tinct, rw=0.3){
	var r=[];
	var elem=document.getElementById(id);
	var pathData = elem.getPathData();
	var fess=createFess(elem, tinct, rw, r);
	setClip(fess, id);
	//insert just after given element
	$(fess).insertAfter(elem);
	var P=getPoints(...r.slice(2));
	var sections=[];
	//[chief, 2/3]
	pathLineIntersection(pathData, P[0], P[1], sections);
	var debug1=displayPath(sections[0]);
	var debug2=displayPath(sections[1],"or");
	$(debug1).remove();
	$(debug2).remove();
	sections.sort(comparePathDataY);
	debug1=displayPath(sections[0]);
	debug2=displayPath(sections[1],"or");
	$(debug1).remove();
	$(debug2).remove();
	var newsections=[];
	pathLineIntersection(sections[1], P[3], P[2], newsections);
	debug1=displayPath(newsections[0]);
	debug2=displayPath(newsections[1],"or");
	$(debug1).remove();
	$(debug2).remove();
	newsections.sort(comparePathDataY);
	debug1=displayPath(newsections[0]);
	debug2=displayPath(newsections[1],"or");
	$(debug1).remove();
	$(debug2).remove();
	// [chief, fess, base]
	sections=[sections[0],...newsections];
	debug1=displayPath(sections[0]);
	debug2=displayPath(sections[1],"or");
	var debug3=displayPath(sections[2],"vert");
	$(debug1).remove();
	$(debug2).remove();
	$(debug3).remove();
	return sections;
}

function addPale(id, tinct, rw=0.3){
	var r=[];
	var elem=document.getElementById(id);
    var pathData = elem.getPathData();
    var pale = createPale(elem, tinct, rw, r);
    //insert just after given element
    $(pale).insertAfter(elem);
	setClip(pale, id);
	var P=getPoints(...r.slice(2));
	var sections=[];
	pathLineIntersection(pathData, P[1], P[2], sections);
	sections.sort(comparePathDataX);
	pathLineIntersection(sections[0], P[0], P[3], sections);
	// [2/3, sinister, dexter, pale]
	sections=sections.slice(1);
	sections.sort(comparePathDataX);
	return sections;
}

function addBend(id, tinct, rw=0.3){
	var elem=document.getElementById(id);
	var pathData = elem.getPathData();
    var c = [];
    var bendAngle = 45;
    var usedHeight = getBendHeightFraction(elem, bendAngle);
    if (usedHeight <= 0.5) {
        var box = elem.getBBox();
        bendAngle = Math.atan(box.width / (0.75 * box.height) ) * 180 / Math.PI;
    }
	var pale=createPale(elem, tinct, rw, c, bendAngle);
	//matrix for rotation by 45deg about centre
	var m=SVG.createSVGMatrix()
			.translate(c[0],c[1])
			.rotate(-bendAngle)
			.translate(-c[0],-c[1]);
	var g=document.createElementNS(SVG_URI, "g");
    g.appendChild(pale);
    //insert just after given element
    $(g).insertAfter(elem);
    setClip(g, id);
	//turn pale into bend
	pale=transformElement(pale,m);
	$(pale).addClass("bend");
	$(pale).attr("id", id+"-bend");
	//turn rectangle x,y,h,w into four SVG points (clockwise from top left)
	var P=getPoints(...c.slice(2));
	//apply 45 deg rotation to get true points
	for( var i=0; i<P.length; ++i ){
		P[i]=P[i].matrixTransform(m);
	}
	var sections=[];
	pathLineIntersection(pathData, P[1], P[2], sections);
	sections.sort(comparePathDataBendwise);
	//sections=[sinister-chief,2/3]
	pathLineIntersection(sections[1], P[0], P[3], sections);
	sections.splice(1,1);
	sections.sort(comparePathDataBendwise);
	return sections;
}

function addSinister(id, tinct, rw=0.3){
	var elem=document.getElementById(id);
	var pathData = elem.getPathData();
	var c=[];
	var pale=createPale(elem, tinct, rw, c, 45);
	//matrix for rotation by 45deg about centre
	var m=SVG.createSVGMatrix()
			.translate(c[0],c[1])
			.rotate(45)
			.translate(-c[0],-c[1]);
	var g=document.createElementNS(SVG_URI, "g");
	g.appendChild(pale);
	setClip(g,id);
	//apply matrix transform to pale
	pale=transformElement(pale, m);
	$(pale).addClass("bend-sinister");
	$(pale).attr("id", id+"-bend_sinister");
	//insert just after given element
	elem.parentNode.insertBefore(g, elem.nextSibling);
	//var m=pale.getCTM();
	//turn rectangle x,y,h,w into four SVG points (clockwise from top left)
	var P=getPoints(...c.slice(2));
	//apply 45 deg rotation to get true points
	for( var i=0; i<P.length; ++i ){
		P[i]=P[i].matrixTransform(m);
	}
	var sections=[];
	pathLineIntersection(pathData, P[0], P[3], sections);
	updateDebugDisplay(sections[0],0);
	updateDebugDisplay(sections[1],1);
	clearDebugDisplay();
	sections.sort(comparePathDataSinister);
	updateDebugDisplay(sections[0],0);
	updateDebugDisplay(sections[1],1);
	clearDebugDisplay();
	pathLineIntersection(sections[1], P[1], P[2], sections);
	// [sinister-chief, 2/3, bend, dexter-base]
	updateDebugDisplay(sections[0],0);
	updateDebugDisplay(sections[1],1);
	updateDebugDisplay(sections[2],2);
	updateDebugDisplay(sections[3],3);
	clearDebugDisplay();
	sections.splice(1,1);
	updateDebugDisplay(sections[0],0);
	updateDebugDisplay(sections[1],1);
	updateDebugDisplay(sections[2],2);
	clearDebugDisplay();
	sections.sort(comparePathDataSinister);
	updateDebugDisplay(sections[0],0);
	updateDebugDisplay(sections[1],1);
	updateDebugDisplay(sections[2],2);
	clearDebugDisplay();
	return sections;
}

var tree = parseString("Azure, a bend or");

tinctureClasses = [
	"heraldry-unspecified",
	"heraldry-argent",
	"heraldry-or",
	"heraldry-gules",
	"heraldry-azure",
	"heraldry-vert",
	"heraldry-purpure",
	"heraldry-sable",
	"heraldry-tenny",
	"heraldry-sanguine",
	"heraldry-vair",
	"heraldry-countervair",
	"heraldry-potent",
	"heraldry-counterpotent",
	"heraldry-ermine",
	"heraldry-ermines",
	"heraldry-erminois",
	"heraldry-pean"
	]

/*immovables = [
	"chief",
	"pale",
	"fess",
	"bend",
	"bend sinister",
	"chevron",
	"saltire",
	"pall",
	"cross",
	"pile",
	"bordure",
	"orle",
	"tressure",
	"canton",
	"flanches",
	"gyron",
	"fret"];*/
/*divisions = [
	"per pale",
	"per fess",
	"per bend",
	"per bend sinister",
	"per chevron",
	"per saltire",
	"",
	"",
	"",
	"",
	"",
	"",
	"",""
	];*/

function applyTree(shieldId, tree){
	if(tree===undefined){
		console.error(
		"Rendering error: no escutcheon to display (probably due to a catastrophic parsing error)"
        );
        return false;
	}
	//var shield=document.getElementById(shieldId);
	var elem = document.getElementById(shieldId);
	var fields=0; //how many nodes must we skip over to get to the charges?
	upsideDownFirst=false;
	upsideDownSecond=false;
	if( tree instanceof Field ){
		var tinct = tinctures[tree.tincture];
		changeTincture(elem, tinct);
	}else if(tree instanceof Division){
		fields=2;
		//var elem = document.getElementById(shieldId);
		var pathData = elem.getPathData();
		var shieldBox = elem.getBBox();
		var p1,p2;
		var sections;
		if( tree.type === 0){//per pale
			var midx = shieldBox.x + shieldBox.width/2;
			p1=new Point(midx, 0);
			p2=new Point(midx, SVG_HEIGHT);
			sections = partyPerPoints(elem, p1, p2, tree);
			sections.sort(comparePathDataX);
		}else if(tree.type==1){//per fess
			var midy = shieldBox.y + shieldBox.height/2;
			p1=new Point(0, midy);
			p2=new Point(SVG_WIDTH, midy);
			sections = partyPerPoints(elem, p1, p2, tree);
			sections.sort(comparePathDataY);
		}else if(tree.type==2){//per bend
			var far = Math.max(shieldBox.width, shieldBox.height);
			far*=1.1;
			p1=new Point(shieldBox.x -10, shieldBox.y-10);
			p2=new Point(shieldBox.x + far, shieldBox.y + far);
			sections = partyPerPoints(elem, p1, p2, tree);
		}else if(tree.type==3){//per bend sinister
			var farx = shieldBox.x + shieldBox.width;
			//var fary = shieldBox.y + shieldBox.height;
			var far = Math.max(shieldBox.width, shieldBox.height);
			far*=1.1;
			p1=new Point(farx+10, shieldBox.y-10);
			p2=new Point(farx - far, shieldBox.y + far);
			sections = partyPerPoints(elem, p1, p2, tree);
		}else{
			console.error("division not implemented");
			return;
		}
		var firstHalf = document.createElementNS(SVG_URI, "path");
		var secondHalf = document.createElementNS(SVG_URI, "path");
		//firstHalf.setAttribute("visibility", "hidden");
		//secondHalf.setAttribute("visibility", "hidden");
		firstHalf.setAttribute("id", shieldId+"-first");
		secondHalf.setAttribute("id", shieldId+"-second");
		firstHalf.setPathData(sections[0]);
		secondHalf.setPathData(sections[1]);
		//make overall shield invisible
		elem.setAttribute("class", "heraldry-invisible");
		//insert just before given element
		elem.parentNode.insertBefore(firstHalf, elem);
		//insert just before given element, which is then just *after* firstHalf
		elem.parentNode.insertBefore(secondHalf, elem);
		applyTree(shieldId+"-first", tree.at(0));
		applyTree(shieldId+"-second", tree.at(1));
	}
	if( tree.subnode.length > fields ){//if there are nodes after the division nodes (if any), display them as charges
		var sections;
		if(tree.at(fields).type===TYPE_IMMOVABLE){
			if(tree.at(fields).index===1){//pale
				sections = addPale(shieldId, tree.at(fields).at(0).tincture);
			}else if(tree.at(fields).index===2){//fess
				sections = addFess(shieldId, tree.at(fields).at(0).tincture);
			}else if(tree.at(fields).index===3){//bend
				sections = addBend(shieldId, tree.at(fields).at(0).tincture);
				upsideDownSecond=true;
			}else if(tree.at(fields).index===4){//bend sinister
				sections = addSinister(shieldId, tree.at(fields).at(0).tincture);
				upsideDownSecond=true;
			}
			if(tree.at(fields).subnode.length > 1){
				if(tree.at(fields).at(1) instanceof Charge){
					var secElem = document.createElementNS(SVG_URI, "path")
					secElem.id=elem.id+"-dexter";
					secElem.setPathData(sections[0]);
					elem.parentNode.insertBefore(secElem, elem.nextSibling);//insert just after field
					addCharge(secElem,
						elem.id,
						tree.at(fields).at(1).index,
						tree.at(fields).at(1).number,
						tree.at(fields).at(1).at(0).tincture,
						upsideDownFirst,
						tree.at(fields).at(1).mirrored,
						tree.at(fields).at(1).orientation
						);
					secElem.parentNode.removeChild(secElem);
				}
				if(tree.at(fields).at(2) instanceof Charge){
					var secElem = document.createElementNS(SVG_URI, "path")
					secElem.id=elem.id+"-sinister";
					secElem.setPathData(sections[2]);
					elem.parentNode.insertBefore(secElem, elem.nextSibling);//insert just after field
					addCharge(secElem,
						elem.id,
						tree.at(fields).at(2).index,
						tree.at(fields).at(2).number,
						tree.at(fields).at(2).at(0).tincture,
						upsideDownSecond,
						tree.at(fields).at(2).mirrored,
						tree.at(fields).at(2).orientation
						);
					secElem.parentNode.removeChild(secElem);
				}
			}
		}else if(tree.at(fields).type===TYPE_MOVABLE){
			addCharge(elem,
						elem.id,
						tree.at(fields).index,
						tree.at(fields).number,
						tree.at(fields).at(0).tincture,
						false,
						tree.at(fields).mirrored,
						tree.at(fields).orientation
						);
		}
		/*movables = [
			"mullet",
			"phrygian cap",
			"fleur-de-lis",
			"pheon",
			"moveable-chevron",
			"inescutcheon",
			"billet",
			"lozenge",
			"key",
			"phrygian cap with bells on"
			];*/
	}
}

function addCharge(
	elem,
	clipId,
	index,
	number,
	tinct,
	upsideDown=false,
	mirror=false,
	rotation=0,
	sequence=0
	){
	var bBox = elem.getBBox();
	var charge = SVG_MOVABLES.getElementById(movables[index]).cloneNode(true);
	charge.id="temp-1";
	$(SVG).append(charge);
	var cBox = charge.getBBox();
	$(charge).remove();
	var pathData = elem.getPathData();
	var cx = bBox.x + bBox.width/2;
	var cy = bBox.y + bBox.height/2;
	var ccx = cBox.x + cBox.width/2;
	var ccy = cBox.y + cBox.height/2;
	var aspectRatio = cBox.width / cBox.height;
	var chHeight = 2 * bBox.height / 3;
	var step = chHeight/50;
	if(number === 1){
		var newCharge=charge.cloneNode(true);
		var nominalHeight = cBox.height;
		if(rotation!=0){
			var t = SVG.createSVGTransformFromMatrix(
				SVG.createSVGMatrix()
					.rotate(-45*rotation)
					.translate(-ccx,-ccy)
				);
			var g=document.createElementNS(SVG_URI, "g");
			$(g).append(charge);
			$(SVG).append(g);
			charge.transform.baseVal.appendItem(t);
			var tBox=g.getBBox();
			aspectRatio = tBox.width / tBox.height;
			nominalHeight=tBox.height;
			$(g).remove();
		}
		var debugBox=document.createElementNS(SVG_URI, "rect");
		$(debugBox).attr("class","heraldry-vert");
		$(debugBox).css({"opacity":0.5});
		$(SVG).append($(debugBox));
		while(true){
			var topPoints = pathLineIntersection(pathData, new Point(0, cy + chHeight/2), new Point(SVG_WIDTH, cy + chHeight/2));
			var midPoints = pathLineIntersection(pathData, new Point(0, cy), new Point(SVG_WIDTH, cy));
			var bottomPoints = pathLineIntersection(pathData, new Point(0, cy - chHeight/2), new Point(SVG_WIDTH, cy - chHeight/2));
			topPoints.sort(comparePointsX);
			midPoints.sort(comparePointsX);
			bottomPoints.sort(comparePointsX);
			var topWidth = Math.abs(topPoints[topPoints.length-1].x - topPoints[0].x) * 0.95;
			var midWidth = Math.abs(midPoints[midPoints.length-1].x - midPoints[0].x);
			var bottomWidth = Math.abs(bottomPoints[bottomPoints.length-1].x - bottomPoints[0].x);
			var maxWidth = Math.max(topWidth, midWidth, bottomWidth)*0.95; //leave 5% buffer space for a snug fit
			var minX=Math.max(topPoints[0].x,midPoints[0].x,topPoints[0].x);
			var maxX=Math.min(
				topPoints[topPoints.length-1].x,
				midPoints[midPoints.length-1].x,
				bottomPoints[bottomPoints.length-1].x
				);
			var minWidth=Math.min(maxX-minX,maxWidth);
			/**********/
			var middleX = (maxX+minX)/2;
			var Ypoints=pathLineIntersection(pathData, new Point(middleX, 0), new Point(middleX, SVG_HEIGHT));
			Ypoints.sort(comparePointsY);
			var middleY = (Ypoints[0].y + Ypoints[Ypoints.length-1].y)/2;
			/**********/
			$(debugBox).attr("x",middleX-chHeight*aspectRatio/2);
			$(debugBox).attr("y",middleY-chHeight/2);
			$(debugBox).attr("width",chHeight*aspectRatio);
			$(debugBox).attr("height",chHeight);
			if( chHeight * aspectRatio < minWidth ){
				//draw charge
				elem.parentNode.insertBefore(newCharge, elem.nextSibling);//insert just after field
				//console.log("Charge height " + chHeight +" fits")
				var scale = chHeight/nominalHeight;
				var translateX = middleX;
				var translateY = middleY;
				//matrix for transform (shift to 0,0 scale then shift to new position)
				var m=SVG.createSVGMatrix()
						.translate(translateX,translateY)
						.rotate(-45*rotation);
				if(mirror){
					m=m.flipX();
				}
					m=m.scale(scale)
						.translate(-ccx,-ccy);
				newCharge.id=clipId+"-charge"+sequence.toString();
				transformElement(newCharge, m);
				var children=newCharge.children;
				for (var i = 0; i < children.length; i++) {
					var childElem = children[i];
					if(childElem.dataset.tinctured==="true"){
						$(childElem).addClass("heraldry-"+tinctures[tinct]);
					}else{
						$(childElem).addClass("heraldry-charge");
					}
				}
				if(clipId!=""){
					setClip(newCharge, clipId);
				}
				break;
			}
			chHeight -= step;
			if(chHeight<0){
				console.error("Could not fit charge in field!");
				break;
			}
		}
		$(debugBox).remove();
		
	}else if( number === 2){
		var sections = [];
		if(upsideDown){
			pathLineIntersection(pathData, new Point(cx, 0), new Point(cx, SVG_HEIGHT), sections);
		}else{
			pathLineIntersection(pathData, new Point(0, cy), new Point(SVG_WIDTH, cy), sections);
		}
		var el1 = document.createElementNS(SVG_URI, "path");
		var el2 = document.createElementNS(SVG_URI, "path");
		el1.setPathData(sections[0]);
		el2.setPathData(sections[1]);
		elem.parentNode.insertBefore(el2, elem.nextSibling);
		elem.parentNode.insertBefore(el1, elem.nextSibling);
		addCharge(el1, clipId, index, 1, tinct, false, mirror, rotation, sequence);
		//var testFlag = testPathData($("#"+clipId+"-charge"+sequence.toString())[0]);
		addCharge(el2, clipId, index, 1, tinct, false, mirror, rotation, sequence+1);
		el1.parentElement.removeChild(el1);
		el2.parentElement.removeChild(el2);
		
	}else if( number === 3){
		var numAbove=2;
		var numBelow=1;
		if(upsideDown){
			numAbove=1;
			numBelow=2;
		}
		var sections = [];
		var subsections =[];
		pathLineIntersection(pathData, new Point(0, cy), new Point(SVG_WIDTH, cy), sections);
		sections.sort(comparePathDataY);
		var el1 = document.createElementNS(SVG_URI, "path");
		var el2 = document.createElementNS(SVG_URI, "path");
		el1.setPathData(sections[0]);
		el2.setPathData(sections[1]);
		$(el2).insertAfter(elem);
		$(el1).insertAfter(elem);
		addCharge(el1, clipId, index, numAbove, tinct, true, mirror, rotation, sequence);
		addCharge(el2, clipId, index, numBelow, tinct, true, mirror, rotation, sequence+numAbove);
		el1.parentElement.removeChild(el1);
		el2.parentElement.removeChild(el2);
	}else{
		console.error("I don't know how to draw a group of "+number+" charges.");
	}
	$(charge).remove();
	var rowNums = getRowNumbers(number);
	var numRows = rowNums.length;
}

function comparePointsY(p1, p2){
	if(p1.y<p2.y){
		return -1;
	}else if(p1.y>p2.y){
		return 1;
	}else{
		return 0;
	}
}

function comparePointsX(p1, p2){
	if(p1.x<p2.x){
		return -1;
	}else if(p1.x>p2.x){
		return 1;
	}else{
		return 0;
	}
}

function comparePathDataX(pd1, pd2){
	var box1 = pathDataBoundingBox(pd1);
	var box2 = pathDataBoundingBox(pd2);
	if(box1.max.x > box2.max.x){
		return 1;
	}else if(box1.max.x < box2.max.x){
		return -1;
	}else if(box1.min.x > box2.min.x){
		return 1;
	}else if(box1.min.x < box2.min.x){
		return -1;
	}else{
		return 0;
	}
}

function comparePathDataY(pd1, pd2){
	var box1 = getBoundingBox(pd1);
	var box2 = getBoundingBox(pd2);
	if(box1.y+box1.height < box2.y+box2.height){
		return -1;
	}else if(box1.y+box1.height > box2.y+box2.height){
		return 1;
	}else if(box1.y < box2.y){
		return -1;
	}else if(box1.y > box2.y){
		return 1;
	}else{
		return 0;
	}
}

function comparePathDataBendwise(pd1, pd2){
	var rot45t=SVG.createSVGTransformFromMatrix(SVG.createSVGMatrix().rotate(-45))
	var box1 = getBoundingBox(pd1, rot45t);
	var box2 = getBoundingBox(pd2, rot45t);
	//smaller y = HIGHER
	if(box1.y+box1.height < box2.y+box2.height){
		return -1;
	}else if(box1.y+box1.height > box2.y+box2.height){
		return 1;
	}else if(box1.y < box2.y){
		return -1;
	}else if(box1.y > box2.y){
		return 1;
	}else{
		return 0;
	}
}

function comparePathDataSinister(pd1, pd2){
	updateDebugDisplay(pd1,0);
	updateDebugDisplay(pd2,1);
	clearDebugDisplay();
	var rot45t=SVG.createSVGTransformFromMatrix(SVG.createSVGMatrix().translate(100,100).rotate(45).translate(-100,-100));
	var box1 = getBoundingBox(pd1, rot45t);
	var box2 = getBoundingBox(pd2, rot45t);
	updateDebugDisplay(box1,0);
	updateDebugDisplay(box2,0);
	clearDebugDisplay();
	if(box1.y < box2.y){
		return -1;
	}else if(box1.y > box2.y){
		return 1;
	}else if(box1.y+box1.height < box2.y+box2.height){
		return -1;
	}else if(box1.y+box1.height > box2.y+box2.height){
		return 1;
	}else{
		return 0;
	}
}

function partyPerPoints(elem, p1, p2, tree){
	//var elem = document.getElementById(shieldId);
	var pathData = elem.getPathData();
	var shieldId = elem.getAttribute("id");
	var sections = [];
	pathLineIntersection(pathData, p1, p2, sections);
	return sections;
}

function clearShield(){
	var shield = document.getElementById("shield");
	var outline = document.getElementById("shieldOutline");
	var clip = document.getElementById("shield-clip");
	var esc = document.getElementById("escutcheon");
	scratch.appendChild(shield);
	scratch.appendChild(outline);
	while (esc.lastChild) {
		esc.removeChild(esc.lastChild);
	}
	var rem = document.querySelectorAll("[id*='-clip']");
	for (var i=0; i < rem.length; i++)
		rem[i].parentNode.removeChild(rem[i]);
	esc.appendChild(shield);
	esc.appendChild(outline);
	changeTincture(shield, "ermine");
}

function setBlazon(str){
	blazon=str;
	clearShield();
    var tree = parseStringAndDisplay(str);
    applyTree("shield", tree);
	//$(SVG).find("*").addClass(styleClass);
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
		if( t>0 && t<=1 && u>0 && u<=1){ //intersection point is within segments
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

function updateDebugDisplay(currentPath, index){
}

function updateDebugDisplayBackup(currentPath, index, transparent=false){
	var colours=["purple","brown","orange","pink","blue"];
	if(currentPath===undefined){
		debugger;
	}
	if(currentPath instanceof SVGRect){
		var x = currentPath.x;
		var y = currentPath.y;
		var width = currentPath.width;
		var height = currentPath.height;
		currentPath=[{type:"M",values:[x,y]},{type:"L",values:[x+width,y]},{type:"L",values:[x+width,y+height]},{type:"L",values:[x,y+height]},{type:"Z",values:[]}];
	}
	if(index===undefined){
		clearDebugDisplay();
		index=0;
	}
	if(debugPaths[index]===undefined){
		debugPaths.push(document.createElementNS(SVG_URI,"path"));
		$(debugPaths[index]).attr("fill",colours[index]);
		debugStarts.push(document.createElementNS(SVG_URI,"ellipse"));
		$(debugStarts[index]).attr("fill",colours[index]);
		$(debugStarts[index]).attr("rx",5);
		$(debugStarts[index]).attr("ry",5);
		$(debugStarts[index]).attr("stroke","green");
		$(debugStarts[index]).attr("stroke-width",0.5);
		debugEnds.push(document.createElementNS(SVG_URI,"ellipse"));
		$(debugEnds[index]).attr("fill",colours[index]);
		$(debugEnds[index]).attr("rx",5);
		$(debugEnds[index]).attr("ry",5);
		$(debugEnds[index]).attr("stroke","red");
		$(debugEnds[index]).attr("stroke-width",0.5);
		$(SVG).append(debugPaths[index]);
		$(SVG).append(debugEnds[index]);
		$(SVG).append(debugStarts[index]);
	}
	if(transparent){
		$(debugPaths[index]).attr("opacity",0.5);
	}else{
		$(debugPaths[index]).attr("opacity",1);
	}
	debugPaths[index].setPathData(currentPath);
	var l=currentPath.length;
	if(currentPath[l-1].type!="Z"){
		var vlen= currentPath[l-1].values.length;
		$(debugEnds[index]).attr("cx",currentPath[l-1].values[vlen-2]);
		$(debugEnds[index]).attr("cy",currentPath[l-1].values[vlen-1]);
		$(debugEnds[index]).attr("fill","red");
		$(debugEnds[index]).show();
	}else{
		$(debugEnds[index]).hide();
	}
	$(debugStarts[index]).attr("cx",currentPath[0].values[0]);
	$(debugStarts[index]).attr("cy",currentPath[0].values[1]);
}

function clearDebugDisplay(){
}

function clearDebugDisplayBackup(){
	for( p of debugPaths){
		$(p).remove();
	}
	for( p of debugStarts){
		$(p).remove();
	}
	for( p of debugEnds){
		$(p).remove();
	}
	debugPaths=[];
	debugStarts=[];
	debugEnds=[];
}

//path data *must* be normalised, or garbage will result
//this function changes p0 and p1. I should probably change this.
function pathLineIntersection(pathData, p0, p1, paths=[]){
	var indexOffset = paths.length;
	var pos = new Point(0, 0);
	var fistPos = new Point(0, 0);
	var iPoints = [];
	var currentPath=[];
	for( var seg of pathData ){
		if( seg.type === "M" || seg.type === "L"){
			var tmp=new Point(seg.values[0], seg.values[1]);
			if(seg.type === "L"){//calculate intersection with line between previous and new positions
				var tmpPoints = fourPointIntersection(p0, p1, pos, tmp);
				iPoints=iPoints.concat(tmpPoints);
				if(tmpPoints.length > 0){
					currentPath.push({ type:"L", values:[tmpPoints[0].x, tmpPoints[0].y] });
					updateDebugDisplay(currentPath)
					currentPath.push({ type:"Z", values:[] });
					updateDebugDisplay(currentPath)
					paths.push(currentPath);
					currentPath=[{ type:"M", values:[tmpPoints[0].x, tmpPoints[0].y] }];
					
				}
				currentPath.push({ type:"L", values:[seg.values[0], seg.values[1]] });
				updateDebugDisplay(currentPath)
			}else{
				firstPos=tmp; //"M" command  resets the start point of the path
				//if we have a non-empty path, add it to the list
				if(currentPath.length>0){
					currentPath.push({type:"Z", values:[]});
					updateDebugDisplay(currentPath)
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
				updateDebugDisplay(currentPath)
				currentPath.push({ type:"Z", values:[] });
				updateDebugDisplay(currentPath)
				paths.push(currentPath);
				currentPath=[ { type:"M", values:[newBez[3].x, newBez[3].y] } ]
			}
			//add the last bezier slice, iff it isn't infinitesimal (dropping it in this case gives minimal error)
			if(!nearlyEqual(ts.slice(-1)[0],1)){
				currentPath.push(bezierPathSegmentFromPoints(...oldBez));
				updateDebugDisplay(currentPath)
			}
			var tmpPoints=bezierPoints(pos, tmp1, tmp2, tmp3, ts);
			iPoints=iPoints.concat(tmpPoints)
			pos = tmp3;//update position
		}else if( seg.type === "Z" ){//intersection using line between current position and start of path
			var tmpPoints=fourPointIntersection(p0, p1, pos, firstPos);
			iPoints=iPoints.concat(tmpPoints);
			if(tmpPoints.length>0){
				currentPath.push({type:"L", values:[tmpPoints[0].x, tmpPoints[0].y]});
				updateDebugDisplay(currentPath)
				currentPath.push({type:"Z", values:[]});
				updateDebugDisplay(currentPath)
				paths.push(currentPath);
				currentPath=[ { type:"M", values:[tmpPoints[0].x, tmpPoints[0].y] } ];
			}
			pos=firstPos.clone();
			currentPath.push({type:"L", values:[firstPos.x, firstPos.y]});//first point of cut up path probably won't be the same
			updateDebugDisplay(currentPath)
		}
	}
	var l=currentPath.length;
	if(currentPath[l-1].type!="Z"){
		currentPath.push({type:"Z",values:[]});
		updateDebugDisplay(currentPath)
	}
	paths.push(currentPath);
	if(pathData.slice(-1)[0].type=="Z" && paths.length>1){//if the path was closed, we append the first segment to the last
		var length=paths.length;
		//we drop the ending "Z" command, as this is no longer the end of the path
		var lastPath = paths[length-1].slice(0,-1);
		updateDebugDisplay(lastPath);
		var firstPath = paths[indexOffset];
		updateDebugDisplay(firstPath);
		//change the initial positioning command into a command to draw a line to that point
		firstPath[0].type="L";
		paths[indexOffset]=lastPath.concat(firstPath);
		updateDebugDisplay(paths[indexOffset]);
		paths.pop();//drop last path, it is now part of the first
		//paths=paths.slice(1);//drop the original first path
	}
	clearDebugDisplay();
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
	/*var clip = document.getElementById("shield-clip")
	if(clip!=null){
		clip.parentNode.removeChild(clip);
	}*/
	shield.setAttribute("d", d);
	outline.setAttribute("d", d);
	setBlazon(blazon);
}

function getGreatestTriangularIndex(n){
	var T=0;
	var ret=0;
	for(;;++ret){
		T+=ret+1;
		if(T>n)
			break;
	}
	T-=ret+1;
	return [ret,T];
}

function getRowNumbers(n){
	var [k, T] = getGreatestTriangularIndex(n);
	var d=n-T;
	var rows=[];
	for(var i=0; i<=k; ++i)//[k, k-1, ..., 1, 0]
		rows.push(k-i);
	for(var i=0; i<d; ++i)
		++rows[k-i]
	if(rows[k]===1 && rows[k-1]===1 && n>2){//special case for two, as it would be wider than long otherwise
		rows[k]=0;
		rows[k-1]=2;
	}
	if(rows[k]===0)
		rows.pop();
	return rows;
}

function getRowPositions(arr, spacing=0){
	var ret=[];
	var cy = (arr.length-1)/2
	for(var i = 0; i<arr.length; ++i){
		var cx = (arr[i]-1)/2;
		var row=[];
		for(var j=0;j<arr[i];++j){
			row.push([(j-cx)*(1+spacing),(i-cy)*(1+spacing)]);
		}
		ret.push(row);
	}
	return ret;
}

function displayPath(pd, tincture="gules"){
	var pathElem = document.createElementNS(SVG_URI, "path");
	pathElem.setPathData(pd);
	pathElem.setAttribute("class","heraldry-"+tincture);
	SVG.appendChild(pathElem);
	return pathElem;
}

function transformPathData(pd, m){
	var ret=clonePathData(pd); //deep copy
	for(i in ret){
		var numPoints = Math.floor(ret[i].values.length / 2);
		for(var j =0; j<numPoints; ++j){
			var x=ret[i].values[j*2];
			var y=ret[i].values[j*2+1];
			ret[i].values[j*2] = m.a * x + m.c * y + m.e;
			ret[i].values[j*2+1] = m.b * x + m.d * y + m.f;
		}
	}
	return ret;
}

function transformPoint(p, m){
	var x=p.x;
	var y=p.y;
	ret=SVG.createSVGPoint();
	ret.x = m.a * x + m.c * y + m.e;
	ret.y = m.b * x + m.d * y + m.f;
	return ret;
}

function transformElement(elem, m){
	var ret=elem;
	if(elem.tagName==="path"){
		elem.setPathData(transformPathData(elem.getPathData({normalize:true}), m));
	}else if(elem.tagName==="rect" || elem.tagName==="ellipse"){
		var newPath = document.createElementNS(SVG_URI,"path");
		var classes = $(elem).attr("class");
		newPath.setPathData(transformPathData(elem.getPathData({normalize:true}), m));
		$(newPath).attr("class", classes);
		$(newPath).insertBefore(elem);
		$(elem).remove();
		ret = newPath;
	}else if(elem.tagName==="g"){
		var children=$(elem).children();
		var l=children.length;
		for(var i=0; i<l; ++i){
			transformElement(children[i], m);
		}
	}else{
		console.error("Transform Element: unknown element type");
	}
	return ret;
}

function testPathData(elem){
	if(elem.tagName==="path"){
		return !!(elem.getPathData)
	}else if(elem.tagName==="rect" || elem.tagName==="ellipse"){
		return !!(elem.getPathData)
	}else if(elem.tagName==="g"){
		var children=$(elem).children();
		var l=children.length;
		ret=true;
		for(var i=0; i<l; ++i){
			ret = ret && testPathData(children[i]);
		}
		return ret;
	}else{
		console.error("Test Path Data: unknown element type");
		return false;
	}
}

function setCTM(elem,m) {
  return elem.transform.baseVal.initialize(
    elem.ownerSVGElement.createSVGTransformFromMatrix(m));
}

function elementCentre(elem){
	var b=elem.getBBox();
	var ret=SVG.createSVGPoint();
	ret.x=b.x+b.width/2;
	ret.y=b.y+b.height/2;
	return ret;
}

/*JQuery extension to get a list of classes -- updated to work on SVG*/
$.fn.classList = function() {
	var names = this[0].className;
	if(names.split===undefined){
		names=this[0].className.baseVal;
	}
	return names.split(/\s+/);
};

function clearTransformations(elem){
	elem.transform.baseVal.initialize(elem.ownerSVGElement.createSVGTransform());
	var children=$(elem).children();
	var l = children.length;
	for(var i=0; i<l; ++i){
		clearTransformations(children[i]);
	}
}


/*use
elem.outerHTML.replace(/><\/line>/g,"/>").replace(/></g,">\n<")
to get nicely formatted output*/
function crossHatch(size, number=4, width=2, vertical=true, horizontal=true){
	var outElem=document.createElementNS(SVG_URI, "g");
	$(outElem).attr("stroke","black")
	$(outElem).attr("stroke-width",width)
	$(outElem).attr("stroke-linecap","square")
	for(var i=0;i<number;++i){
		var x=(2*i+1)*size/(2*number);
		if(vertical){
			var lineX=document.createElementNS(SVG_URI, "line");
			$(lineX).attr("x1",x);
			$(lineX).attr("x2",x);
			$(lineX).attr("y1",0);
			$(lineX).attr("y2",size);
			$(outElem).append(lineX);
		}
		if(horizontal){
			var lineY=document.createElementNS(SVG_URI, "line");
			$(lineY).attr("x1",0);
			$(lineY).attr("x2",size);
			$(lineY).attr("y1",x);
			$(lineY).attr("y2",x);
			$(outElem).append(lineY);
		}
	}
	return outElem;
}

/*use
elem.outerHTML.replace(/><\/ellipse>/g,"/>").replace(/></g,">\n<")
to get nicely formatted output*/
function dotty(size, number, radius){
	var g=document.createElementNS(SVG_URI,"g");
	for(var i=0;i<number+1;++i){
		y=i*size/number;
		for(var j=0;j<number+(i+1)%2;++j){
			x=j*size/number + (size/(2*number))*(i%2);
			var elem=document.createElementNS(SVG_URI,"ellipse");
			$(elem).attr("cx",x);
			$(elem).attr("cy",y);
			$(elem).attr("rx",radius);
			$(elem).attr("ry",radius);
			$(elem).attr("fill","black");
			$(g).append(elem);
		}
	}
	return g;
}

function drawUserBlazon(){
	setBlazon(document.getElementById('blazonText').value);
}

function getSyntaxCookie(){
	//magic incantation, do not touch
	return document.cookie.replace(/(?:(?:^|.*;\s*)showSyntax\s*\=\s*([^;]*).*$)|^.*$/, "$1");
}

function getCookie(name){
	//magic incantation, do not touch
	var regexp = new RegExp("(?:(?:^|.*;\\s*)"+name+"\\s*\\=\\s*([^;]*).*$)|^.*$");
	return document.cookie.replace(regexp, "$1");
}

function setSyntaxCookie(val){
	document.cookie = "showSyntax=" + val + ";max-age=31536000";
}

function setCookie(name, val){
	document.cookie = name + "=" + val + ";max-age=31536000";
}

function enableDebugging(){
	updateDebugDisplay=updateDebugDisplayBackup;
	clearDebugDisplay=clearDebugDisplayBackup;
	setCookie("debug","true");
}

function disableDebugging(){
	updateDebugDisplay=function(){};
	clearDebugDisplay=function(){};
	setCookie("debug","");
}

function getBendHeightFraction(elem, angle) {
    var box = elem.getBBox();
    var w = box.width;
    var h = box.height;
    return w / (Math.tan(angle) * h);
}

function disablePage() {
    $("body").append('<div id="disableBackground"></div>')
    $("html").addClass("noOverflow");
    pageDisabled = true;
}

function enablePage() {
    $("#disableBackground").remove();
    $("html").removeClass("noOverflow");
    pageDisabled = false;
}
