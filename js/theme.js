var checkbox = document.querySelector('input[name=theme]');
var doltheme = window.localStorage.getItem('theme');
var path = window.location.pathname;
var page = path.split("/").pop();

$(document).ready(function checksettheme() {
    if (doltheme == "dark") {
			document.documentElement.setAttribute('data-theme', 'dark');
		if (page == "login") {
			$('input[type="checkbox"]').attr("checked", "checked");
		}
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
    }

})

//(document.documentElement.getAttribute('data-theme') === 'light')

if(page == "login")
{
	checkbox.addEventListener('change', function () {
    	if (this.checked) {
        	trans()
        	document.documentElement.setAttribute('data-theme', 'dark')
			window.localStorage.setItem('theme', 'dark');
		} else {
        	trans()
        	document.documentElement.setAttribute('data-theme', 'light')
        	window.localStorage.setItem('theme', 'light');
    	}
	})

	let trans = () => {
		document.documentElement.classList.add('transition');
		window.setTimeout(() =>{
			document.documentElement.classList.remove('transition')
		}, 300)
	}
}
