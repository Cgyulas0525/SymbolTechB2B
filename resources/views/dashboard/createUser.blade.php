@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    @if (App\Models\Employee::count() == 0)
                        <h1>{{ \App\Classes\langClass::trans('Kezdeti adatbetöltés') }}</h1>
                        @if  (env('INSTALL_STATUS') == 0)
                            <h1><a href="{!! route('getSUXSD') !!}" class="btn btn-success firstSUDataUploadButton">{{ \App\Classes\langClass::trans('SÜ Adat struktúra') }}</a></h1>
                        @endif
                        @if (env('INSTALL_STATUS') == 1)
                            <h1><a href="{!! route('getSUXML') !!}" class="btn btn-success firstSUDataUploadButton">{{ \App\Classes\langClass::trans('SÜ Adatok') }}</a></h1>
                        @endif
                        <div>
                            @if  (env('INSTALL_STATUS') == 0)
                                <p class="h3 text-center fs-6">{{ \App\Classes\langClass::trans('Kérem exportálja ki az adatokat struktúráját a Symbol Tech Ügyviteli rendszeréből!') }}</p>
                            @endif
                            @if (env('INSTALL_STATUS') == 1)
                                <p class="h3 text-center fs-6">{{ \App\Classes\langClass::trans('Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!') }}</p>
                            @endif
                            <p class="h3 text-center fs-6">{{ \App\Classes\langClass::trans('Ha sikerült, a fenti gombbal importálja az adatokat!') }}</p>
                        </div>
                    @else
                        <h1>{{ \App\Classes\langClass::trans('Belső felhasználó hozzáadás') }}</h1>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">
            @if (App\Models\Employee::count() > 0)
                @if (env('MAIL_SET') == 0)
                    <div class="card-body">
                        <div class="row">
                            @include('setting.fields')
                        </div>
                    </div>

                    <div class="card-footer">
                        {!! Form::submit(\App\Classes\langClass::trans('Ment'), ['class' => 'btn btn-primary', 'id' => 'saveBtn']) !!}
                        <a href="{{ route('myLogin') }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kilép') }}</a>
                    </div>

                    {!! Form::close() !!}
                @else
                    {!! Form::open(['route' => 'users.store']) !!}

                    <div class="card-body">

                        <div class="row">
                            @include('users.fields')
                        </div>

                    </div>

                    <div class="card-footer">
                        @if (App\Models\Employee::count() > 0)
                            {!! Form::submit(\App\Classes\langClass::trans('Ment'), ['class' => 'btn btn-primary', 'id' => 'saveBtn']) !!}
                        @endif
                        <a href="{{ route('myLogin') }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kilép') }}</a>
                    </div>

                    {!! Form::close() !!}
                @endif
            @endif
        </div>
    </div>
@endsection
