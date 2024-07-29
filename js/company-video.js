var videoCards = document.querySelectorAll(".video-card");
var currentVideoCard = null;

videoCards.forEach(function (videoCard) {
	videoCard.addEventListener("click", function () {
		currentVideoCard = this;
		if (currentVideoCard) {
			currentVideoCard.style.background = "black";
			currentVideoCard.style.border = "none";
		}
		this.requestFullscreen().then(() => {
			var video = currentVideoCard.querySelector("video");
			video.controls = true; // Enable controls in fullscreen mode
			video.play();
		});
	});
});

document.addEventListener("fullscreenchange", function () {
	if (document.fullscreenElement) {
		var closeButton = currentVideoCard.querySelector(".information-close");
		var video = currentVideoCard.querySelector("video");
		video.controls = true;
		video.play();

		// Disable clicking on the video to toggle play/pause
		video.addEventListener("click", disableVideoClick);

		if (closeButton) {
			closeButton.style.display = "block";
		}
	} else {
		var closeButton = currentVideoCard.querySelector(".information-close");
		var video = currentVideoCard.querySelector("video");
		video.controls = false; // Disable controls when exiting fullscreen
		video.pause();

		// Remove event listener when exiting fullscreen
		video.removeEventListener("click", disableVideoClick);

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

function disableVideoClick(event) {
	event.stopPropagation();
}

// redirect to home after 5 minutes of inactivity
const back_button = document.querySelector(".menu-container-back-button");
function redirect() {
	back_button.click();
}

let timer = setTimeout(redirect, 60000 * 5);
document.addEventListener("click", function (event) {
	clearTimeout(timer);
	timer = setTimeout(redirect, 60000 * 5);
});
