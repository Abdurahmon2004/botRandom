<div class="modal fade" id="addCodesModal" tabindex="-1" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Kodlar sonini oshirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <p class="text-danger text-center" id="await" style="display: none">Iltimos hech qayerga tegmay turing! Kuting!!</p>
            <p class="text-danger text-center" id="await_error" style="display: none">Nimadur xatolik ketdi qaytadan uruning!</p>
            <form id="addCodes">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="group_id" id="group_id">
                    <div class="mb-3">
                        <label for="Holati">Kodlar soni</label><br>
                        <input type="number" name="code_count" id="count" class="form-control">
                        <p class="text-danger" id="codeId" style="display: none">10 mingtadan ko'p kiritish mumkin emas</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                    <button type="submit" class="btn btn-primary" id="codesAdd">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#addCodes').submit(function(e) {
        e.preventDefault();
        var codeCount = $('#count').val();
        if (codeCount > 25000) {
            $('#count').addClass('is-invalid');
            $('#codeId').removeAttr('style');
            return;
        } else {
            $('#codeId').attr('style','display: none');
            $('#count').removeClass('is-invalid');
        }
        var formData = $(this).serialize();
        var id = $('#group_id').val();
        $.ajax({
            url: '/codes-group/'+id,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#await').removeAttr('style');
            },
            success: function(response) {
                $('#addCodesModal').modal('hide');
                $('#addCodes')[0].reset();
                loadGroup();
            },
            error: function(error) {
                $('#await_error').removeAttr('style');
                console.log(error);
            }
        });
    });
    $(document).on('click', '#addCodesId', function() {
        var id = $(this).data('id');
            $('#group_id').val(id);
            console.log(id)
    });
</script>
