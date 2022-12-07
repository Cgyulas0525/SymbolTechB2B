<div class="table-responsive">
    <table class="table" id="apimodelerrors-table">
        <thead>
            <tr>
                <th>Apimodel Id</th>
        <th>Smtp</th>
        <th>Error</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($apimodelerrors as $apimodelerror)
            <tr>
                <td>{{ $apimodelerror->apimodel_id }}</td>
            <td>{{ $apimodelerror->smtp }}</td>
            <td>{{ $apimodelerror->error }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['apimodelerrors.destroy', $apimodelerror->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('apimodelerrors.show', [$apimodelerror->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('apimodelerrors.edit', [$apimodelerror->id]) }}" class='btn btn-default btn-xs'>
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
