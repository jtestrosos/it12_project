<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Basic Information</h6>
                        <div class="mb-3">
                            <label class="form-label text-muted">User</label>
                            <p class="fw-bold" id="log-user">&nbsp;</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Action</label>
                            <p id="log-action">&nbsp;</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Table</label>
                            <p id="log-table">&nbsp;</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Record ID</label>
                            <p id="log-record">&nbsp;</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Status</label>
                            <p id="log-status">&nbsp;</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Change Details</h6>
                        <div class="mb-3" id="log-old-wrapper" style="display:none;">
                            <strong class="text-dark">Old Values:</strong>
                            <pre class="bg-light p-3 small rounded" style="border: 1px solid #e9ecef;"
                                id="log-old"></pre>
                        </div>
                        <div class="mb-3" id="log-new-wrapper" style="display:none;">
                            <strong class="text-dark">New Values:</strong>
                            <pre class="bg-light p-3 small rounded" style="border: 1px solid #e9ecef;"
                                id="log-new"></pre>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">IP Address</label>
                            <p id="log-ip">&nbsp;</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Timestamp</label>
                            <p id="log-timestamp">&nbsp;</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
