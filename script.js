const myCanvas = document.querySelector("#myCanvas");

import * as THREE from "three";
import { OrbitControls } from "three/addons/controls/OrbitControls.js";
import { GLTFLoader } from "three/addons/loaders/GLTFLoader.js";
import { CSS2DRenderer } from "three/addons/renderers/CSS2DRenderer.js";
import { DRACOLoader } from "three/addons/loaders/DRACOLoader.js";
import jsonData from "./data/data.json" assert { type: "json" };

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
const size = 150;
const divisions = 150;
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
const renderer = new THREE.WebGLRenderer({ canvas: myCanvas });
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
const loadingManager = new THREE.LoadingManager();

loadingManager.onStart = function () {
	loadingScreenContainer.style.display = "flex";
};

loadingManager.onProgress = function (url, loaded, total) {
	loadingScreenBar.value = (loaded / total) * 100;
};

loadingManager.onLoad = function () {
	loadingScreenBar.value = 0;
	loadingScreenContainer.style.display = "none";
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

if (currentPath.includes("crushing-plant")) {
	path = jsonData["Hokkaido Crushing Full Plant"].glb_file;
	camera.position.set(
		jsonData["Hokkaido Crushing Full Plant"].position.x,
		jsonData["Hokkaido Crushing Full Plant"].position.y,
		jsonData["Hokkaido Crushing Full Plant"].position.z
	);
	updateInformation("Hokkaido Crushing Full Plant");
} else if (currentPath.includes("recycling-plant")) {
	path = jsonData["Recycling Full Plant"].glb_file;
	camera.position.set(
		jsonData["Recycling Full Plant"].position.x,
		jsonData["Recycling Full Plant"].position.y,
		jsonData["Recycling Full Plant"].position.z
	);
	updateInformation("Recycling Full Plant");
} else {
	path = jsonData["MSD700-Blade"].glb_file;
	camera.position.set(
		jsonData["MSD700-Blade"].position.x,
		jsonData["MSD700-Blade"].position.y,
		jsonData["MSD700-Blade"].position.z
	);
	updateInformation("MSD700-Blade");
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
		file3D.layers.enableAll();

		file3D.position.set(0, -0.95, 0);
	},
	undefined,
	function (error) {
		console.error(error);
	}
);

// ----------------------------------------- RENDER LOOP ----------------------------------------
renderer.setAnimationLoop(() => {
	orbitControls.update();
	labelRenderer.render(scene, camera);
	renderer.render(scene, camera);
});

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
}
