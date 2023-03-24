@if($customer->notifications()->count()>0)
    @php
        $count = $customer->notifications->filter(function ($notification) {
                $data = $notification->data;
                return $data['self'] == 1;/**/
            })->count()
    @endphp
    <div class="chatButton  @if (isset($loop)&&$loop->first)active @endif " data-id="{{ $customer->id }}">
        <div class="chatInfo">
            <div class="image my-image">

            </div>

            <p style="margin-bottom: unset;" class="name">
                {{ $customer->fullname }}
            </p>
            {{--                @dd($customer->notifications->first()->data)--}}
            @if(!isset($search))
                <p style="margin-bottom: unset;" class="message">{{ substr($customer->notifications()->first()?->data['message'], 0, 10) }} ...</p>
            @else
                <p style="margin-bottom: unset;" class="message">{{ substr($customer->notifications()->where('data','like','%'.$search.'%')->first()?->data['message'], 0, 10) }} ...</p>
            @endif
        </div>

        <div class="status onTop">
            <p style="margin-bottom: unset;" class="date">{{ $customer->notifications()->first()?->created_at->diffForHumans() }}</p>
            <p style="margin-bottom: unset;" class="count">{{ $count }}</p>
            {{--                <i class="material-icons read">done_all</i>--}}

        </div>
    </div>
@endif
