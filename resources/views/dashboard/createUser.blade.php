@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    @if (App\Models\Employee::count() == 0)
                        <h1>{{ langClass::trans('Kezdeti adatbetöltés') }}</h1>
                        @if  (env('INSTALL_STATUS') == 0)
                            <h1><a href="#" class="btn btn-success firstSUDataUploadButton" id="getSUXSDButton">{{ langClass::trans('SÜ Adat struktúra') }}</a></h1>
                        @endif
                        @if (env('INSTALL_STATUS') == 1)
                            <h1><a href="{!! route('getSUXMLInstall') !!}" class="btn btn-success firstSUDataUploadButton">{{ langClass::trans('SÜ Adatok') }}</a></h1>
                        @endif
                        <div>
                            @if  (env('INSTALL_STATUS') == 0)
                                <p class="h3 text-center fs-6">{{ langClass::trans('Kérem exportálja ki az adatokat struktúráját a Symbol Tech Ügyviteli rendszeréből!') }}</p>
                            @endif
                            @if (env('INSTALL_STATUS') == 1)
                                <p class="h3 text-center fs-6">{{ langClass::trans('Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!') }}</p>
                            @endif
                            <p class="h3 text-center fs-6">{{ langClass::trans('Ha sikerült, a fenti gombbal importálja az adatokat!') }}</p>
                        </div>
                    @else
                        @if (env('MAIL_SET') == 0)
                            <h1>{{ langClass::trans('Levelezés beállítás') }}</h1>
                        @else
                            <h1>{{ langClass::trans('Belső felhasználó hozzáadás') }}</h1>
                        @endif
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
                        {!! Form::submit(langClass::trans('Ment'), ['class' => 'btn btn-primary', 'id' => 'saveBtn']) !!}
                        <a href="{{ route('myLogin') }}" class="btn btn-default">{{ langClass::trans('Kilép') }}</a>
                    </div>

                    {!! Form::close() !!}
                @else
{{--                    {!! Form::open(['route' => 'users.store']) !!}--}}

                    <div class="card-body">

                        <div class="row">
                            @include('users.fields')
                        </div>

                    </div>

                    <div class="card-footer">
                        @if (App\Models\Employee::count() > 0)
                            {!! Form::submit(langClass::trans('Ment'), ['class' => 'btn btn-primary', 'id' => 'saveBtn1']) !!}
                        @endif
                        <a href="{{ route('myLogin') }}" class="btn btn-default">{{ langClass::trans('Kilép') }}</a>
                    </div>

                    {!! Form::close() !!}
                @endif
            @endif
        </div>
    </div>
@endsection

@section('scripts')

    <script type="text/javascript">

        var currentLocation = window.location;

        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.getSUXSDButton').click(function (event) {
                event.preventDefault();
                Swal.fire({
                    title: "Biztos, hogy betölti az SÜ adatbázis struktúra exportot?",
                    text: "Adatbázis struktúra változás átvezetés!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: "Betöltés",
                    cancelButtonText: "Kilép",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "GET",
                            url:"{{url('api/structureProcess')}}",
                            success: function (response) {
                                console.log('Response:', response);
                                window.location.href = currentLocation;
                            },
                            error: function (response) {
                                console.log('Error:', response);
                            }
                        });
                    }
                })
            });

            $('.getSUXMLButton').click(function (event) {
                event.preventDefault();
                Swal.fire({
                    title: "Biztos, hogy betölti a SÜ adatbázis exportot?",
                    text: "Adatbázis betöltés!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: "Betöltés",
                    cancelButtonText: "Kilép",
                }).then((result) => {
                    if (result.isConfirmed) {
                        alert(currentLocation);
                        window.location.href = currentLocation;
                        $.ajax({
                            type: "GET",
                            url:"{{url('api/dataProcess')}}",
                            success: function (response) {
                                console.log('Response:', response);
                            },
                            error: function (response) {
                                console.log('Error:', response);
                            }
                        });
                    }
                })
            });

        });
    </script>
@endsection


