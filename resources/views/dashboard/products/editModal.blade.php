<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProductForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Holati</label><br>
                        <input type="checkbox" value="1" id="edit_status" name="status" data-bootstrap-switch>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="updateProductBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#editProductForm').submit(function(e) {
        e.preventDefault();

        var id = $('#edit_id').val();
        var formData = $(this).serialize();

        $.ajax({
            url: '/products/' + id,
            type: 'PUT',
            data: formData,
            success: function(response) {
                $('#editModal').modal('hide');
                loadProducts();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    // Edit product
    $(document).on('click', '.editProduct', function() {
        var id = $(this).data('id');
            $('#edit_id').val(id);
            $('#edit_name').val($(this).data('name'));
            $(this).data('status') == 1 ?$('#edit_status').bootstrapSwitch('state', true, true) : '';
            $('#editModal').modal('show');
    });
</script>
