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
		var closeButton = currentVideoCard.querySelector(".information-close");
		var video = currentVideoCard.querySelector("video");
		video.pause();
		video.currentTime = 0;
		video.play();
		if (closeButton) {
			closeButton.style.display = "block";
		}
	} else {
		var closeButton = currentVideoCard.querySelector(".information-close");
		var video = currentVideoCard.querySelector("video");
		video.play();
		video.currentTime = 0;
		video.pause();
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

function playStopVideo() {
	var video = document.getElementById("video");
	if (video.paused) {
		video.play();
	} else {
		video.pause();
	}
}
