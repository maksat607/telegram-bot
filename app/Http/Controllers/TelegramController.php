<?php

namespace App\Http\Controllers;

use App\Events\ApplicationChat;
use App\Models\Customer;
use App\Notifications\UserNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        Storage::disk('local')->append('example.txt', json_encode($request->all()));
        $message = $request->input('message.text');
        $chatId = $request->input('message.chat.id');
        $first_name = $request->input('message.chat.first_name');
        $last_name = $request->input('message.chat.last_name');
        $username = $request->input('message.chat.username');
        Storage::disk('local')->put('fullname.txt', $message . ' ' . $chatId . ' ' . $first_name . ' ' . $last_name . ' ' . $username);


        $customer = Customer::where('telegram_id', $chatId)->first();
        if (!$customer) {
            $customer = Customer::create([
                'telegram_id' => $chatId,
                'fullname' => $first_name . ' ' . $last_name,
                'telegram_id' => $chatId,
                'username' => $username,
            ]);
        }


        $data = [
            'user_id' => 0,
            'curomer_id' => $customer->id,
            'message' => $message,
            'self' => 1
        ];
        $customer->notify(new UserNotifications($data));
        $customer->load('notifications');
        event(new ApplicationChat($customer, $data));

        return 'OK';
    }

    public function upload(Request $request,Customer $customer)
    {

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $singleFile) {

                    $fileName = time() . '_' . $singleFile->getClientOriginalName();
                    Storage::disk('uploads')->put($fileName, file_get_contents($singleFile));
                    if (in_array(strtolower($singleFile->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'bmp'])) {
                        Image::make($singleFile->path())->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save(public_path(). '/uploads/thumbnails/' . $fileName);
                        $thumbnail_url = Storage::disk('uploads')->url('thumbnails/' . $fileName);
                        $url = Storage::disk('uploads')->url( $fileName);
                    }else{
                        $url = Storage::disk('uploads')->url( $fileName);
                        $thumbnail_url = Storage::disk('icons')->url('unknown.svg');
                        if(file_exists(public_path('icons').'/'.$singleFile->getClientOriginalExtension().'.svg')){
                            $thumbnail_url = Storage::disk('icons')->url($singleFile->getClientOriginalExtension().'.svg');
                        }
                    }
                $data = [
                    'user_id' => auth()->id(),
                    'curomer_id' => $customer->id,
                    'message' => '_file',
                    'thumbnail_url'=>$thumbnail_url,
                    'url'=>$url,
                    'self' => 0
                ];
                $customer->notify(new UserNotifications($data));
                $customer->load('notifications');
                event(new ApplicationChat($customer, $data));
            }
            return response()->json(['message' => 'Files uploaded successfully']);
        } else {
            return response()->json(['message' => 'Please select at least one file to upload']);
        }

    }

    public function start(Request $request)
    {
        Storage::disk('local')->put('example.txt', json_encode($request->all()));
//        $message = $update->getMessage();
//        $chat_id = $message->getChat()->getId();
//        $text = 'Welcome to my bot!';
//        $this->sendMessage($chat_id, $text);
    }

    private function sendMessage($chat_id, $text)
    {
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
        ];


        Telegram::sendMessage($data);
    }
}
