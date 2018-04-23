//https://stackoverflow.com/a/133997/1543262
function post(path, params, method) {
	method = method || "post"; // Set method to post by default if not specified.

	// The rest of this code assumes you are not using a library.
	// It can be made less wordy if you use one.
	var form = document.createElement("form");
	form.setAttribute("method", method);
	form.setAttribute("action", path);

	for(var key in params) {
		if(params.hasOwnProperty(key)) {
			var hiddenField = document.createElement("input");
			hiddenField.setAttribute("type", "hidden");
			hiddenField.setAttribute("name", key);
			hiddenField.setAttribute("value", params[key]);

			form.appendChild(hiddenField);
		}
	}

	document.body.appendChild(form);
	form.submit();
}

function clickPost(path,params,method){
	post(path, params, method);
	return false;
}

$(document).ready(function (){
	$('html').on('click', 'a[data-post]', function(evt){
		evt.preventDefault();
		if(evt.target.dataset.post != "")
			var params = JSON.parse(evt.target.dataset.post);
		else
			var params = {};
		params.CSRF = window.CSRF;
		post(evt.target.href,params);
	});
});
