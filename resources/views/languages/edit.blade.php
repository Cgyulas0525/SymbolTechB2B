@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>{{ $languages->shortname }} {{ $languages->name }}</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($languages, ['route' => ['languages.update', $languages->id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @include('languages.fields')
                </div>
            </div>

            <div class="card-footer">
{{--                {!! Form::submit( langClass::trans('Ment'), ['class' => 'btn btn-primary']) !!}--}}
                <a href="{{ route('languages.index') }}" class="btn btn-default">{{ langClass::trans('Kilép') }}</a>
            </div>

           {!! Form::close() !!}

        </div>
    </div>
@endsection

