function showContainer(argument, value) { Jelo.Anim.ate({ me: $("#" + argument), css: "height", to: value, easing: "smooth" }); }

function hideContainer() {
	var inurcounter = 0;
	while(inurcounter < arguments.length) {
		// alert(arguments[inurcounter]);
		Jelo.Anim.ate({ me: $("#" + arguments[inurcounter]), css: "height", to: 0, easing: "smooth" });
		inurcounter++;
	}
}