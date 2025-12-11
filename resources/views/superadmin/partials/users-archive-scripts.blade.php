<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Sync table with dark mode on page load
        const syncTableDark = () => {
            const isDark = document.body.classList.contains('bg-dark');
            const table = document.querySelector('.table');
            if (table) {
                table.classList.toggle('table-dark', isDark);
            }
        };

        // Sync on load
        syncTableDark();

        // Watch for theme changes
        const observer = new MutationObserver(() => {
            syncTableDark();
        });
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });
    });


</script>
