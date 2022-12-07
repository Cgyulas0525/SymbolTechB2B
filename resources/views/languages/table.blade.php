<div class="table-responsive">
    <table class="table" id="languages-table">
        <thead>
            <tr>
                <th>Shortname</th>
        <th>Name</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($languages as $languages)
            <tr>
                <td>{{ $languages->shortname }}</td>
            <td>{{ $languages->name }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['languages.destroy', $languages->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('languages.show', [$languages->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('languages.edit', [$languages->id]) }}" class='btn btn-default btn-xs'>
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
