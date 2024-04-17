var videoCards = document.querySelectorAll(".video-card");

var currentVideoCard = null; 

videoCards.forEach(function (videoCard) {
	videoCard.addEventListener("click", function () {
		currentVideoCard = this;
		if (this !== undefined) {
			this.style.background = "black";
			this.style.border = "none";
		}
		this.requestFullscreen();
	});
});


document.addEventListener("fullscreenchange", function () {
	if (document.fullscreenElement) {
		var closeButton = document.querySelector(".information-close");
		if (closeButton) {
			closeButton.style.display = "block";
		}
	} else {
		var closeButton = document.querySelector(".information-close");
		if (closeButton) {
			closeButton.style.display = "none";
			if (currentVideoCard !== null) {
				currentVideoCard.style.background = "";
				currentVideoCard.style.border = "";
			}
		}
	}
});

var closeButtons = document.querySelectorAll(".information-close");

closeButtons.forEach(function (closeButton) {
	closeButton.addEventListener("click", function () {
		document.exitFullscreen();
	});
});
