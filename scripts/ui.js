//ui.js: UI code and onLoad triggers

var pageEnabled = true;
var debugEnabled = false;
var inputEnabled = false;

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
    //AJAX request to get the movable charges and draw initial arms
    var xhr = new XMLHttpRequest;
    xhr.open('get', 'movables.svg', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState != 4) return;//4 means completed
        //load SVG document from request
        SVG_MOVABLES = xhr.responseXML.documentElement;
        SVG_MOVABLES = document.importNode(SVG_MOVABLES, true);
        SVG = document.getElementById("escutcheonContainer");
        jqSVG = $("#escutcheonContainer");
        //once charges are set up, draw the initial arms
        $('#blazonText')[0].value = "Azure, a bend Or";
        setBlazon($('#blazonText')[0].value);
        //and finally, now that everything is ready, enable input
        enableInput();
        $("#blazonText")[0].focus();
    };
    xhr.send();

    if (getSyntaxCookie() != "") {
        $("#syntax").show();
        $("#toggleSyntax").addClass("showing");
    }
    $("#menuContainer").on("click", ".menu-item", function (evt) {
        if (evt.target.id === "toggleSyntax") {
            $("#syntax").slideToggle();
            if (getSyntaxCookie() == "") {
                setSyntaxCookie("show");
                $("#toggleSyntax").addClass("showing");
                return false;
            } else {
                setSyntaxCookie("");
                $("#toggleSyntax").removeClass("showing");
                return false;
            }
        } else if (evt.target.id === "exampleBlazons") {
            $("#exampleContainer").slideToggle();
        }
    });
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
    $("#menuButton").on("click", function () {
        animateSideMenu();
        //$("#sideMenu").fadeToggle();
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
    //hide menu when "focus" is lost
    $("html").on("click", function (evt) {
        var isMenuClick = $.contains($("#menuContainer")[0], evt.target);
        if (!isMenuClick && pageEnabled) {
            animateSideMenu("hide");
        }
    });
});

function animateSideMenu(method = "toggle") {
    $("#sideMenu").animate({ "width": method }, 350);
}

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

function disablePage() {
    $("body").append('<div id="disableBackground"></div>')
    $("html").addClass("noOverflow");
    pageEnabled = false;
}

function enablePage() {
    $("#disableBackground").remove();
    $("html").removeClass("noOverflow");
    pageEnabled = true;
}
