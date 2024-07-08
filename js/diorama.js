import { scene, camera, orbitControls, loader } from "../script.js";
import * as THREE from "three";
import { GLTFLoader } from "three/addons/loaders/GLTFLoader.js";
import jsonData from "../data/data.json" with { type: "json" };
import {
	product_list_text,
	setProductListText,
	createAnnotation,
	removeAnnotation,
} from "../script.js";
import { updateSound, sound, timeoutId, setSoundStatus, playSound } from "./audio.js";

// ---------------------------------------------------------------------------------------
// ----------------------------------- Const, Var, Let -----------------------------------
// ---------------------------------------------------------------------------------------
// let product_list_text = "VSI Gyropactor";
// ----------------------------------- dark/light mode -----------------------------------
const toggle = document.querySelector(".toggle");

let getMode = localStorage.getItem("mode");

// -------------------------------------- lightning --------------------------------------
const menuLightning = document.querySelector(".menu-container-blue-lightning");
const lightning_expand = document.querySelector(
	".menu-container-blue-lightning-expand"
);

// -------------------------------- slider env brightness --------------------------------
const slider_env = document.getElementById("slider-env");
const maxValue_env = slider_env.getAttribute("max");
let value_env;
const sliderFill_env = document.getElementById("fill-env");

// --------------------------------- slider lamp position --------------------------------
const slider_lamp_pos = document.getElementById("slider-lamp-pos");
const maxValue_lamp_pos = slider_lamp_pos.getAttribute("max");
let value_lamp_pos;
const sliderFill_lamp_pos = document.getElementById("fill-lamp-pos");

// ------------------------------- slider lamp brightness --------------------------------
const slider_lamp = document.getElementById("slider-lamp");
const maxValue_lamp = slider_lamp.getAttribute("max");
let value_lamp;
const sliderFill_lamp = document.getElementById("fill-lamp");

// ------------------------------- slider volume --------------------------------
const slider_volume = document.getElementById("slider-sound");
const maxValue_volume = slider_volume.getAttribute("max");
let value_volume;
const sliderFill_volume = document.getElementById("fill-sound");

// -------------------------------------- catalogue --------------------------------------
const menuAlbum = document.querySelector(".menu-container-blue-album");
const catalogueContainer = document.getElementById("catalogue-container-2");
const catalogue_product_list = document.querySelectorAll(
	".catalogue-product-list-2"
);

const catalogue_close = document.querySelector(".catalogue-close");

// ------------------------------------- slider zoom -------------------------------------
const slider = document.getElementById("slider-zoom");
const maxValue = slider.getAttribute("max");
let value;
const sliderFill = document.getElementById("fill-zoom");

// ---------------------------------------- sound ----------------------------------------
const menuSound = document.querySelector(".menu-container-blue-sound");
const iconSoundOff = document.getElementById("sound-off");
const iconSoundOn = document.getElementById("sound-on");

const soundExpand = document.querySelector(".sound-expand");

let change_audio = "model_name_1";
// let soundStatus = 1;

// var audio_speech = new Audio("./audio/Play.ht - VSI Gyropactor.wav");
// var audio_speech_2 = new Audio(
// 	"./audio/Play.ht - VSI Gyropactor & Platform.wav"
// );

// Function to print "test"
// function printTest() {
// 	console.log("test");
// 	console.log("adio pause", sound.paused);
// 	console.log("audion ended", sound.ended);
// 	if (sound.paused) {
// 		sound.currentTime = 0;
// 		sound.play();
// 	}
// }

// var intervalId = setInterval(printTest, 10000); // 30000 milliseconds (30 seconds)

// const toggle_music = document.querySelector(".toggle-music");
const toggle_speech = document.querySelector(".toggle-speech");

// -------------------------------------- animation --------------------------------------
const menuAnimation = document.querySelector(".menu-container-blue-animation");
const iconAnimationOff = document.getElementById("animation-off");
const iconAnimationOn = document.getElementById("animation-on");

// ------------------------------------- information -------------------------------------
const menuInformation = document.querySelector(
	".menu-container-blue-information"
);
const informationContainer = document.getElementById("information-container");
const information_title = document.querySelector(
	".information-description-title"
);
const information_description = document.querySelector(
	".information-description-description"
);
// const information_link = document.querySelector(".information-link");
const pdf_file = document.getElementById("pdf-file");
const info_img = document.querySelector(".information-specification-img");

const info_close = document.querySelector(".information-close");

// ------------------------------------- video button ------------------------------------
const video_button = document.querySelector(".menu-video");
const video_pop_up = document.querySelector(".container-full-screen-video");
const video = document.getElementById("video");
const close_video_x = document.querySelector(".close-video");

