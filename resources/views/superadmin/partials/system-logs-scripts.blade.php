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

    document.addEventListener('show.bs.modal', function (event) {
        const modal = document.getElementById('logDetailsModal');
        if (!modal || event.target !== modal) return;
        const trigger = event.relatedTarget;
        if (!trigger) return;

        const get = (key) => trigger.getAttribute('data-' + key) || '';

        const user = get('user');
        const action = get('action');
        const tableName = get('table');
        const recordId = get('record');
        const status = get('status');
        const ip = get('ip');
        const timestamp = get('timestamp');
        const oldRaw = get('old');
        const newRaw = get('new');

        const setText = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value || 'N/A';
        };

        setText('log-user', user);
        setText('log-action', action);
        setText('log-table', tableName);
        setText('log-record', recordId);
        setText('log-status', status);
        setText('log-ip', ip);
        setText('log-timestamp', timestamp);

        const oldWrapper = document.getElementById('log-old-wrapper');
        const newWrapper = document.getElementById('log-new-wrapper');
        const oldPre = document.getElementById('log-old');
        const newPre = document.getElementById('log-new');

        try {
            if (oldRaw && oldRaw !== 'null') {
                oldWrapper.style.display = '';
                oldPre.textContent = JSON.stringify(JSON.parse(oldRaw), null, 2);
            } else {
                oldWrapper.style.display = 'none';
                oldPre.textContent = '';
            }
        } catch (e) {
            oldWrapper.style.display = '';
            oldPre.textContent = oldRaw;
        }

        try {
            if (newRaw && newRaw !== 'null') {
                newWrapper.style.display = '';
                newPre.textContent = JSON.stringify(JSON.parse(newRaw), null, 2);
            } else {
                newWrapper.style.display = 'none';
                newPre.textContent = '';
            }
        } catch (e) {
            newWrapper.style.display = '';
            newPre.textContent = newRaw;
        }
    });
</script>
