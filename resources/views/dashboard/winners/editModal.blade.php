<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Winner Groupni tahrirlash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editWinnerGroupForm">
                @csrf
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body overflow-auto" style="height: 500px">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nomi</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3 overflow-auto">
                        <label for="edit_products" class="form-label">Maxsulotlar</label>
                        <table class="table table-striped table-hover">
                            <tr>
                                <td>Barchasi</td>
                                <td><input type="checkbox" id="editSelectAllProduct"></td>
                            </tr>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td><input type="checkbox" class="edit_product" name="product_ids[]" value="{{ $product->id }}"></td>
                                </tr>
                            @endforeach
                        </table>
                        <div class="" style="width: 100%; height: 2px; border: 2px solid gray"></div>
                    </div>
                    <div class="mb-3 overflow-auto">
                        <label for="edit_regions" class="form-label">Manzillar</label>
                        <table class="table table-striped table-hover">
                            <tr>
                                <td>Barchasi</td>
                                <td><input type="checkbox" id="editSelectAll"></td>
                            </tr>
                            @foreach ($regions as $region)
                                <tr>
                                    <td>{{ $region->name }}</td>
                                    <td><input type="checkbox" class="edit_region" name="region_ids[]" value="{{ $region->id }}"></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary" id="editWinnerGroupBtn">Yangilash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Barcha regionlarni belgilash
    $('#editSelectAll').change(function() {
        let isChecked = $(this).prop('checked');
        $('.edit_region').prop('checked', isChecked);
    });
    $('.edit_region').change(function() {
        if (!$(this).prop('checked')) {
            $('#editSelectAll').prop('checked', false);
        }
    });

    // Barcha maxsulotlarni belgilash
    $('#editSelectAllProduct').change(function() {
        let isChecked = $(this).prop('checked');
        $('.edit_product').prop('checked', isChecked);
    });
    $('.edit_product').change(function() {
        if (!$(this).prop('checked')) {
            $('#editSelectAllProduct').prop('checked', false);
        }
    });

    // Tahrirlash modalini ochish
    $(document).on('click', '.editWinnerGroup', function() {
        var id = $(this).data('id');
        $('#edit_id').val(id);
        $('#edit_name').val($(this).data('name'));

        var selectedProducts = $(this).data('product-ids');
        var selectedRegions = $(this).data('region-ids');

        $('.edit_product').each(function() {
            $(this).prop('checked', selectedProducts.includes(parseInt($(this).val())));
        });

        $('.edit_region').each(function() {
            $(this).prop('checked', selectedRegions.includes(parseInt($(this).val())));
        });

        $('#editModal').modal('show');
    });

    // Formani yuborish
    $('#editWinnerGroupForm').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var id = $('#edit_id').val();

        $.ajax({
            url: '/winner-groups/' + id,
            type: 'PUT',
            data: formData,
            success: function(response) {
                $('#editModal').modal('hide');
                $('#editWinnerGroupForm')[0].reset();
                loadWinnerGroups();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
</script>
