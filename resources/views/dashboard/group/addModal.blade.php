<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Yangi maxsulot qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <p class="text-danger text-center" id="await_code" style="display: none">Iltimos hech qayerga tegmay turing! Kuting!!</p>
            <form id="addProductForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nomi</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Maxsulotlar nomi</label>
                        <select name="product_id" class="form-control">
                            @foreach ($products as $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="Holati">Kodlar soni</label><br>
                        <input type="number" name="code_count" id="code_count" class="form-control">
                        <p class="text-danger" id="countId" style="display: none">10 mingtadan ko'p kiritish mumkin emas</p>
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

        var codeCount = $('#code_count').val();

        if (codeCount > 25000) {
            $('#code_count').addClass('is-invalid');
            $('#countId').removeAttr('style');
            return;
        } else {
            $('#countId').attr('style','display: none');
            $('#code_count').removeClass('is-invalid');
        }


        var formData = $(this).serialize();

        $.ajax({
            url: '{{ route('codes.store') }}',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#await_code').removeAttr('style');
            },
            success: function(response) {
                $('#addModal').modal('hide');
                $('#addProductForm')[0].reset();
                loadGroup();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

</script>
