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
        const data = JSON.parse(event.data);
        document.getElementById("players").innerHTML = data.game_players;
        document.getElementById("check_call_money").innerHTML = data.check_call_money + '$';
        const raise_money_input = document.getElementById("raise_money_input");
        raise_money_input.min = data.min_raise_money;
        raise_money_input.placeholder = '≥' + data.min_raise_money;
        raise_money_input.max = data.you_money;
	}
} else {
	document.title = "BROWSER NOT SUPPORTED!!!";
}
