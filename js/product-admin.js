document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("admin-product-modal");
    if (!modal) return;

    tinymce.init({
        selector: '#prod-info', 
        height: 300,
        menubar: false,
        plugins: 'lists link',
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | removeformat',
        skin: 'oxide-dark', 
        content_css: 'dark',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save(); 
            });
        }
    });
    
    const form = document.getElementById("admin-product-form");
    const closeBtn = document.getElementById("admin-product-close-btn");
    const cancelBtn = document.getElementById("admin-product-cancel-btn");
    const modalTitle = document.getElementById("product-modal-title");
    const btnAdd = document.getElementById("admin-add-product-btn");
    const btnsEdit = document.querySelectorAll(".admin-button-edit-product");
    const inputs = {
        id: document.getElementById("prod-id"),
        key: document.getElementById("prod-key"),
        category: document.getElementById("prod-category"),
        titleEn: document.getElementById("prod-title-en"),
        titleJp: document.getElementById("prod-title-jp"),
        camX: document.getElementById("prod-cam-x"),
        camY: document.getElementById("prod-cam-y"),
        camZ: document.getElementById("prod-cam-z"),
    };
    const fileInputs = {
        glb: document.getElementById("prod-file-glb"),
        preview: document.getElementById("prod-file-preview"),
        audio: document.getElementById("prod-file-audio"),
    };
    const statusLabels = {
        glb: document.getElementById("status-glb"),
        preview: document.getElementById("status-preview"),
        audio: document.getElementById("status-audio"),
        infoImg: document.getElementById("status-info-img"),
        pdf: document.getElementById("status-pdf"),
        video: document.getElementById("status-video"),
    };

    const openModal = async (mode, id = null) => {
        form.reset();
        Object.values(statusLabels).forEach(label => label.textContent = "");

        if (tinymce.get('prod-info')) {
            tinymce.get('prod-info').setContent('');
        }

        if (mode === 'edit' && id) {
            modalTitle.textContent = "Edit Product";
            inputs.key.setAttribute("readonly", "true");
            inputs.key.style.backgroundColor = "#2d3748";
            Object.values(fileInputs).forEach(input => input.removeAttribute('required'));

            try {
                const response = await fetch(`api/product_api.php?action=get_one&id=${id}`);
                const result = await response.json();

                if (result.status === 'success') {
                    const data = result.data;
                    
                    inputs.id.value = data.id;
                    inputs.key.value = data.product_key;
                    inputs.category.value = data.category;
                    inputs.titleEn.value = data.display_title_en;
                    inputs.titleJp.value = data.display_title_jp || "";
                    
                    if (tinymce.get('prod-info')) {
                        tinymce.get('prod-info').setContent(data.info || "");
                    }

                    inputs.camX.value = data.camera_pos_x;
                    inputs.camY.value = data.camera_pos_y;
                    inputs.camZ.value = data.camera_pos_z;

                    // Update status labels to show current filenames
                    const setStatus = (key, path) => {
                        if (path) {
                            const fileName = path.split('/').pop();
                            statusLabels[key].innerHTML = `Current: <span style="font-weight: 600;">${fileName}</span>`;
                            statusLabels[key].style.color = '#74e7d4';
                        } else {
                            statusLabels[key].textContent = "(Leave empty to keep existing file)";
                            statusLabels[key].style.color = '#9ca3af';
                        }
                    };
                    setStatus('glb', data.glb_file);
                    setStatus('preview', data.preview_img_path);
                    setStatus('audio', data.audio_link);
                    setStatus('infoImg', data.info_img);
                    setStatus('pdf', data.pdf_link);
                    setStatus('video', data.video_link);

                    modal.classList.add("active");
                } else {
                    showToast("Failed to fetch data: " + result.message, 'error');
                }
            } catch (error) {
                console.error(error);
                showToast("System error occurred.", 'error');
            }

        } else {
            modalTitle.textContent = "Add New Product";
            inputs.id.value = "";
            inputs.key.removeAttribute("readonly");
            inputs.key.style.backgroundColor = "";
            Object.values(fileInputs).forEach(input => input.setAttribute('required', 'required'));
            
            modal.classList.add("active");
        }
    };

    const closeModal = () => {
        modal.classList.remove("active");
    };

    // EVENT LISTENERS 

    btnsEdit.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const id = btn.getAttribute("data-id");
            openModal('edit', id);
        });
    });

    if (btnAdd) {
        btnAdd.addEventListener("click", () => {
            openModal('add');
        });
    }

    closeBtn.addEventListener("click", closeModal);
    cancelBtn.addEventListener("click", closeModal);

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        tinymce.triggerSave();

        const formData = new FormData(form);
        const prodId = inputs.id.value;
        let action = prodId ? 'update' : 'add';
        
        try {
            const response = await fetch(`api/product_api.php?action=${action}`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                showToast(result.message, 'success');
                closeModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast("Failed: " + result.message, 'error');
            }
        } catch (error) {
            console.error(error);
            showToast("System error occurred while saving.", 'error');
        }
    });
    
    const style = document.createElement('style');
    style.innerHTML = `
        .tox-tinymce-aux {
            z-index: 10000 !important;
        }
    `;
    document.head.appendChild(style);
});