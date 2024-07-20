<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->status == 0 ? 'Nofaol': 'Faol' }}</td>
                <td>
                    <button type="button"
                    class="btn btn-primary btn-sm editProduct" data-bs-toggle="modal"
                    data-bs-target="#editModal"
                    data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-status="{{ $product->status }}"
                    >
                    Tahrirlash
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
