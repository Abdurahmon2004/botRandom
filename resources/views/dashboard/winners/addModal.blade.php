<!-- resources/views/dashboard/winner_groups/addModal.blade.php -->

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Add New Winner Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addWinnerGroupForm">
                @csrf
                <div class="modal-body overflow-auto" style="height: 500px">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nomi</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3 overflow-auto" >
                        <label for="name" class="form-label">Maxsulotlar</label>
                       <table class="table table-striped table-hover">
                        <tr>
                            <td>Barchasi</td>
                            <td><input type="checkbox" id="selectAllProduct"></td>
                        </tr>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{$product->name}}</td>
                                <td><input type="checkbox" class="product" name="product_ids[]" value="{{$product->id}}"></td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="" style="width: 100%; height: 2px; border: 2px solid gray"></div>
                    </div>
                    <div class="" style="width: 100%; height: 2px; border: 2px solid gray"></div>
                    <div class="mb-3 overflow-auto">
                        <label for="name" class="form-label">Manzillar</label>
                        <table class="table table-striped table-hover">
                            <tr>
                                <td>Barchasi</td>
                                <td><input type="checkbox" id="selectAll"></td>
                            </tr>
                            @foreach ($regions as $region)
                                <tr>
                                    <td>{{$region->name}}</td>
                                    <td><input type="checkbox" class="region" name="region_ids[]" value="{{$region->id}}"></td>
                                </tr>
                            @endforeach
                        </table>
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
      $('#selectAll').change(function() {
            let isChecked = $(this).prop('checked');
            $('.region').prop('checked', isChecked);
        });
      $('.region').change(function() {
            if (!$(this).prop('checked')) {
                $('#selectAll').prop('checked', false);
            }
        });
        $('#selectAllProduct').change(function() {
            let isChecked = $(this).prop('checked');
            $('.product').prop('checked', isChecked);
        });
      $('.product').change(function() {
            if (!$(this).prop('checked')) {
                $('#selectAllProduct').prop('checked', false);
            }
        });
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
