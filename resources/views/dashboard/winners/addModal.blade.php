<!-- resources/views/dashboard/winner_groups/addModal.blade.php -->

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Add New Winner Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addWinnerGroupForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="addWinnerGroupBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#addWinnerGroupForm').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: '{{ route('winner-groups.store') }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#addModal').modal('hide');
                $('#addWinnerGroupForm')[0].reset();
                loadWinnerGroups();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
</script>
