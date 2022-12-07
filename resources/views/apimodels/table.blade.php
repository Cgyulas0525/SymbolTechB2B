<div class="table-responsive">
    <table class="table" id="apimodels-table">
        <thead>
            <tr>
                <th>Api Id</th>
        <th>Model</th>
        <th>Recordnumber</th>
        <th>Insertednumber</th>
        <th>Updatednumber</th>
        <th>Errornumber</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($apimodels as $apimodel)
            <tr>
                <td>{{ $apimodel->api_id }}</td>
            <td>{{ $apimodel->model }}</td>
            <td>{{ $apimodel->recordnumber }}</td>
            <td>{{ $apimodel->insertednumber }}</td>
            <td>{{ $apimodel->updatednumber }}</td>
            <td>{{ $apimodel->errornumber }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['apimodels.destroy', $apimodel->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('apimodels.show', [$apimodel->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('apimodels.edit', [$apimodel->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
