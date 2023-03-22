@foreach($messages as $message)
    <div class="msg @if($message->data['self'] != 0) messageReceived @else messageSent @endif">
        @if($message->data['message']!='_file')
        {{ $message->data['message'] }}
        @else
            <a href="{{  str_replace('\\', '', $message->data['url']) }}" download="">
                <img src="{{  str_replace('\\', '', $message->data['thumbnail_url']) }}" alt="Thumbnail Image">
            </a>
        @endif
        <i class="material-icons readStatus">done</i>
        <span class="timestamp">{{ $message->created_at }}</span>
    </div>
@endforeach
