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
});

function animateSideMenu(method = "toggle") {
    $("#sideMenu").animate({ "width": method }, 350);
}