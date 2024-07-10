const myCanvas = document.querySelector("#myCanvas");

import * as THREE from "three";
import { OrbitControls } from "three/addons/controls/OrbitControls.js";
import { GLTFLoader } from "three/addons/loaders/GLTFLoader.js";
// import {
// 	CSS2DRenderer,
// 	CSS2DObject,
// } from "three/addons/renderers/CSS2DRenderer.js";
import { CSS2DRenderer, CSS2DObject} from "./js/CSS2DRenderer.js";
import { DRACOLoader } from "three/addons/loaders/DRACOLoader.js";
import jsonData from "./data/data.json" with { type: "json" };
import { audioPlayer, timeoutId, sound, updateSound } from "./js/audio.js";

// ----------------------------------- SCENE BACKGROUND COLOR -----------------------------------
export const scene = new THREE.Scene();
scene.background = new THREE.Color(0xdbe9e9);

// ------------------------------------------- CAMERA -------------------------------------------
export const camera = new THREE.PerspectiveCamera(
	60,
	myCanvas.offsetWidth / myCanvas.offsetHeight
);
camera.position.set(6, 4, -4);
camera.layers.enableAll();

// ----------------------------------------- GRID HELPER ----------------------------------------
const size = 75;
const divisions = 30;
const colorCenterLine = 0xffffff;
const colorGrid = 0xffffff;

const grid = new THREE.GridHelper(size, divisions, colorCenterLine, colorGrid);
grid.position.y = -1;
grid.name = "grid";
scene.add(grid);

// ------------------------------------------ LIGHTNING -----------------------------------------
/*
	Light in 3D scene
	set(x,y,z)
		+x front
		+y up
		+z left
*/

// ---------------------------------- LIGHTNING CUSTOM: AMBIENT ---------------------------------
const ambientLight = new THREE.HemisphereLight(
	"white", // bright sky color
	"grey", // dim ground color
	0.5 // intensity
);
ambientLight.name = "ambientLight";
scene.add(ambientLight);

// -------------------------------- LIGHTNING CUSTOM: DIRECTIONAL -------------------------------
var dirLight = new THREE.DirectionalLight(0x404040, 20);
dirLight.name = "dirLight";
dirLight.position.set(100, 100, -10);
dirLight.castShadow = true;
scene.add(dirLight);
// --------------------------- LIGHTNING DEFAULT: CENTER BELOW CENTER ---------------------------
export const renderer = new THREE.WebGLRenderer({ canvas: myCanvas });
renderer.setClearColor(0xff0000, 1.0);
renderer.setPixelRatio(window.devicePixelRatio);
renderer.setSize(myCanvas.offsetWidth, myCanvas.offsetHeight);

// --------------------------------------- ORBIT CONTROLS ---------------------------------------
const labelRenderer = new CSS2DRenderer();
labelRenderer.setSize(window.innerWidth, window.innerHeight);
labelRenderer.domElement.style.position = "absolute";
labelRenderer.domElement.style.zIndex = 1;
labelRenderer.domElement.style.top = "0px";
document.body.appendChild(labelRenderer.domElement);

export const orbitControls = new OrbitControls(
	camera,
	labelRenderer.domElement
);

// --------------------------------------- 3D FILE LOADER ---------------------------------------
const loadingScreenBar = document.getElementById("loadingBar");
const loadingScreenContainer = document.querySelector(
	".loadingScreenContainer"
);
export const loadingManager = new THREE.LoadingManager();

loadingManager.onStart = function () {
	loadingScreenContainer.style.display = "flex";
};

loadingManager.onProgress = function (url, loaded, total) {
	loadingScreenBar.value = (loaded / total) * 100;
	// console.log("Loading file: " + url + ".\nLoaded: " + loaded + " of " + total);
};

loadingManager.onLoad = function () {
	loadingScreenBar.value = 0;
	loadingScreenContainer.style.display = "none";
	setTimeout(audioPlayer, 500);
};

export const loader = new GLTFLoader(loadingManager);
loader.name = "loader";

const currentPath = window.location.pathname;
let path;

