var audio_speech_3 = new Audio("./audio/Play.ht - Full Plant.wav");

export var sound;

let soundStatus = 1;

export var timeoutId;

function handleTimeout() {
	timeoutId = setTimeout(() => {
		audioPlayer();
		timeoutId = undefined;
	}, 10000);
}

export async function audioPlayer() {
	if (typeof soundStatus !== "undefined" && soundStatus === 1) {
		try {
			sound.currentTime = 0;
			await sound.play();
		} catch (e) {
			// do nothing
			// console.log("error", e);
		}
	}
}

export function setSoundStatus(status) {
	soundStatus = status;
}

export function playSound() {
	if (!timeoutId) {
		sound.play();
	}
}

export function updateSound(newSound, volume) {
	sound = newSound;
	sound.addEventListener("ended", function () {
		handleTimeout();
	});
	if (volume !== undefined && volume !== null && volume !== "") {
		sound.volume = volume;
	} else {
		sound.volume = 0.5;
	}
}
