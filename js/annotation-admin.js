document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("admin-annotation-modal");
    if (!modal) return;

    const closeBtn = document.getElementById("admin-anno-close-btn");
    const cancelBtn = document.getElementById("admin-anno-cancel-btn");
    const listBody = document.getElementById("annotation-list-body");
    const productNameDisplay = document.getElementById("anno-product-name");
    const productIdInput = document.getElementById("anno-product-id");
    const btnAddMode = document.getElementById("btn-add-annotation-mode");

    window.loadAnnotationsList = async (productId) => {
        try {
            const response = await fetch(`api/product_api.php?action=get_annotations&product_id=${productId}`);
            const result = await response.json();

            listBody.innerHTML = ""; 

            if (result.status === 'success' && result.data.length > 0) {
                result.data.forEach(anno => {
                    const row = document.createElement("tr");
                    row.style.borderBottom = "1px solid #475467";
                    
                    row.innerHTML = `
                        <td style="padding: 8px; color: #e0e1dc;">${anno.text}</td>
                        <td style="padding: 8px; text-align: right;">
                            <button class="admin-button-icon btn-delete-anno" data-id="${anno.id}" style="background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer;" title="Delete Annotation"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    `;
                    listBody.appendChild(row);
                });

                document.querySelectorAll(".btn-delete-anno").forEach(btn => {
                    btn.addEventListener("click", async (e) => {
                        e.stopPropagation();
                        if(!confirm("Are you sure you want to delete this annotation?")) return;
                        
                        const id = e.target.getAttribute("data-id");
                        await deleteAnnotation(id);
                    });
                });

            } else {
                listBody.innerHTML = '<tr><td colspan="2" style="text-align:center; padding:10px; color:#ccc;">No annotations for this product yet.</td></tr>';
            }
        } catch (error) {
            console.error("Error loading annotations:", error);
            listBody.innerHTML = '<tr><td colspan="2" style="text-align:center; padding:10px; color:red;">Failed to load data.</td></tr>';
        }
    };

    // DELETE
    const deleteAnnotation = async (id) => {
        try {
            const response = await fetch('api/product_api.php?action=delete_annotation', {
                method: 'POST',
                body: JSON.stringify({ id: id }) 
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                window.loadAnnotationsList(productIdInput.value);
                if (typeof window.refresh3DAnnotations === "function") {
                    window.refresh3DAnnotations();
                }
            } else {
                showToast("Failed to delete " + result.message, 'error');
            }
        } catch (error) {
            console.error(error);
            showToast("System error occurred while deleting.", 'error');
        }
    };

    // MANAGE ANNOTATIONS - Handle per-product button clicks
    document.body.addEventListener("click", (e) => {
        if (e.target.closest(".btn-manage-annotations")) {
            const button = e.target.closest(".btn-manage-annotations");
            const productId = button.getAttribute("data-id");
            const productName = button.getAttribute("data-name");
            
            if (productId && modal) {
                productIdInput.value = productId;
                productNameDisplay.textContent = `Product: ${productName || 'Unknown'}`;
                
                modal.classList.add("active");   
                window.loadAnnotationsList(productId);
            }
        }
    });

    const closeModal = () => {
        modal.classList.remove("active");
    };

    if (closeBtn) closeBtn.addEventListener("click", closeModal);
    if (cancelBtn) cancelBtn.addEventListener("click", closeModal);
    
    modal.addEventListener("click", (e) => {
        if (e.target === modal) closeModal();
    });


    // ADD (with raycasting)
    if (btnAddMode) {
        btnAddMode.addEventListener("click", () => {
            modal.classList.remove("active");

            showToast("Click on 3D model where you want to place the annotation.", 'info');

            if (typeof window.startAnnotationPlacement === "function") {
                
                window.startAnnotationPlacement(async (point) => {
                    const text = prompt("Enter annotation text:");
                    
                    if (text) {
                        await saveAnnotation(productIdInput.value, text, point);
                    } else {
                        modal.classList.add("active");
                    }
                });

            } else {
                modal.classList.add("active");
            }
        });
    }

    // SAVE ANNOTATION
    const saveAnnotation = async (productId, text, point) => {
        try {
            const payload = {
                product_id: productId,
                text: text,
                pos_x: point.x,
                pos_y: point.y,
                pos_z: point.z
            };

            const response = await fetch('api/product_api.php?action=add_annotation', {
                method: 'POST',
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.status === 'success') {
                showToast("Annotation successfully added!", 'success');
                
                modal.classList.add("active");
                window.loadAnnotationsList(productId);
                
                if (window.refresh3DAnnotations) {
                    window.refresh3DAnnotations();
                }

            } else {
                showToast("Failed to save: " + result.message, 'error');
                modal.classList.add("active");
            }

        } catch (error) {
            console.error(error);
            showToast("System error occurred.", 'error');
            modal.classList.add("active");
        }
    };

});