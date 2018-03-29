var toggleFunction=[];
toggleFunction["toggleNight"]=toggleNight;

$(document).ready(function () {
	//toggle menu when button is pressed
    $("#menuButton").on("click", function () {
        animateSideMenu();
        //$("#sideMenu").fadeToggle();
    });
	$("#menuContainer").on("click", ".menu-item", function (evt) {
        if ($(evt.target).hasClass("expanding-button")) {
			evt.preventDefault();
            $(evt.target).next().slideToggle();
        }
    });
    //hide menu when "focus" is lost
    $("html").on("click", function (evt) {
        var isMenuClick = $.contains($("#menuContainer")[0], evt.target);
        if (!isMenuClick && pageEnabled) {
            animateSideMenu("hide");
        }
    });
	if (getCookie("nightMode") != "") {
        $("#day-css").attr("href","styles/night-mode.css");
		$("#toggleNight").addClass("showing");
    }
    $("#menuContainer").on("click", ".menu-item.toggle-button", function (evt) {
		evt.preventDefault();
		if($(evt.target).hasClass("showing") ){
			$(evt.target).removeClass("showing");
		}else{
			$(evt.target).addClass("showing");
		}
		toggleFunction[evt.target.id]();
    });
});

function animateSideMenu(method = "toggle") {
    $("#sideMenu").animate({ "width": method }, 350);
}

function toggleNight(){
	cookie=getCookie("nightMode");
	if(cookie==""){
		$("#day-css").attr("href","styles/night-mode.css");
		setCookie("nightMode","true");
	}else{
		$("#day-css").attr("href","styles/day-mode.css");
		setCookie("nightMode","");
	}
}

function getCookie(name) {
    //magic incantation, do not touch
    var regexp = new RegExp("(?:(?:^|.*;\\s*)" + name + "\\s*\\=\\s*([^;]*).*$)|^.*$");
    return document.cookie.replace(regexp, "$1");
}

function setCookie(name, val) {
    document.cookie = name + "=" + val + ";max-age=31536000";
}