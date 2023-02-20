@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                        {{ langClass::trans('Kommunikáció beállítások') }}
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            <div class="card-body">
                <div class="row">
                    @include('setting.communicationFields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit(langClass::trans('Ment'), ['class' => 'btn btn-primary', 'id' => 'saveBtn']) !!}
                <a href="{{ route('dIndex') }}" class="btn btn-default">{{ langClass::trans('Kilép') }}</a>
            </div>

           {!! Form::close() !!}

        </div>
    </div>
@endsection
