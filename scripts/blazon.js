//ui.js: UI code and onLoad triggers

var debugEnabled = false;
var inputEnabled = false;
var initialBlazon = "Azure, a bend Or";
toggleFunction["toggleSyntax"]=toggleSyntax;
var colourCss;
var lineArtCss;
$(document).ready(function () {
    var debugCookie = getCookie("debug");
    if (debugCookie != "") {
        enableDebugging();
        $("#shieldCover").hide();
    }
    //set up shield
    path = document.getElementById("shield");
    box = path.getBBox();
    centre = new Point(box.x + (box.width / 2), box.y + (box.height / 2));
    var tempString = getParameterByName("blazon");
    if (tempString !== null) {
        initialBlazon = tempString;
    }
    //AJAX request to get the movable charges and draw initial arms
    var xhr = new XMLHttpRequest;
    xhr.open('get', 'SVG/movables.svg', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState != 4) return;//4 means completed
        //load SVG document from request
        SVG_MOVABLES = xhr.responseXML.documentElement;
        SVG_MOVABLES = document.importNode(SVG_MOVABLES, true);
        SVG = document.getElementById("escutcheonContainer");
        jqSVG = $("#escutcheonContainer");
        //once charges are set up, draw the initial arms
        $('#blazonText')[0].value = initialBlazon;
        setBlazon($('#blazonText')[0].value);
        //update the history state to correctly represent the initial state of the page
        var stateObj = { blazon: initialBlazon };
        history.replaceState(stateObj, "", window.location.href);
        //and finally, now that everything is ready, enable input
        enableInput();
        $("#blazonText")[0].focus();
    };
    xhr.send();

    if (getSyntaxCookie() != "") {
        $("#syntax").show();
        $("#toggleSyntax").addClass("showing");
    }
	
    $("#menuContainer").on("click", ".demoBlazon", function (evt) {
        if (inputEnabled) {
            var newTextElem = $(this).find(".blazonText")[0];
            if (newTextElem === undefined) {
                newUserBlazon(evt.target.innerHTML);
            } else {
                newUserBlazon(newTextElem.innerHTML);
            }
        }
    });
    //draw blazon when enter is pressed
    $("#blazonText").keypress(function (e) {
        if (e.which == 13) {
            e.preventDefault();
            e.stopPropagation();
            drawUserBlazon();
            //set focus back on the textbox, for UX
            $("#blazonText")[0].focus();
        }
    });
	$.ajax({
    url: "/styles/heraldry-bw.css",
    dataType: "text",
    success: function(cssText) {
        lineArtCss=cssText;
    }});
	$.ajax({
    url: "/styles/heraldry-not-shit.css",
    dataType: "text",
    success: function(cssText) {
        colourCss=cssText;
    }});
});

function enableDebugging() {
    debugEnabled = true;
    updateDebugDisplay = updateDebugDisplayBackup;
    clearDebugDisplay = clearDebugDisplayBackup;
    setCookie("debug", "true");
}

function disableDebugging() {
    debugEnabled = false;
    updateDebugDisplay = function () { };
    clearDebugDisplay = function () { };
    setCookie("debug", "");
}


//With thanks to Stack Overflow
//https://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript/901144#901144
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function drawUserBlazon(updateHistory = true) {
    disableInput();
    var str = document.getElementById('blazonText').value;
    if (str != blazon) {
        var couldParse = setBlazon(str);
        if (updateHistory && couldParse) {
            var stateObj = { blazon: str };
            //replace spaces with plus signs, for URL readability
            str = encodeURIComponent(str);
            str = str.replace(/%20/g, "+");
            //push new URL to history, with blazon in the query string
            var idx = window.location.href.indexOf("?");
            var href;
            if (idx < 0) {
                href = window.location.href;
            } else {
                href = window.location.href.slice(0, idx);
            }
            history.pushState(stateObj, "", href + "?blazon=" + str);
        }
    }
    enableInput();
    //take away form focus from textbox, for UX reasons
    document.activeElement.blur();
}

function newUserBlazon(str, updateHistory) {
    document.getElementById('blazonText').value = str;
    drawUserBlazon(updateHistory);
}

window.onpopstate = function (event) {
    var blazonHadFocus = false;
    if (document.activeElement.id === "blazonText") {
        blazonHadFocus = true;
    }
    //we pass "false" to newUserBlazon to stop it pushing a new history state
    if (event.state !== null) {
        newUserBlazon(event.state.blazon, false);
    }
    if (blazonHadFocus) {
        $("#blazonText")[0].focus();
    }
}

function enableInput() {
    $("#blazonButton").attr("disabled", false);
    $("#blazonText").attr("disabled", false);
    inputEnabled = true;
}

function disableInput() {
    $("#blazonButton").attr("disabled", true);
    $("#blazonText").attr("disabled", true);
    inputEnabled = false;
}

function getSyntaxCookie() {
    //magic incantation, do not touch
    return document.cookie.replace(/(?:(?:^|.*;\s*)showSyntax\s*\=\s*([^;]*).*$)|^.*$/, "$1");
}

function setSyntaxCookie(val) {
    document.cookie = "showSyntax=" + val + ";max-age=31536000";
}

function isNightMode(){
	return nightMode;
}

function toggleSyntax(){
	$("#syntax").slideToggle();
	if (getSyntaxCookie() == "") {
		setSyntaxCookie("show");
	} else {
		setSyntaxCookie("");
	}
	return true;
}