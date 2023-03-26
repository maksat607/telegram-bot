@foreach($messages as $message)
    <div class="msg @if($message->data['self'] != 0) messageReceived @else messageSent @endif">
        @if(trim($message->data['message'])=='audio')
            <audio controls>
                <source src="{{  str_replace('\\', '', $message->data['url']) }}" type="audio/ogg">
            </audio>
            <a href="{{  str_replace('\\', '', $message->data['url']) }}" download="">
                basename($message->data['url'])
            </a>
        @elseif(trim($message->data['message'])=='_video')
            <video controls>
                <source src="{{  str_replace('\\', '', $message->data['url']) }}" type="video">
            </video>
        @elseif($message->data['message']=='_file')
            <a href="{{  str_replace('\\', '', $message->data['url']) }}" download="">
                <img src="{{  str_replace('\\', '', $message->data['thumbnail_url']) }}" alt="Document" width="200"
                     height="200">
            </a>
            <span>{{ basename($message->data['url']) }}</span>
        @else
            {{ $message->data['message'] }}
        @endif
        <i class="material-icons readStatus">done</i>
        <span class="timestamp">{{ $message->created_at->format('d.m.Y H:i') }}</span>
    </div>
@endforeach

