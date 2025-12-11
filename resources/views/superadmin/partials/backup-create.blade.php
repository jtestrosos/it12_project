<div class="row mb-4">
    <div class="col-12">
        <div class="backup-card">
            <div class="d-flex align-items-center mb-3">
                <div class="backup-icon backup-info me-3">
                    <i class="fas fa-server"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">System Backup</h5>
                    <p class="text-muted mb-0">Create a complete backup including database (PostgreSQL dump) and all
                        uploaded files</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-lg d-flex align-items-center" onclick="createBackup(this)">
                    <i class="fas fa-download me-2"></i>
                    <span>Create Backup</span>
                </button>
                <button class="btn btn-outline-secondary d-flex align-items-center" onclick="location.reload()">
                    <i class="fas fa-sync me-2"></i>
                    <span>Refresh</span>
                </button>
            </div>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Backup includes: PostgreSQL database dump + uploaded files (storage & uploads)
                </small>
            </div>
        </div>
    </div>
</div>
