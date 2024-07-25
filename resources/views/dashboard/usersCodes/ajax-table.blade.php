<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>F.I.O</th>
            <th>Telefon Raqami</th>
            <th>Hududi</th>
            <th>Qo'shilgan sanasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->region->name }}</td>
                <td>{{ $user->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<ul class="pagination pagination-rounded justify-content-end mb-2">
    {{ $users->links() }}
</ul>
