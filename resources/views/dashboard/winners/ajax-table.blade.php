<!-- resources/views/dashboard/winner_groups/ajax-table.blade.php -->

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($winnerGroups as $winnerGroup)
            <tr>
                <td>{{ $winnerGroup->id }}</td>
                <td>{{ $winnerGroup->name }}</td>
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
