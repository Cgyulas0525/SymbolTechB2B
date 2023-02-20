@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>404 {{ langClass::trans('Nem létező Oldal') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">404 {{ langClass::trans('Nem létező Oldal') }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="error-page">
                <h2 class="headline text-warning"> 404</h2>

                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> {{ langClass::trans('Nem található az oldal!') }}</h3>

                    <p>
                        {{ langClass::trans('Nem találtuk meg a keresett oldalt!') }}
                    </p>
                    <p>
                        {{ langClass::trans('Vissza térhet a bejelentkező oldalhoz:') }} <a href="{{ route('myLogin') }}"> {{ langClass::trans('Vissza') }}</a>
                    </p>

                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
        </section>
        <!-- /.content -->
    </div>
@endsection
