@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="pubic/css/app.css">
    @include('layouts.datatables_css')
    @include('layouts.costumcss')
@endsection


@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    @if (App\Models\Employee::count() == 0)
                        <h1>{{ langClass::trans('Kezdeti adatbetöltés') }}</h1>
                        @if  (env('INSTALL_STATUS') == 0)
                            <h1><a href="#" class="btn btn-success firstSUDataUploadButton getSUXSDButton">{{ langClass::trans('SÜ Adat struktúra') }}</a></h1>
                        @endif
                        @if (env('INSTALL_STATUS') == 1)
                            <h1><a href="{!! route('getSUXMLInstall') !!}" class="btn btn-success firstSUDataUploadButton getSUXMLButton">{{ langClass::trans('SÜ Adatok') }}</a></h1>
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

    @include('functions.clickEvent')

    <script type="text/javascript">

        var currentLocation = window.location;

        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function changeEnvironmentVariable(key, value) {
                $.ajax({
                    type: "GET",
                    url:"{{url('api/changeEnvironmentVariable')}}",
                    data: { key: key, value: value },
                    success: function (response) {
                        if ( response.name != null ) {
                            console.log('Error:', response);
                        }
                    },
                    error: function (response) {
                        console.log('Error:', response);
                        alert('nem ok');
                    }
                });
            }


            $('.getSUXSDButton').click(function (event) {

                var url = <?php echo "'" . url('api/structureProcess') . "'"; ?>;
                var title = <?php echo "'" . langClass::trans("Biztos, hogy betölti az SÜ adatbázis struktúra exportot?") . "'"; ?>;
                var text = <?php echo "'" . langClass::trans("Adatbázis struktúra változás átvezetés!") . "'"; ?>;
                var icon = "warning";
                var confirmButtonText = <?php echo "'" . langClass::trans("Betöltés") . "'"; ?>;
                var cancelButtonText = <?php echo "'" . langClass::trans("Kilép") . "'"; ?>;

                clickEvent(event, url, title, text, icon, confirmButtonText, cancelButtonText);

                changeEnvironmentVariable('INSTALL_STATUS', 1);

            });

            $('.getSUXMLButton').click(function (event) {

                var url = <?php echo "'" . url('api/dataProcess') . "'"; ?>;
                var title = <?php echo "'" . langClass::trans("Biztos, hogy betölti a SÜ adatbázis exportot?") . "'"; ?>;
                var text = <?php echo "'" . langClass::trans("Adatbázis betöltés!") . "'"; ?>;
                var icon = "warning";
                var confirmButtonText = <?php echo "'" . langClass::trans("Betöltés") . "'"; ?>;
                var cancelButtonText = <?php echo "'" . langClass::trans("Kilép") . "'"; ?>;

                clickEvent(event, url, title, text, icon, confirmButtonText, cancelButtonText);

                changeEnvironmentVariable('INSTALL_STATUS', 2);

            });

        });
    </script>
@endsection


