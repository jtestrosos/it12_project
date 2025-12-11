<div class="row mb-4">
    <div class="col-12">
        <div class="backup-card">
            <h5 class="mb-3">
                <i class="fas fa-info-circle me-2"></i> Backup Status
            </h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        @if($lastDatabase)
                            <div class="backup-icon backup-success mx-auto mb-2">
                                <i class="fas fa-check"></i>
                            </div>
                            <h6>Last Database Backup</h6>
                            <small class="text-muted">{{ $lastDatabase->completed_at->diffForHumans() }}</small>
                        @else
                            <div class="backup-icon backup-warning mx-auto mb-2">
                                <i class="fas fa-exclamation"></i>
                            </div>
                            <h6>Last Database Backup</h6>
                            <small class="text-muted">Never</small>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        @if($lastFiles)
                            <div class="backup-icon backup-success mx-auto mb-2">
                                <i class="fas fa-check"></i>
                            </div>
                            <h6>Last File Backup</h6>
                            <small class="text-muted">{{ $lastFiles->completed_at->diffForHumans() }}</small>
                        @else
                            <div class="backup-icon backup-warning mx-auto mb-2">
                                <i class="fas fa-exclamation"></i>
                            </div>
                            <h6>Last File Backup</h6>
                            <small class="text-muted">Never</small>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="backup-icon backup-info mx-auto mb-2">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h6>Next Scheduled</h6>
                        <small class="text-muted">Tomorrow 2:00 AM</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="backup-icon backup-success mx-auto mb-2">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <h6>Storage Used</h6>
                        <small class="text-muted">{{ $storageUsed }} / {{ $storageTotal }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