const pdf_file = document.getElementById("pdf-file");
const video = document.getElementById("video");
const information_description = document.querySelector(
	".information-description-description"
);
const pdf_button = document.querySelector(".menu-pdf");
const video_button = document.querySelector(".menu-video");
const info_img = document.querySelector(".information-specification-img");
export let product_list_text;

if (currentPath.includes("crushing-plant")) {
	path = jsonData["Hokkaido Crushing Full Plant"].glb_file;
	setProductListText("Hokkaido Crushing Full Plant");
	updateInformation("Hokkaido Crushing Full Plant");
	resetAndUpdateSound("Hokkaido Crushing Full Plant");
} else if (currentPath.includes("recycling-plant")) {
	path = jsonData["Recycling Full Plant"].glb_file;
	setProductListText("Recycling Full Plant");
	updateInformation("Recycling Full Plant");
	resetAndUpdateSound("Recycling Full Plant");
} else {
	path = jsonData["MSD700-Blade"].glb_file;
	setProductListText("MSD700-Blade");
	updateInformation("MSD700-Blade");
	resetAndUpdateSound("MSD700-Blade");
}

const dracoLoader = new DRACOLoader();
dracoLoader.setDecoderPath(
	"https://www.gstatic.com/draco/versioned/decoders/1.5.6/"
);
dracoLoader.setDecoderConfig({ type: "js" });
loader.setDRACOLoader(dracoLoader);

loader.load(
	path,
	function (gltf) {
		let file3D = gltf.scene;
		file3D.name = "file3D";
		scene.add(file3D);
		// file3D.layers.enableAll();

		file3D.position.set(0, -0.95, 0);
	},
	undefined,
	function (error) {
		console.error(error);
	}
);

// ----------------------------------------- RENDER LOOP ----------------------------------------
renderer.setAnimationLoop(() => {
	animateLoop();
});

export function animateLoop(){
	orbitControls.update();
	labelRenderer.render(scene, camera);
	renderer.render(scene, camera);
}

orbitControls.addEventListener("change", render);
// renderer.setAnimationLoop(null);
 
function render(){
    labelRenderer.render(scene, camera);
    renderer.render(scene, camera);
}
 
// render();
// ---------------------------------------- RESIZE CANVAS ---------------------------------------
myCanvas.style.width = window.innerWidth + "px";
myCanvas.style.height = window.innerHeight + "px";
camera.aspect = window.innerWidth / window.innerHeight;
camera.updateProjectionMatrix();

window.addEventListener("resize", () => {
	myCanvas.style.width = window.innerWidth + "px";
	myCanvas.style.height = window.innerHeight + "px";
	camera.aspect = window.innerWidth / window.innerHeight;
	camera.updateProjectionMatrix();
	labelRenderer.setSize(window.innerWidth - 0.5, window.innerHeight - 0.5);
});

function updateInformation(file_name) {
	let x = jsonData[file_name].info.split("。");
	let x_joined = x.join("。<br><br>");
	information_description.innerHTML = x_joined;
	createAnnotation(
		jsonData[file_name].annotation_text,
		new THREE.Vector3(4, 7.25, 0),
		"A"
	);

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

	camera.position.set(
		jsonData[file_name].position.x,
		jsonData[file_name].position.y,
		jsonData[file_name].position.z
	);
}

export function createAnnotation(content, position, label) {
	const annotationDiv = document.createElement("div");
	annotationDiv.id = "annotationDiv";

	annotationDiv.textContent = content;
	annotationDiv.style.backgroundColor = "#74E7D4";
	annotationDiv.style.fontFamily = "Ubuntu";
	annotationDiv.style.borderRadius = "5px";
	annotationDiv.style.padding = "4px";

	const annotation = new CSS2DObject(annotationDiv);
	annotation.name = label;
	annotation.position.copy(position);
	annotation.center.set(0, 1, 0);
	scene.add(annotation);
}

export function removeAnnotation(label) {
	const annotation = scene.getObjectByName(label);
	if (annotation != null) {
		scene.remove(annotation);
	}
}

export function setProductListText(text) {
	product_list_text = text;
}

function resetAndUpdateSound(file_name) {
	clearTimeout(timeoutId);
	try {
		sound.pause();
		sound.currentTime = 0;
	} catch (e) {
		// do nothing
	}
	updateSound(new Audio(jsonData[file_name].audio_link));
}
