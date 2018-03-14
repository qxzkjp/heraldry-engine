$(document).ready(function () {
	//toggle menu when button is pressed
    $("#menuButton").on("click", function () {
        animateSideMenu();
        //$("#sideMenu").fadeToggle();
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