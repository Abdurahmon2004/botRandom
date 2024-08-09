<!-- resources/views/dashboard/winner_groups/ajax-table.blade.php -->

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>F.I.O</th>
            <th>Telefon raqami</th>
            <th>Hududi</th>
            <th>Kiritgan kodi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->person->id }}</td>
                <td>{{ $user->person->name }}</td>
                <td>{{ $user->person->phone }}</td>
                <td>{{ $user->person->region->name }}</td>
                <td>{{ $user->code->code }}</td>
                <td>{{ $user->product }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
