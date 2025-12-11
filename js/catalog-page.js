const pdfCards = document.querySelectorAll(".pdf-background");
const pdf_pop_up = document.querySelector(".container-full-screen-pdf");
const pdf_file = document.getElementById("pdf-file");
let currentPdfCard = null;

pdfCards.forEach(function (pdfCard) {
    pdfCard.addEventListener("click", function () {
        const pdf_object = pdfCard.querySelector(".pdf-card");
        const selectedPdf = pdf_object.getAttribute("data-pdf");

        if (currentPdfCard !== selectedPdf) {
            currentPdfCard = selectedPdf;
            pdf_file.setAttribute(
                "src",
                currentPdfCard + "#page=1&scrollbar=0&toolbar=0&view=FitH"
            );
        }
        pdf_pop_up.classList.add("active");
    });
});

pdf_pop_up.addEventListener("click", function (e) {
	if (!document.getElementById("pdf-pop-up-container").contains(e.target)) {
		if (pdf_pop_up.classList.contains("active")) {
			pdf_pop_up.classList.remove("active");
		}
	}
});

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


// CRUD CATALOG ADMIN MODAL

document.addEventListener("DOMContentLoaded", () => {
    
    const modal = document.getElementById("admin-catalog-modal");
    if (!modal) return; 

    const btnAdd = document.getElementById("admin-add-catalog-btn");
    const btnsEdit = document.querySelectorAll(".admin-button-edit");
    const btnsDelete = document.querySelectorAll(".admin-button-delete");
    const btnClose = document.getElementById("admin-modal-close-btn");
    const btnCancel = document.getElementById("admin-modal-cancel-btn");
    const form = document.getElementById("admin-catalog-form");
    const modalTitle = document.getElementById("admin-modal-title");
    const modalOverlay = document.querySelector(".admin-modal-overlay");
    const catalogIdInput = document.getElementById("catalog-id");
    const catalogTitleInput = document.getElementById("catalog-title");
    const catalogOrderInput = document.getElementById("catalog-order");
    const catalogPdfInput = document.getElementById("catalog-pdf");
    const catalogImageInput = document.getElementById("catalog-image");


    const resetForm = () => {
        form.reset();
        catalogIdInput.value = ""; 
    };

    const openModal = (mode, data = null) => {
        resetForm();
        if (mode === 'edit' && data) {
            modalTitle.textContent = "Edit Catalog";
            
            catalogIdInput.value = data.id;
            catalogTitleInput.value = data.title;
            catalogOrderInput.value = data.sort_order;
            catalogPdfInput.removeAttribute('required');
            catalogImageInput.removeAttribute('required');

            // Update small text below file inputs to show current filename
            const pdfSmall = catalogPdfInput.nextElementSibling;
            if (pdfSmall && pdfSmall.tagName === 'SMALL' && data.pdf_file) {
                pdfSmall.innerHTML = `Current file: <span style="color: #74e7d4; font-weight: 600;">${data.pdf_file}</span>`;
            }
            
            const imgSmall = catalogImageInput.nextElementSibling;
            if (imgSmall && imgSmall.tagName === 'SMALL' && data.preview_image) {
                imgSmall.innerHTML = `Current file: <span style="color: #74e7d4; font-weight: 600;">${data.preview_image}</span>`;
            }

        } else {
            modalTitle.textContent = "Add New Catalog";
            catalogPdfInput.setAttribute('required', 'required');
            catalogImageInput.setAttribute('required', 'required');
        }
        modal.classList.add("active");
    };

    const closeModal = () => {
        modal.classList.remove("active");
    };

    // EVENT LISTENERS

    // Add button
    if (btnAdd) {
        btnAdd.addEventListener("click", () => {
            openModal('add');
        });
    }

    // Close & Cancel buttons
    if (btnClose) btnClose.addEventListener("click", closeModal);
    if (btnCancel) btnCancel.addEventListener("click", closeModal);
    if (modalOverlay) {
        modalOverlay.addEventListener("click", (e) => {
            if (e.target === modalOverlay) closeModal();
        });
    }

    // Edit buttons
    btnsEdit.forEach(btn => {
        btn.addEventListener("click", async (e) => {
            e.stopPropagation();
            const catalogId = btn.getAttribute("data-id");

            try {
                // Panggil API Get One
                const response = await fetch(`api/catalog_api.php?action=get_one&id=${catalogId}`);
                const result = await response.json();

                if (result.status === 'success') {
                    openModal('edit', result.data); 
                } else {
                    showToast("Failed to fetch catalog: " + result.message, 'error');
                }

            } catch (error) {
                console.error("Error fetching catalog data:", error);
                showToast("System error occurred while fetching data.", 'error');
            }
        });
    });

    // Delete buttons
    btnsDelete.forEach(btn => {
        btn.addEventListener("click", async (e) => {
            e.stopPropagation();
            
            if (!confirm("Are you sure you want to delete this catalog?")) return;

            const catalogId = btn.getAttribute("data-id");
            const formData = new FormData();
            formData.append('id', catalogId);

            try {
                const response = await fetch('api/catalog_api.php?action=delete', {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    console.error("JSON Parse Error. Response was:", text);
                    showToast("System error: Invalid response format", 'error');
                    return;
                }

                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    // Delay reload to show success toast
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast("Failed to delete: " + result.message, 'error');
                }

            } catch (error) {
                console.error("Network Error:", error);
                showToast("Network error occurred while deleting.", 'error');
            }
        });
    });

    // Form submission
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const catalogId = catalogIdInput.value;
        
        let action = catalogId ? 'update' : 'add';
        let apiUrl = `api/catalog_api.php?action=${action}`;

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            
            let result;
            try {
                result = JSON.parse(text);
            } catch (parseError) {
                console.error("JSON Parse Error. Response was:", text);
                showToast("System error: Invalid response format", 'error');
                return;
            }

            if (result.status === 'success') {
                showToast(result.message, 'success');
                closeModal();
                // Delay reload to show success toast
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast("Failed: " + result.message, 'error');
            }

        } catch (error) {
            console.error("Network Error:", error);
            showToast("Network error occurred.", 'error');
        }
    });
});