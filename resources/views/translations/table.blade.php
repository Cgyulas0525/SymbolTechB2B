<div class="table-responsive">
    <table class="table" id="translations-table">
        <thead>
            <tr>
                <th>Huname</th>
        <th>Language</th>
        <th>Name</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($translations as $translations)
            <tr>
                <td>{{ $translations->huname }}</td>
            <td>{{ $translations->language }}</td>
            <td>{{ $translations->name }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['translations.destroy', $translations->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('translations.show', [$translations->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('translations.edit', [$translations->id]) }}" class='btn btn-default btn-xs'>
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
