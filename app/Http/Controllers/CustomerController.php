<?php

namespace App\Http\Controllers;

use App\Events\ApplicationChat;
use App\Events\NewMessages;
use App\Models\Customer;
use App\Notifications\UserNotifications;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function event(Customer $customer){


        $bin = rand(0,1);
        $data = [
            'user_id'=> $bin ? 0 : auth()->id(),
            'curomer_id'=>$customer->id,
            'message'=>'tt'.fake()->text(100),
            'self'=>$bin
        ];
            $customer->notify(new UserNotifications($data));
        $customer->load('notifications');
        event(new ApplicationChat($customer,$data));

        echo now();

    }
    public function chat(){
        $customers = Customer::with('notifications')->get();
        return view('chat',compact('customers'));
    }

    public function respond(Request $request,Customer $customer){
        $data = [
            'user_id'=> auth()->id(),
            'curomer_id'=>$customer->id,
            'message'=>$request->message,
            'self'=>0
        ];
        $customer->notify(new UserNotifications($data));
        $customer->load('notifications');
        event(new ApplicationChat($customer,$data));



        $data = [
            'chat_id' => $customer->telegram_id,
            'text' => $request->message
        ];
        $response = file_get_contents("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage?" . http_build_query($data) );

        return true;
    }
    public function messages(Customer $customer){




        $customerView = view('customer',['customer'=>$customer])->render();
        $messagesView = view('messages',['messages'=>$customer->notifications->sortBy('created_at')])->render();
        return [
            'id'=>$customer->id,
            'customer'=>$customerView,
            'customer_id'=>'customer-'.$customer->id,
            'messages'=>$messagesView
        ];


//        $content = view('messages',compact('messages','customers','customer'))->render();
//        $header = view('message-header',compact('customer'))->render();
//        return compact('content','header');
    }
}
