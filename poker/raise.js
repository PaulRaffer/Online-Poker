function raise_money_action() {
    raise_radio_input = document.getElementById("raise");
    if (!raise_money_input.checked) {
        document.getElementById("raise").checked = true;
    }
    raise_radio_action();
}

function raise_radio_action() {
    const raise_money_input = document.getElementById("raise_money_input");
    if (!raise_money_input.value) {
        raise_money_input.value = raise_money_input.min;
        raise_money_input.select();
    }
}
