@extends('dashboard.app')

@section('content')
    <div class="content-wrapper" style="overflow: auto">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('codes.index') }}">Maxsus kodlar</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <p style="clear: both"></p>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="container-fluid" style="overflow: auto">
                    <div class="card-header bg-white d-flex justify-content-end">
                    </div>
                    <div class="card">
                        <div class="card-body" id="ajax-request">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kodi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($codes as $code)
                                        <tr>
                                            <td>{{ $code->id }}</td>
                                            <td>{{ $code->code }}</td>
                                            <td>{{ $code->status == 0 ? 'Nofaol' : 'Faol' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <ul class="pagination pagination-rounded justify-content-end mb-2">
                    {{ $codes->links() }}
                </ul>
            </div>
        </div>
    </div>
@endsection
