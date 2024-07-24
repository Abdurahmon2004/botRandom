<!-- resources/views/dashboard/winner_groups/addModal.blade.php -->

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Omadli ishtirokchilarni aniqlash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <p class="text-danger text-center" style="display: none" id="await_error">Ro'yhatdan o'tganlar yetarli emas</p>

            <form id="addWinner">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Soni</label>
                        <input type="number" class="form-control" id="count" name="count" required>
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
    $('#addWinner').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: '{{ route('winner.store',$id) }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#addModal').modal('hide');
                $('#addWinner')[0].reset();
                loadWinner();
            },
            error: function(error) {
                $('#await_error').removeAttr('style')
            }
        });
    });
</script>
