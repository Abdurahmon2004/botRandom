<!-- resources/views/dashboard/winner_groups/ajax-table.blade.php -->

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nomi</th>
            <th>G'oliblar soni</th>
            <th>Amallar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($winnerGroups as $winnerGroup)
            <tr>
                <td>{{ $winnerGroup->id }}</td>
                <td><a href="{{route('winnerUsers.index',$winnerGroup->id)}}">{{ $winnerGroup->name }}</a></td>
                <td>{{ $winnerGroup->users->count() }}</td>
                <td>
                    <button type="button"
                    class="btn btn-primary btn-sm editWinnerGroup" data-bs-toggle="modal"
                    data-bs-target="#editModal"
                    data-id="{{ $winnerGroup->id }}" data-name="{{ $winnerGroup->name }}"
                    >
                    Edit
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
