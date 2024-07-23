<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Hududi</th>
            <th>Telefon Raqami</th>
            <th>F.I.O</th>
            <th>Qo'shilgan sanasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tg_users as $tg_user)
            <tr>
                <td>{{ $tg_user->id }}</td>
                <td>{{ $tg_user->region->name }}</td>
                <td>{{ $tg_user->phone }}</td>
                <td>{{ $tg_user->name }}</td>
                <td>{{ $tg_user->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<ul class="pagination pagination-rounded justify-content-end mb-2">
    {{ $tg_users->links() }}
</ul>
