<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Yangi kanal qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addChannelForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="channel" class="form-label">Kanal</label>
                        <input type="text" class="form-control" id="channel" name="channel" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                    <button type="submit" class="btn btn-primary" id="addChannelBtn">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>
