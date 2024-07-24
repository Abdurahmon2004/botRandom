@extends('dashboard.app')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->

        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content bg-white p-5">
            <form action="{{route('admin')}}" class="col-3">
                <h4>sana</h4>
                <div class="form-group d-flex align-items-center">
                    <input type="date" required value="{{request()->from}}" class="form-control mr-2" name="from"> <label class="mr-2" for="dan"> dan</label>
                    <input type="date" required value="{{request()->to}}" class="form-control mr-2" name="to"> <label class="mr-2" for="gacha"> gacha</label>
                    <input type="submit" class="btn btn-success">
                </div>
            </form>
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h4>Barcha kodlar: {{ $codes->count() }}</h4>
                                <h5>Foydalanilgan kodlar: {{ $codesActive->count() }}</h5>

                                <p>Kodlar Haqida Ma'lumot</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-code"></i>
                            </div>
                            <a href="#" class="small-box-footer">Batafsil <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h4>Jami Foydalanuvchilar: {{ $users->count() }}</h4>

                                <h5>Kod kiritgan Foydalanuvchilar: {{ $codeUsers }}</h5>

                                <p>Foydalanuvchilar</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <a href="#" class="small-box-footer">Batafsil <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h4>Jami hududlar: {{ $regions->count() }}</h4>

                                <h5>Aktiv hududlar: {{ $regionsActive->count() }}</h5>
                                <p>Hududlar</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="#" class="small-box-footer">Batafsil <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h4>Jami maxsulotlar: {{ $products->count() }}</h4>

                                <h5>Aktiv maxsulotlar: {{ $products->count() }}</h5>
                                <p>Maxsulotlar</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection
