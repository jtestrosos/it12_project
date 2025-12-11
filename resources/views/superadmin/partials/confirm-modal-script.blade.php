<script>
    (function () {
        const modalEl = document.getElementById('confirmActionModal');
        // Check if modal exists and bootstrap is available
        if (!modalEl || typeof bootstrap === 'undefined') {
            return;
        }

        const modal = new bootstrap.Modal(modalEl);
        const titleEl = document.getElementById('confirmActionTitle');
        const messageEl = document.getElementById('confirmActionMessage');
        const confirmBtn = document.getElementById('confirmActionBtn');

        let pendingForm = null;

        // Attach click handlers to buttons with data-confirm attribute
        document.querySelectorAll('[data-confirm]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                // Prevent default form submission or link following if it's a direct click
                // though typically this is used on type="button" in a form
                e.preventDefault(); 
                
                const form = btn.closest('form');
                if (!form) return;
                pendingForm = form;

                if (titleEl) {
                    titleEl.textContent = btn.getAttribute('data-confirm-title') || 'Confirm Action';
                }
                if (messageEl) {
                    messageEl.textContent = btn.getAttribute('data-confirm-message') || 'Are you sure you want to proceed?';
                }

                modal.show();
            });
        });

        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                if (pendingForm) {
                    pendingForm.submit();
                    pendingForm = null;
                }
                modal.hide();
            });
        }
    })();
</script>
