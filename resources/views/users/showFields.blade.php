@section('css')
    @include('layouts.costumcss')
@endsection

<div class="form-group col-sm-5">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('employee_id', \App\Classes\langClass::trans('Felhasználó:')) !!}
            </div>
            <div class="mylabel col-sm-9">
               {!! Form::hidden('id', $users->id,['class'=>'form-control', 'required' => 'true', 'id' => 'id', 'readonly' => 'true' ]) !!}
               {!! Form::text('name', $users->name,['class'=>'form-control', 'required' => 'true', 'id' => 'name', 'readonly' => 'true' ]) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-3">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-2">
                {!! Form::label('email', 'Email:') !!}
            </div>
            <div class="mylabel col-sm-10">
                {!! Form::email('email', $users->email, ['class' => 'form-control', 'maxlength' => 191,'maxlength' => 191, 'id' => 'email', 'readonly' => 'true']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-4">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-2">
                {!! Form::label('rendszergazda', \App\Classes\langClass::trans('Státusz:')) !!}
            </div>
            <div class="mylabel col-sm-10">
                {!! Form::text('rgnev', $users->rgnev,['class'=>'form-control', 'required' => 'true', 'id' => 'rendszergazda', 'readonly' => 'true']) !!}
                {!! Form::hidden('rendszergazda', $users->rendszergazda,['class'=>'form-control', 'required' => 'true', 'id' => 'rendszergazda', 'readonly' => 'true']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-12">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-1">
                {!! Form::label('megjegyzes', \App\Classes\langClass::trans('Megjegyzés:')) !!}
            </div>
            <div class="mylabel col-sm-11">
                {!! Form::textarea('megjegyzes', $users->megjegyzes, ['class' => 'form-control', 'rows' => 4, 'id' => 'megjegyzes', 'readonly' => 'true']) !!}
            </div>
        </div>
    </div>
</div>

@section('scripts')

    @include('functions.sweetalert_js')

    <script type="text/javascript">
        $(function () {

            $('#saveBtn').on('click', function (event) {
                event.preventDefault();

                const url = $(this).attr('href');
                swal.fire({
                    title: <?php echo "'" . App\Classes\langClass::trans("Biztos törli a tételt?") . "'"; ?>,
                    text: <?php echo "'" . App\Classes\langClass::trans("Ez a bejegyzés véglegesen törlődik!") . "'"; ?>,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Törlés",
                    cancelButtonText: "Kilép"
                }).then((result) => {
                    if (result.isConfirmed) {
                        if ( <?php echo $users->rendszergazda ?> == 0 ) {
                            $.ajax({
                                type: "GET",
                                url: "{{ route('B2BUserDestroy', $users->id) }}",
                                success: function (data) {
                                    window.location.href = url;      //
                                }
                            });
                        } else {
                            $.ajax({
                                type: "GET",
                                url: "{{ route('belsoUserDestroy', $users->id) }}",
                                success: function (data) {
                                    window.location.href = url;      //
                                }
                            });
                        }
                    }
                });
            });

        });

    </script>

@endsection

