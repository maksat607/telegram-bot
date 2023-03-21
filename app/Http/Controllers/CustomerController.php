<?php

namespace App\Http\Controllers;

use App\Events\ApplicationChat;
use App\Models\Customer;
use App\Notifications\UserNotifications;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function event(Customer $customer)
    {


        $bin = rand(0, 1);
        $data = [
            'user_id' => $bin ? 0 : auth()->id(),
            'curomer_id' => $customer->id,
            'message' => 'tt' . fake()->text(100),
            'self' => $bin
        ];
        $customer->notify(new UserNotifications($data));
        $customer->load('notifications');
        event(new ApplicationChat($customer, $data));

        echo now();

    }

    public function chat()
    {
        $customers = Customer::with('notifications')->get();
        return view('chat', compact('customers'));
    }

    public function respond(Request $request, Customer $customer)
    {
        $data = [
            'user_id' => auth()->id(),
            'curomer_id' => $customer->id,
            'message' => $request->message,
            'self' => 0
        ];
        $customer->notify(new UserNotifications($data));
        $customer->load('notifications');
        event(new ApplicationChat($customer, $data));

        //        $url = URL::to('/uploads') . '/' . $file;
//        file_put_contents($upload_dir, $image_base64);
//        chmod(public_path() . '/uploads/' . $file, 0777);


//        $data = [
//            'chat_id' => $customer->telegram_id,
//            'text' => $request->message
//        ];
//        try {
//            $response = file_get_contents("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage?" . http_build_query($data) );
//        }catch(Exception $e) {
//            echo 'Message: ' .$e->getMessage();
//        }
//
//
//        return true;


        $file_path = public_path().'/fon.jpg';

// Create a Guzzle client
        $client = new Client();

// Create a multipart form request with the file



        $form_params = [
            [
                'name' => 'chat_id',
                'chat_id' => $customer->telegram_id,
            ],
            [
                'name' => 'document',
                'contents' => $file_path
            ]
        ];



        $response = Http::attach(
            'document', file_get_contents($file_path), 'photo.jpg'
        )->post("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendDocument", [
            'chat_id' => $customer->telegram_id
        ]);
// Handle the response
        if ($response->getStatusCode() == 200) {
            // Success
        } else {
            // Error
        }


    }

    public function messages(Customer $customer)
    {


        $customerView = view('customer', ['customer' => $customer])->render();
        $messagesView = view('messages', ['messages' => $customer->notifications->sortBy('created_at')])->render();
        return [
            'id' => $customer->id,
            'customer' => $customerView,
            'customer_id' => 'customer-' . $customer->id,
            'messages' => $messagesView
        ];


//        $content = view('messages',compact('messages','customers','customer'))->render();
//        $header = view('message-header',compact('customer'))->render();
//        return compact('content','header');
    }
}
