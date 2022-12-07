@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="public/css/app.css">
    <link rel="stylesheet" href="public/css/datatables.css">
    @include('layouts.costumcss')
@endsection

@section('content')
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary" >
            <div class="box-body">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <section class="content-header">
                        <h4>{{ \App\Classes\langClass::trans('Vevők') }}</h4>
                    </section>
                    @include('flash::message')
                    <div class="clearfix"></div>
                    <div class="box box-primary">
                        <div class="box-body"  >
                            <table class="table table-hover table-bordered table" style="width: 100%;"></table>
                        </div>
                    </div>
                    <div class="text-center"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    @include('layouts.RowCallBack_js')

    <script type="text/javascript">
        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('.table').DataTable({
                serverSide: true,
                scrollY: 400,
                colReorder: true,
                ajax: "{{ route('customerIndex') }}",
                columns: [
                    {title: <?php echo "'" . App\Classes\langClass::trans('Id') . "'"; ?>, data: 'Id', name: 'Id'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Név') . "'"; ?>, data: 'Name', name: 'Name'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Kereső név') . "'"; ?>, data: 'SearchName', name: 'SearchName'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Kód') . "'"; ?>, data: 'Code', name: 'Code'},
                ],
                columnDefs: [
                    {
                        visible: false,
                        targets: [0]
                    },
                ],
                fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    RCB(nRow, aData, iDisplayIndex, iDisplayIndexFull);
                },
            });

        });
    </script>
@endsection
