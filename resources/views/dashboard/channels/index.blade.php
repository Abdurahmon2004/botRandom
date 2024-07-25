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
                        <li class="breadcrumb-item active">Kanallar</li>
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
                        <span class="fas fa-plus-circle"></span> Yangi kanal qo'shish
                    </button>
                </div>
                <div class="card">
                    <div class="card-body" id="ajax-request">
                        @include('dashboard.channels.ajax-table', ['channels' => $channels])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('dashboard.channels.editModal')
@include('dashboard.channels.addModal')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function loadChannels() {
        $.ajax({
            url: '{{ route('channels.index') }}',
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

    $('#addChannelForm').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: '{{ route('channels.store') }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#addModal').modal('hide');
                $('#addChannelForm')[0].reset();
                loadChannels();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    $('#editChannelForm').submit(function(e) {
        e.preventDefault();

        var id = $('#edit_id').val();
        var formData = $(this).serialize();

        $.ajax({
            url: '/channels/' + id,
            type: 'PUT',
            data: formData,
            success: function(response) {
                $('#editModal').modal('hide');
                loadChannels();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    $(document).on('click', '.editChannel', function() {
        var id = $(this).data('id');
        $('#edit_id').val(id);
        $('#edit_channel').val($(this).data('channel'));
        $('#editModal').modal('show');
    });
</script>
@endsection
