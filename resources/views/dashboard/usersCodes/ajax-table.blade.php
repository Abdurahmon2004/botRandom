<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>F.I.O</th>
            <th>Telefon Raqami</th>
            <th>Qo'shilgan sanasi</th>
            <th>Amallar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->user->name }}</td>
                <td>{{ $user->user->phone }}</td>
                <td>{{ $user->created_at }}</td>
                <td><button class="btn btn-danger DeleteBtn" type="button"
                       data-id="{{ $user->id }}">O'chirish</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<ul class="pagination pagination-rounded justify-content-end mb-2">
    {{ $users->links() }}
</ul>
<script>
     $(document).on('click', '.DeleteBtn', function() {
        var id = $(this).data('id');
        console.log(id);
        $.ajax({
            url: '/codeDelete'+id,
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#ajax-request').html(response);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
</script>

