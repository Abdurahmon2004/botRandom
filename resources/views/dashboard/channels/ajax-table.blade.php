<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Channel</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($channels as $channel)
        <tr>
            <td>{{ $channel->id }}</td>
            <td>{{ $channel->channel }}</td>
            <td>
                <button type="button" class="btn btn-primary btn-sm editChannel" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $channel->id }}" data-channel="{{ $channel->channel }}">
                    Tahrirlash
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
