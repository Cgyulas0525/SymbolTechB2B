@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="public/css/app.css">
    @include('layouts.costumcss')
@endsection

@section('content')
    <div class="row">

    <div class="col-lg-12 col-md-12 col-xs-12 " style="display: flex;
  justify-content: center;
  align-items: center; margin-top: 5em;">
        <div class="login-box"  >
            <div class="login-logo">
                <a href="{{ url('/home') }}"><img src={{ URL('/public/img/B2B.png') }} style="width: 240px;" alt="B2B"></a>
            </div>

            <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg" style="font-size: 1.5em">Jelszó csere</p>

                <form method="post" action="{{ route('myLogin') }}">
                    {!! csrf_field() !!}

                    <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input type="password" class="form-control" placeholder="Jelszó" name="password" id="password">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="form-group has-feedback{{ $errors->has('password2') ? ' has-error' : '' }}">
                        <input type="password" class="form-control" placeholder="Jelszó újra" name="password2" id="password2">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        @if ($errors->has('password2'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('password2') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox icheck">
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-primary btn-block btn-flat" id="changeBtn">Jelszó csere</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            <!--
                        <a href="{{ url('/password/reset') }}">Elfelejtette jelszavát?</a><br>
                        <a href="{{ url('/register') }}" class="text-center">Regisztráció</a>
-->
            </div>
            <!-- /.login-box-body -->
        </div>
    </div>
    </div>

@endsection

@section('scripts')

    @include('functions.ajax_js')
    @include('functions.sweetalert_js')

    <script type="text/javascript">
        $(function () {

            ajaxSetup();

            $('#changeBtn').click(function (e) {
                let password = $('#password').val();
                let password2 = $('#password2').val();
                if ( password == '' ) {
                    swMove(<?php echo "'" . App\Classes\langClass::trans("Üresen hagyta a jelszó mezőt!") . "'"; ?>);
                    e.preventDefault();
                    $('#password').focus();
                    return false;
                } else {
                    if ( password.length < 8) {
                        swMove(<?php echo "'" . App\Classes\langClass::trans("Jelszónak minimum 8 karakter hosszúnak kell lennie!") . "'"; ?>);
                        e.preventDefault();
                        $('#password').focus();
                        return false;
                    } else {
                        if ( password2 == '' ) {
                            swMove(<?php echo "'" . App\Classes\langClass::trans("Üresen hagyta a jelszó újra mezőt!") . "'"; ?>);
                            e.preventDefault();
                            $('#password2').focus();
                            return false;
                        } else {
                            if ( password != password2 ) {
                                swMove(<?php echo "'" . App\Classes\langClass::trans("Nem egyezik a két jelszó!") . "'"; ?>);
                                e.preventDefault();
                                $('#password').val(null);
                                $('#password2').val(null);
                                $('#password').focus();
                                return false;
                            } else {
                                $.ajax({
                                    type:"GET",
                                    url:"{{url('api/passwordChange')}}",
                                    data: { password: password },
                                    success:function(res){
                                        console.log(res);
                                    }
                                });
                            }
                        }
                    }
                }
            });
        });

    </script>

@endsection
