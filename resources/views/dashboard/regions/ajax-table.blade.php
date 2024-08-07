<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nomi</th>
            <th>Holati</th>
            <th>Amallar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($regions as $region)
            <tr>
                <td>{{ $region->id }}</td>
                <td>{{ $region->name }}</td>
                <td>{{ $region->status == 0 ? 'Nofaol': 'Faol' }}</td>
                <td>
                    <button type="button"
                    class="btn btn-primary btn-sm editRegion" data-bs-toggle="modal"
                    data-bs-target="#editModal"
                    data-id="{{ $region->id }}" data-name="{{ $region->name }}" data-status="{{ $region->status }}"
                    >
                    Tahrirlash
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<ul class="pagination pagination-rounded justify-content-end mb-2">
    {{ $regions->links() }}
</ul>
