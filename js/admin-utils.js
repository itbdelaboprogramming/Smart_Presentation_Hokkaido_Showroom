// TOAST GLOBAL FUNCTION (window.showToast)
window.showToast = function(message, type = 'success') {
    let container = document.querySelector('.admin-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'admin-toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `admin-toast ${type}`;
    
    let icon = '<i class="fas fa-check-circle"></i>';
    if (type === 'error') icon = '<i class="fas fa-times-circle"></i>';
    if (type === 'warning') icon = '<i class="fas fa-exclamation-triangle"></i>';

    toast.innerHTML = `
        <span style="margin-right: 10px;">${icon}</span>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = "opacity 0.5s, transform 0.5s";
        toast.style.opacity = "0";
        toast.style.transform = "translateY(-20px)";
        
        setTimeout(() => {
            if (toast.parentElement) toast.remove();
        }, 500);
    }, 3000);
};