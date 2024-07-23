<!-- resources/views/dashboard/winner_groups/editModal.blade.php -->

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Edit Winner Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editWinnerGroupForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="updateWinnerGroupBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#editWinnerGroupForm').submit(function(e) {
        e.preventDefault();

        var id = $('#edit_id').val();
        var formData = $(this).serialize();

        $.ajax({
            url: '/winner-groups/' + id,
            type: 'PUT',
            data: formData,
            success: function(response) {
                $('#editModal').modal('hide');
                loadWinnerGroups();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    $(document).on('click', '.editWinnerGroup', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
    });
</script>
