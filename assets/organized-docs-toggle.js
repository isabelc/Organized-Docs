(function() {
	var titles = document.querySelectorAll(".docs-sub-heading");
	var i = titles.length;
	while (i--) {
		titles[i].setAttribute("style", "cursor:pointer");
		titles[i].innerText = '\u25be ' + titles[i].innerText;// Add Down arrow
	}
	var uls = document.querySelectorAll(".docs-sub-heading + ul");
	var i = uls.length;
	while (i--) {
		uls[i].className = "toggle-ul";
	}
})();

function toggleDocs(e){
	if(e.target&&("docs-sub-heading"==e.target.className||"widget-title docs-sub-heading"==e.target.className)){
	
		var t=e.target.nextElementSibling;
		if ("none" == t.style.display) {
			t.style.display = "block";
			// Up arrow
			e.target.innerText = e.target.innerText.replace('\u25be', '\u25b4');

		} else {
			t.style.display = "none";
			// Down arrow
			e.target.innerText = e.target.innerText.replace('\u25b4', '\u25be');
		}
	}
}

document.addEventListener("click",toggleDocs,!0);
