const pdfCards = document.querySelectorAll(".pdf-background");
const pdf_pop_up = document.querySelector(".container-full-screen-pdf");
const pdf_file = document.getElementById("pdf-file");
// const full_screen_pdf = document.querySelector(".full-screen-pdf.active");
let currentPdfCard = null;

pdfCards.forEach(function (pdfCard) {
	pdfCard.addEventListener("click", function () {
		const pdf_object = pdfCard.querySelector(".pdf-card");
		if (currentPdfCard !== pdf_object.getAttribute("data-pdf")) {
			currentPdfCard = pdf_object.getAttribute("data-pdf");
			pdf_file.setAttribute(
				"src",
				currentPdfCard + "#page=1&scrollbar=0&toolbar=0&view=FitH"
			);
		}
		pdf_pop_up.classList.add("active");
	});
});

// pdfCards.forEach(function (pdfCard) {
// 	pdfCard.addEventListener("click", function () {
// 		const pdf_object = pdfCard.querySelector(".pdf-card"); // Adjust selector as needed
// 		const newPdf = pdf_object.getAttribute("data-pdf");
// 		if (currentPdfCard !== newPdf) {
// 			currentPdfCard = newPdf;

// 			const pdfContainer = document.getElementById("pdf-pop-up-container");
// 			const pdf_file = document.getElementById("pdf-file");
// 			if (pdf_file) {
// 				pdfContainer.removeChild(pdf_file);
// 			}

// 			const newEmbed = document.createElement("embed");
// 			newEmbed.id = "pdf-file";
// 			newEmbed.type = "application/pdf";
// 			newEmbed.width = "100%";
// 			newEmbed.height = "100%";
// 			newEmbed.data =
// 				currentPdfCard + "#page=1&scrollbar=0&toolbar=0&view=FitH";
// 			pdfContainer.appendChild(newEmbed);
// 		}
// 		pdf_pop_up.classList.add("active");
// 	});
// });

pdf_pop_up.addEventListener("click", function (e) {
	if (!document.getElementById("pdf-pop-up-container").contains(e.target)) {
		if (pdf_pop_up.classList.contains("active")) {
			pdf_pop_up.classList.remove("active");
		}
	}
});
