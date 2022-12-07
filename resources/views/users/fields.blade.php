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
                {!! Form::select('employee_id', ddwClass::employeeNotB2BDDW(), null,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'employee_id']) !!}
                {!! Form::hidden('customercontact_id', null,['class'=>'form-control', 'required' => 'true', 'id' => 'customercontact_id', 'readonly' => 'true' ]) !!}
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
                {!! Form::email('email', null, ['class' => 'form-control','maxlength' => 191,'maxlength' => 191, 'id' => 'email']) !!}
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
                @if ( myUser::user()->name === "administrator")
                    {!! Form::text('rendszergazda', 'Rendszergazda',['class'=>'form-control', 'required' => 'true', 'id' => 'rendszergazda', 'readonly' => 'true']) !!}
                    {!! Form::hidden('rendszergazda_hidden', 3,['class'=>'form-control', 'required' => 'true', 'id' => 'rendszergazda_hidden', 'readonly' => 'true']) !!}
                @else
                    {!! Form::select('rendszergazda', ddwClass::belsoStatuszDDW(), null,['class'=>'select2 form-control', 'required' => 'true', 'id' => 'rendszergazda']) !!}
                @endif
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
                {!! Form::textarea('megjegyzes', null, ['class' => 'form-control', 'rows' => 4, 'id' => 'megjegyzes']) !!}
            </div>
        </div>
    </div>
</div>

@section('scripts')

    @include('functions.ajax_js')
    @include('functions.sweetalert_js')

    <script type="text/javascript">
        $(function () {

            ajaxSetup();

            $('#employee_id').change(function() {
                let employee_id = $('#employee_id').val();
                $.ajax({
                    type: "GET",
                    url:"{{url('api/getUserWithEmployeeId')}}",
                    data: { id: employee_id },
                    success: function (response) {
                        if ( response.name != null ) {
                            sw(<?php echo "'" . App\Classes\langClass::trans("Ennek a felhasználónak már van hozzáférése a rendszerhez!") . "'"; ?>);
                            $("#employee_id").val(null);
                            $("#employee_id").focus();
                        } else {
                            $.ajax({
                                type:"GET",
                                url:"{{url('api/getEmployee')}}",
                                data: { id: employee_id },
                                success:function(res){
                                    if (res.Email != null) {
                                        $("#email").val(res.Email);
                                        $('#email').prop('readonly', true);
                                        $("#rendszergazda").focus();
                                    } else {
                                        sw(<?php echo "'" . App\Classes\langClass::trans("Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!") . "'"; ?>);
                                        $("#employee_id").val(null);
                                        $("#employee_id").focus();
                                    }
                                }
                            });
                        }
                    },
                    error: function (response) {
                        console.log('Error:', response);
                        alert('nem ok');
                    }
                });
            });

            $('#saveBtn').click(function (e) {
                let employeeId = $('#employee_id').val();
                let email = $('#email').val();
                let rendszergazda = $('#rendszergazda').val();

                if ( parseInt(employeeId) == 0) {
                    swMove(<?php echo "'" . App\Classes\langClass::trans("Nem adott meg felhasználót!") . "'"; ?>);
                    e.preventDefault();
                    $('#employee_id').focus();
                    return false;
                } else {
                    if ( email.length == 0) {
                        swMove(<?php echo "'" . App\Classes\langClass::trans("Nem adott meg email címet!") . "'"; ?>);
                        e.preventDefault();
                        $('#email').focus();
                        return false;
                    } else {
                        if ( parseInt(rendszergazda) == 0) {
                            swMove(<?php echo "'" . App\Classes\langClass::trans("Nem adott meg státuszt!") . "'"; ?>);
                            e.preventDefault();
                            $('#rendszergazda').focus();
                            return false;
                        }
                    }
                }
            });
        });

    </script>

@endsection