// ---------------------------------------------------------------------------------------
// ------------------------------------- PROGRAM CODE ------------------------------------
// ---------------------------------------------------------------------------------------

// ----------------------------------- dark/light mode -----------------------------------
if (!getMode || (getMode && getMode === "dark-theme")) {
	document.body.classList.add("dark-theme");
	toggle.classList.add("active");

	scene.background = new THREE.Color(0x1d2538);

	scene.remove(scene.getObjectByName("grid"));

	const grid = new THREE.GridHelper(75, 30, 0x475b74, 0x475b74);
	grid.position.y = -1;
	grid.name = "grid";
	scene.add(grid);

	localStorage.setItem("mode", "dark-theme");
}

toggle.addEventListener("click", () => toggle.classList.toggle("active"));

toggle.addEventListener("click", () => {
	document.body.classList.toggle("dark-theme");

	if (document.body.classList.contains("dark-theme")) {
		scene.background = new THREE.Color(0x1d2538);

		scene.remove(scene.getObjectByName("grid"));

		const grid = new THREE.GridHelper(75, 30, 0x475b74, 0x475b74);
		grid.position.y = -1;
		grid.name = "grid";
		scene.add(grid);

		localStorage.setItem("mode", "dark-theme");
	} else {
		scene.background = new THREE.Color(0xdbe9e9);

		scene.remove(scene.getObjectByName("grid"));
		const grid = new THREE.GridHelper(75, 30, 0xffffff, 0xffffff);
		grid.position.y = -1;
		grid.name = "grid";
		scene.add(grid);

		localStorage.setItem("mode", "light");
	}
});

// -------------------------------------- lightning --------------------------------------
menuLightning.addEventListener("click", () => {
	menuLightning.classList.toggle("active");

	if (menuLightning.classList.contains("active")) {
		lightning_expand.style.display = "block";
	} else {
		lightning_expand.style.display = "none";
	}
});

// -------------------------------- slider env brightness --------------------------------
slider_env.value = 0.5;
updateSliderEnv();
updateEnvBrightness();
slider_env.addEventListener("input", () => {
	updateSliderEnv();
	updateEnvBrightness();
});

// --------------------------------- slider lamp position --------------------------------
slider_lamp_pos.value = 210;
updateSliderLampPos();
updateLampPos();
slider_lamp_pos.addEventListener("input", () => {
	updateSliderLampPos();
	updateLampPos();
});

// ------------------------------- slider lamp brightness --------------------------------
slider_lamp.value = 20;
updateSliderLamp();
updateLamp();
slider_lamp.addEventListener("input", () => {
	updateSliderLamp();
	updateLamp();
});

// ------------------------------- slider volume --------------------------------
slider_volume.value = 0.5;
updateSliderVolume();
updateVolume();
slider_volume.addEventListener("input", () => {
	updateSliderVolume();
	updateVolume();
});

// -------------------------------------- catalogue --------------------------------------
menuAlbum.addEventListener("click", () => {
	menuAlbum.classList.toggle("active");

	if (menuAlbum.classList.contains("active")) {
		catalogueContainer.style.display = "flex";
	} else {
		catalogueContainer.style.display = "none";
	}
});

catalogue_close.addEventListener("click", () => {
	menuAlbum.classList.remove("active");
	catalogueContainer.style.display = "none";
});

loadCatalogue(catalogue_product_list);

// ------------------------------------- slider zoom -------------------------------------
updateSlider();
updateZoomCamera();
slider.addEventListener("input", () => {
	updateSlider();
	updateZoomCamera();
});

// ---------------------------------------- sound ----------------------------------------
menuSound.addEventListener("click", () => {
	menuSound.classList.toggle("active");

	if (menuSound.classList.contains("active")) {
		// iconSoundOff.style.display = "none";
		// iconSoundOn.style.display = "block";
		soundExpand.style.display = "flex";
	} else {
		// iconSoundOff.style.display = "block";
		// iconSoundOn.style.display = "none";
		soundExpand.style.display = "none";
	}
});

// toggle_speech.addEventListener("click", () => {
// 	toggle_speech.classList.toggle("active");

// 	// if (change_audio === "model_name_1") {
// 	// 	sound = audio_speech;
// 	// } else if (change_audio === "model_name_2") {
// 	// 	sound = audio_speech_2;
// 	// } else if (change_audio === "model_name_3") {
// 	// 	sound = audio_speech_3;
// 	// }

// 	if (toggle_speech.classList.contains("active")) {
// 		soundStatus = 1;
// 		audioPlayer();
// 	} else {
// 		soundStatus = 0;
// 		sound.pause();
// 		sound.currentTime = 0;
// 	}
// });

