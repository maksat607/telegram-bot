<?php

namespace App\Http\Controllers;

use App\Events\ApplicationChat;
use App\Models\Customer;
use App\Notifications\UserNotifications;
use App\Services\Telegram;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\TelegramBotHandler;
use Illuminate\Support\Facades\File;

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


    public function respond(Request $request, Customer $customer, Telegram $telegramBot)
    {
        Log::info('responding to customer' . $request->message);
        $data = [
            'user_id' => auth()->id(),
            'curomer_id' => $customer->id,
            'message' => $request->message,
            'self' => 0
        ];
        $customer->notify(new UserNotifications($data));
        $customer->load('notifications');
        event(new ApplicationChat($customer, $data));
        $response = $telegramBot->sendMessage( $customer->telegram_id, $request->message);

    }

    public function mark(Customer $customer){
        $customer->unreadNotifications->markAsRead();
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
    }
}
