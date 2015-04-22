function handleUpdateClick() {
	if (confirm("Do you want to update?") == true) {
		var text = document.getElementById("text").value;
		var color = document.getElementById("color").value;

		appendText(text);
		updateColor(color);
	}
}

function toggle() {
	var e = document.getElementById("results");
	if (e.style.visibility == 'hidden')
		e.style.visibility = 'visible';
	else
		e.style.visibility = 'hidden';
}

function appendText(text) {
	var e = document.getElementById("results");
	e.innerHTML = e.innerHTML + text;
}

function updateColor(color) {
	var e = document.getElementById("results");
	e.style.backgroundColor = color;
}