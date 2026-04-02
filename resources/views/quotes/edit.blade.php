<form method="POST" action="{{ route('quotes.update', $quote->id) }}">
    @csrf
@method('PUT')

    <h2>Edit Quote</h2>

    @foreach($quote->items as $index => $item)
        <input type="text" name="items[{{ $index }}][service]" value="{{ $item->service_name }}">
        <input type="number" name="items[{{ $index }}][qty]" value="{{ $item->quantity }}">
        <input type="number" name="items[{{ $index }}][price]" value="{{ $item->unit_price }}">
    @endforeach

    <button type="submit">Update Quote</button>
</form>
