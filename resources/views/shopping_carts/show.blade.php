@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ langClass::trans('Kosár törlés') }}</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('shoppingCarts.index') }}">
                        {{ langClass::trans('Vissza', ['customerContact' => ( (empty($_COOKIE['scContact']) ? 0 : $_COOKIE['scContact']) == 0 ? myUser::user()->customercontact_id : -99999),
                                                       'year' => empty($_COOKIE['scYear']) ? date('Y') : $_COOKIE['scYear']]) }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">

            <div class="card-body">
                <div class="row">
                    @include('shopping_carts.show_fields')
                </div>
            </div>

            <div class="card-footer">
                <a class="btn btn-primary"
                   href="{{ route('shoppingCarts.index', ['customerContact' => ( (empty($_COOKIE['scContact']) ? 0 : $_COOKIE['scContact']) == 0 ? myUser::user()->customercontact_id : -99999),
                                                       'year' => empty($_COOKIE['scYear']) ? date('Y') : $_COOKIE['scYear']]) }}">
                    {{ langClass::trans('Vissza') }}
                </a>
                {!! Form::submit(langClass::trans('Töröl'), ['class' => 'btn btn-danger', 'id' => 'saveBtn']) !!}
            </div>

        </div>
    </div>
@endsection
