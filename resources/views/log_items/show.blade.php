@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>{{ $logItemTable->customerName }} {{ $logItemTable->userName }} {{ $logItemTable->eventName }} {{ $logItemTable->recordid }} 'ID' adatai</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            <div class="clearfix"></div>
            <div class="box box-primary">
                <div class="box-body"  >
                    <table class="table table-hover table-bordered partners-table" style="width: 100%;"></table>
                </div>
            </div>
            <div class="text-center"></div>

            <div class="card-footer">
                <a href="{{ route('logItems.index') }}" class="btn btn-default">{{ langClass::trans('Kilép') }}</a>
            </div>

            {!! Form::close() !!}

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
                scrollY: 390,
                scrollX: true,
                order: [[0, 'asc']],
                paging: false,
                ajax: "{{ route('logItemTableDetailIndex', $logItemTable->id) }}",
                columns: [
                    {title: <?php echo "'" . langClass::trans('Mező') . "'"; ?>, data: 'changedfield', name: 'changedfield'},
                    {title: <?php echo "'" . langClass::trans('Régi') . "'"; ?>, data: 'oldValue', sClass: "text-center", width: '150px', name: 'oldValue', orderable: false, searchable: false},
                    {title: <?php echo "'" . langClass::trans('Új') . "'"; ?>, data: 'newValue', sClass: "text-center", width: '150px', name: 'newValue', orderable: false, searchable: false},
                ],
                buttons: [],
            });

        });
    </script>
@endsection
