@foreach($messages as $message)
    <div class="msg @if($message->data['self'] != 0) messageReceived @else messageSent @endif">
        {{ $message->data['message'] }}
        <i class="material-icons readStatus">done</i>
        <span class="timestamp">{{ $message->created_at }}</span>
    </div>
@endforeach
