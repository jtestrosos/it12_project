<script>
    function createBackup(buttonElement) {
        if (confirm('Are you sure you want to create a system backup?')) {
            const button = buttonElement || event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Creating backup...';
            button.disabled = true;

            fetch('{{ route("superadmin.backup.create") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        return response.json().then(data => Promise.reject(data));
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Backup response:', data);
                    if (data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message || 'Backup completed successfully!'
                        });
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Backup failed!'
                        });
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Backup error:', error);
                    Toast.fire({
                        icon: 'error',
                        title: error.message || 'An error occurred while creating the backup.'
                    });
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
        }
    }

    function scheduleBackup(type) {
        const schedule = prompt(`Enter schedule for ${type} backup (e.g., "daily", "weekly", "monthly"):`);
        if (schedule) {
            fetch('{{ route("superadmin.backup.schedule") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: type,
                    schedule: schedule
                })
            })
                .then(response => response.json())
                .then(data => {
                    Toast.fire({
                        icon: 'success',
                        title: `${type.charAt(0).toUpperCase() + type.slice(1)} backup scheduled for ${schedule}!`
                    });
                })
                .catch(error => {
                    Toast.fire({
                        icon: 'error',
                        title: 'Error scheduling backup: ' + error
                    });
                });
        }
    }
</script>
