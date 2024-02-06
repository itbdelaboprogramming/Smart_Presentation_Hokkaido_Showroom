var audio_speech_3 = new Audio("./audio/Play.ht - Full Plant.wav");

export var sound;

let soundStatus = 1;

export var timeoutId;

function handleTimeout() {
	timeoutId = setTimeout(() => {
		audioPlayer();
	}, 10000);
}

export async function audioPlayer() {
	// if (change_audio === "model_name_1") {
	// 	sound = audio_speech;
	// } else if (change_audio === "model_name_2") {
	// 	sound = audio_speech_2;
	// } else if (change_audio === "model_name_3") {
	// 	sound = audio_speech_3;
	// }
	if (typeof soundStatus !== "undefined" && soundStatus === 1) {
		sound.addEventListener("ended", function () {
			// Delay the next call to audioPlayer by 30000 milliseconds
			handleTimeout();
		});
	}
	try {
		sound.currentTime = 0;
		await sound.play();
	} catch (e) {
		// do nothing
		// console.log("error", e);
	}
}
// audioPlayer();

export function updateSound(newSound) {
	sound = newSound;
}
