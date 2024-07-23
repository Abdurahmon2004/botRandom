<!-- resources/views/dashboard/winner_groups/index.blade.php -->

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
                            <li class="breadcrumb-item active">Winner Groups</li>
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
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                            <span class="fas fa-plus-circle"></span> Add New Winner Group
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-body" id="ajax-request">
                            @include('dashboard.winner.ajax-table', ['winnerGroups' => $winnerGroups])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
    integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    @include('dashboard.winner.editModal')
    @include('dashboard.winner.addModal')
    <script>
        function loadWinnerGroups() {
            $.ajax({
                url: '{{ route('winnerGroup.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $('#ajax-request').html(response);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    </script>
@endsection
