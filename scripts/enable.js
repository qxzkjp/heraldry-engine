var pageEnabled = true;

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