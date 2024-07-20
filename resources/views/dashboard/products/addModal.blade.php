<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Yangi maxsulot qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addProductForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nomi</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="Holati">Holati</label><br>
                        <input type="checkbox" value="1" name="status" data-bootstrap-switch>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                    <button type="submit" class="btn btn-primary" id="addProductBtn">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#addProductForm').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: '{{ route('products.store') }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#addModal').modal('hide');
                $('#addProductForm')[0].reset();
                loadProducts();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
</script>
