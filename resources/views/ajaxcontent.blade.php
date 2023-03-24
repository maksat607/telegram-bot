@foreach($customers as $customer)
    <div id="customer-{{$customer->id}}">
        @include('customer')
    </div>
@endforeach