// -------------------------------------- animation --------------------------------------
menuAnimation.addEventListener("click", () => {
	menuAnimation.classList.toggle("active");

	if (menuAnimation.classList.contains("active")) {
		iconAnimationOff.style.display = "none";
		iconAnimationOn.style.display = "block";
		orbitControls.autoRotate = true;
	} else {
		iconAnimationOff.style.display = "block";
		iconAnimationOn.style.display = "none";
		orbitControls.autoRotate = false;
	}
});

// ------------------------------------- information -------------------------------------
menuInformation.addEventListener("click", () => {
	menuInformation.classList.toggle("active");

	if (menuInformation.classList.contains("active")) {
		informationContainer.style.display = "flex";
	} else {
		informationContainer.style.display = "none";
	}
});

info_close.addEventListener("click", () => {
	menuInformation.classList.remove("active");
	informationContainer.style.display = "none";
});

hideInformation(true);

// ------------------------------------- video button ------------------------------------
video_button.addEventListener("click", () => {
	video_pop_up.classList.toggle("active");
	setSoundStatus(0);
	sound.pause();
});

close_video_x.addEventListener("click", () => {
	video_pop_up.classList.remove("active");
	video.pause();
	video.currentTime = 0;
	setSoundStatus(1);
	playSound();
});

video_pop_up.addEventListener("click", function (e) {
	if (
		!document.getElementById("pdf-pop-up-container-video").contains(e.target)
	) {
		if (video_pop_up.classList.contains("active")) {
			video_pop_up.classList.remove("active");
			video.pause();
			video.currentTime = 0;
			setSoundStatus(1);
			playSound();
		}
	}
});

// ---------------------------------------------------------------------------------------
// ---------------------------------- FUNCTION HELPER ------------------------------------
// ---------------------------------------------------------------------------------------

// -------------------------------- slider env brightness --------------------------------
function updateSliderEnv() {
	value_env = (slider_env.value / maxValue_env) * 100 + "%";
	sliderFill_env.style.width = value_env;
}

function updateEnvBrightness() {
	let ambient = scene.getObjectByName("ambientLight");
	ambient.intensity = slider_env.value;
}

// --------------------------------- slider lamp position --------------------------------
function updateSliderLampPos() {
	value_lamp_pos = (slider_lamp_pos.value / maxValue_lamp_pos) * 100 + "%";
	sliderFill_lamp_pos.style.width = value_lamp_pos;
}

function updateLampPos() {
	let lamp = scene.getObjectByName("dirLight");
	lamp.position.set(100, 100, -(slider_lamp_pos.value - 200));
}

// ------------------------------- slider lamp brightness --------------------------------
function updateSliderLamp() {
	value_lamp = (slider_lamp.value / maxValue_lamp) * 100 + "%";
	sliderFill_lamp.style.width = value_lamp;
}

function updateLamp() {
	let lamp = scene.getObjectByName("dirLight");
	lamp.intensity = slider_lamp.value;
}

// ------------------------------- slider volume --------------------------------
function updateSliderVolume() {
	value_volume = (slider_volume.value / maxValue_volume) * 100 + "%";
	sliderFill_volume.style.width = value_volume;
}

function updateVolume() {
	// let sound = scene.getObjectByName("dirLight");
	// sound.intensity = slider_lamp.value;
	sound.volume = slider_volume.value;
}

// -------------------------------------- catalogue --------------------------------------

// Inside the loadCatalogue function
function loadCatalogue(catalogue_product_list) {
	catalogue_product_list.forEach(function (product_list) {
		product_list.addEventListener("click", () => {
			if (product_list.id != change_audio) {
				change_audio = product_list.id;
				sound.pause();
				sound.currentTime = 0;
				// toggle_speech.classList.contains("active") ? audioPlayer() : "";
			}

			resetCatalogueSelect();

			// product_list.classList.toggle("active");
			product_list.classList.add("active");

			setProductListText(
				product_list.querySelector(".catalogue-product-list-text-2").innerText
			);
			// product_list_text = product_list.querySelector(
			// 	".catalogue-product-list-text-2"
			// ).innerText;

			let file3D = scene.getObjectByName("file3D");

			// Reset the model and annotations for the current 3D model
			// resetModelAndAnnotations(file3D);

			updateFile3D(product_list_text);
			if (product_list_text === "Overview") {
				// updateInformation("Hokkaido Crushing Full Plant");
				// console.log("Overview");
				updateAnnotation(convertOverviewToName("Overview"));
				hideInformation(true);
				resetAndUpdateSound(convertOverviewToName("Overview"));
			} else {
				hideInformation(false);
				updateInformation(product_list_text);
				resetAndUpdateSound(product_list_text);
			}
		});
	});
}

