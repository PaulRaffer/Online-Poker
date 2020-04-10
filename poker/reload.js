function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}


if (typeof(EventSource) !== "undefined") {
	const evtSource = new EventSource("reload.php?game=" + getUrlVars()["game"]);
	evtSource.onmessage = (event) => {
		document.getElementById("players").innerHTML = event.data;
	}
} else {
	document.title = "BROWSER NOT SUPPORTED!!!";
}
