<?php

namespace App\Events;


use App\Models\Customer;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use ProtoneMedia\Splade\Facades\Splade;

class ApplicationChat implements ShouldBroadcast
{
    public function __construct(public $customer,public $data)
    {

    }

    public function broadcastOn()
    {
        return new PrivateChannel('user-1');
    }
//    public function broadcastAs()
//    {
//        return 'chat';
//    }
    public function broadcastWith()
    {
        $customer = view('customer',['customer'=>$this->customer])->render();
        $messages = view('messages',['messages'=>$this->customer->notifications->sortBy('created_at')])->render();
        return [
            'id'=>$this->customer->id
//            'customer'=>$customer,
//            'id'=>'customer-'.$this->customer->id,
//            'messages'=>$messages
        ];
    }


}
