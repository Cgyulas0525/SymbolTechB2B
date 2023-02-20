@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7" style="margin-top: 2%">
                <div class="box">
                    <h3 class="box-title" style="padding: 2%">{{ langClass::trans('Erősítsd meg az e-mail címed') }}</h3>

                    <div class="box-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">{{ langClass::trans('Új ellenőrző linket küldtünk az email címre') }}
                            </div>
                        @endif
                        <p>{{ langClass::trans('Mielőtt folytatná, ellenőrizze e-mailjében, hogy van-e ellenőrző link. Ha nem kapta meg
                            az email') }},</p>
                        <a href="{{ route('verification.resend') }}">{{ langClass::trans('kattintson ide, és ujra küldjük') }}</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