function resetAndUpdateSound(file_name) {
	clearTimeout(timeoutId);
	sound.pause();
	sound.currentTime = 0;
	updateSound(new Audio(jsonData[file_name].audio_link), slider_volume.value);
}

function hideInformation(status) {
	if (status) {
		menuInformation.classList.remove("active");
		menuInformation.style.display = "none";
		informationContainer.style.display = "none";
	} else {
		menuInformation.classList.add("active");
		menuInformation.style.display = "flex";
		informationContainer.style.display = "flex";
	}
}

function resetCatalogueSelect() {
	catalogue_product_list.forEach(function (product_list) {
		product_list.classList.remove("active");
	});
}

function updateAnnotation(file_name) {
	removeAnnotation("A");
	createAnnotation(
		jsonData[file_name].annotation_text,
		new THREE.Vector3(
			jsonData[file_name].annotation.x,
			jsonData[file_name].annotation.y,
			jsonData[file_name].annotation.z
		),
		"A"
	);
}

function updateInformation(file_name) {
	information_title.innerHTML = file_name;

	let x = jsonData[file_name].info.split("。");
	let x_joined = x.join("。<br><br>");
	information_description.innerHTML = x_joined;

	updateAnnotation(file_name);

	// information_link.href = jsonData[file_name].web_link;
	// information_link.innerHTML = file_name + " | Nakayama Iron Works (ncjpn.com)";
	if (jsonData[file_name].hasOwnProperty("pdf_link")) {
		pdf_button.style.display = "flex";
		pdf_file.setAttribute(
			"src",
			jsonData[file_name].pdf_link + "#scrollbar=0&toolbar=0&view=FitH"
		);
	} else {
		pdf_button.style.display = "none";
	}

	if (jsonData[file_name].hasOwnProperty("video_link")) {
		video_button.style.display = "flex";
		video.setAttribute("src", jsonData[file_name].video_link);
	} else {
		video_button.style.display = "none";
	}

	if (jsonData[file_name].hasOwnProperty("info_img")) {
		info_img.style.display = "block";
		info_img.src = jsonData[file_name].info_img;
	} else {
		info_img.style.display = "none";
	}
}

function updateFile3D(file_name) {
	try {
		let file3D = scene.getObjectByName("file3D");
		file3D.name = "file3D";

		scene.remove(file3D);
		let newFile3D;
		let file_name2 = convertOverviewToName(file_name);
		newFile3D = jsonData[file_name2].glb_file;

		loader.load(
			newFile3D,
			function (gltf) {
				file3D = gltf.scene;
				file3D.name = "file3D";
				scene.add(file3D);
				file3D.position.set(0, -0.95, 0);

				camera.position.set(
					jsonData[file_name2].position.x,
					jsonData[file_name2].position.y,
					jsonData[file_name2].position.z
				);
			},
			undefined,
			function (error) {
				console.error(error);
			}
		);
	} catch (e) {
		// do nothing
	}
}

// ------------------------------------- slider zoom -------------------------------------
function updateZoomCamera() {
	camera.zoom = slider.value;
	camera.updateProjectionMatrix();
}

function updateSlider() {
	value = (slider.value / maxValue) * 100 + "%";
	sliderFill.style.width = value;
}

// pdf button
const pdf_button = document.querySelector(".menu-pdf");
const pdf_pop_up = document.querySelector(".container-full-screen-pdf");

pdf_button.addEventListener("click", () => {
	const annotationDivs = document.querySelectorAll("#annotationDiv");

	if (annotationDivs) {
		annotationDivs.forEach((div) => {
			div.style.display = "none";
		});
	}
	pdf_pop_up.classList.toggle("active");
});

pdf_pop_up.addEventListener("click", function (e) {
	if (!document.getElementById("pdf-pop-up-container").contains(e.target)) {
		if (pdf_pop_up.classList.contains("active")) {
			pdf_pop_up.classList.remove("active");
		}
	}
});

function convertOverviewToName(file_name) {
	if (file_name === "Overview") {
		if (window.location.pathname.includes("crushing-plant")) {
			return "Hokkaido Crushing Full Plant";
		} else {
			return "Recycling Full Plant";
		}
	} else {
		return file_name;
	}
}

// ------------------------------------- redirect after 5 minute no interaction -------------------------------------
const back_button = document.querySelector(".menu-container-back-button");
function redirect() {
	back_button.click();
}

let timer = setTimeout(redirect, 60000 * 5);

document.addEventListener("click", function (event) {
	clearTimeout(timer);
	timer = setTimeout(redirect, 60000 * 5);
});
