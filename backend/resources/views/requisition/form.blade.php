@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

<!-- Display current selections -->
@foreach(session('selected_items', []) as $item)
    <div>
        {{ $item['type'] }} ID: {{ $item['id'] }}
    </div>
@endforeach