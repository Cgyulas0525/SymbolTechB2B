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
                        <h4>{{ langClass::trans('Nyelvek') }}</h4>
                    </section>
                    @include('flash::message')
                    <div class="clearfix"></div>
                    <div class="box box-primary">
                        <div class="box-body"  >
                            <table class="table table-hover table-bordered partners-table" style="width: 100%;"></table>
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

    <script type="text/javascript">
        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 450,
                scrollX: true,
                paginate: false,
                order: [1, 'asc'],
                ajax: "{{ route('languages.index') }}",
                columns: [
                    {title: '', data: 'action', sClass: "text-center", width: '200px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . langClass::trans('Név') . "'"; ?>, data: 'lname', name: 'lname'},
                    {title: <?php echo "'" . langClass::trans('Nemzetiség') . "'"; ?>, data: 'nemz', name: 'nemz'},
                    {title: <?php echo "'" . langClass::trans('Tétel') . "'"; ?>, data: 'DetailNumber', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'DetailNumber'},
                    {title: <?php echo "'" . langClass::trans('Fordított') . "'"; ?>, data: 'TranslatedNumber', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'TranslatedNumber'},
                    {title: <?php echo "'" . langClass::trans('Fordítatlan') . "'"; ?>, data: 'UnTranslatedNumber', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'UnTranslatedNumber'},
                ],
                buttons: []
            });

        });
    </script>
@endsection


