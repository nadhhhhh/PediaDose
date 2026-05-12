// assets/js/script.js

document.addEventListener('DOMContentLoaded', function () {
    // Basic confirmation for delete actions
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide simple alerts after 5 seconds if they don't have important results
    const simpleAlerts = document.querySelectorAll('.alert:not(.alert-result)');
    simpleAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });
});
