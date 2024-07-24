<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nomi</th>
            <th>Maxsulot nomi</th>
            <th>Kodlar soni</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groups as $group)
            <tr>
                <td>{{ $group->id }}</td>
                <td>{{ $group->name }}</td>
                <td>{{ $group->product->name }}</td>
                <td>{{ $group->codes->count() }} ta</td>
                <td>{{ $group->status == 0 ? 'Nofaol': 'Faol' }}</td>
                <td>
                    <button type="button"
                    class="btn btn-primary btn-sm editGroup" data-bs-toggle="modal"
                    data-bs-target="#editModal"
                    data-id="{{ $group->id }}" data-name="{{ $group->name }}" data-status="{{ $group->status }}"
                    data-product_id="{{ $group->product_id }}"
                    >
                    Tahrirlash
                    </button>
                    <button type="button" id="addCodesId" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCodesModal"
                    data-id="{{$group->id}}">
                    Yana kod qo'shish
                    </button>
                    <a href="{{ route('codes.export', $group->id) }}" class="btn btn-success btn-sm">Export to Excel</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
