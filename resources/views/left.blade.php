<div class="leftPanel">
    <header>
        <button class="trigger">
            <svg viewBox="0 0 24 24">
                <path d="M3,6H21V8H3V6M3,11H21V13H3V11M3,16H21V18H3V16Z"></path>
            </svg>
        </button>

        <input class="searchChats" type="search" placeholder="Search...">
    </header>
    <div class="chats">

        @foreach($customers as $customer)
            <div id="customer-{{$customer->id}}">
                @include('customer')
            </div>

        @endforeach

    </div>
</div>
