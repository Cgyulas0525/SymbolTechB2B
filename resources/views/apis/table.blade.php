<div class="table-responsive">
    <table class="table" id="apis-table">
        <thead>
            <tr>
                <th>Filename</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($apis as $api)
            <tr>
                <td>{{ $api->filename }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['apis.destroy', $api->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('apis.show', [$api->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('apis.edit', [$api->id]) }}" class='btn btn-default btn-xs'>
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
