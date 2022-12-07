<!-- Huname Field -->

<h1>{{ $logItemTable->id }}</h1>

@foreach(App\Models\LogItemTableDetail::where('logitemtable_id', 58)->get() as $item)
    <h1>{{ $item->changedfield }}</h1>
@endforeach

