<?php

namespace App\Http\Controllers;

use App\Events\ApplicationChat;
use App\Models\Customer;
use App\Notifications\UserNotifications;
use App\Services\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Telegram\Bot\Api;


class TelegramController extends Controller
{

    public function handle(Request $request)
    {
        $message = $request->input('message.text');
        $chatId = $request->input('message.chat.id');
        $first_name = $request->input('message.chat.first_name');
        $last_name = $request->input('message.chat.last_name');
        $username = $request->input('message.chat.username');
        $dataR = json_decode($request->getContent(), true);
        Storage::disk('local')->append('json.txt', json_encode(($dataR)));
        if (isset($dataR['message']['document']['file_id'])) {
            // Get the document file ID
            $fileId = $dataR['message']['document']['file_id'];

            Storage::disk('local')->append('file_id.txt', $fileId);
            // Download the document file from Telegram
            $response = file_get_contents("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/getFile?file_id=$fileId");
            $data = json_decode($response, true);
            Storage::disk('local')->append('response.txt', $response);
            $filePath = $data['result']['file_path'];
            $fileUrl = "https://api.telegram.org/file/bot".env('TELEGRAM_BOT_TOKEN')."/".$filePath;

            Storage::disk('uploads')->put(basename($fileUrl), file_get_contents($fileUrl));
//            Image::make(file_get_contents($fileUrl))->resize(300, null, function ($constraint) {
//                $constraint->aspectRatio();
//            })->save(public_path(). '/uploads/thumbnails/' . basename($fileUrl));

            $url = Storage::disk('uploads')->url( basename($fileUrl));


            $path_parts = pathinfo($fileUrl);

//            echo $path_parts['basename']; // output: file.txt




            $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/unknown.svg');
            if(file_exists(public_path('uploads').'/thumbnails/'.$path_parts['extension'].'.svg')){
                $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/'.$path_parts['extension'].'.svg');
            }

            $chatId = $request->input('message.chat.id');
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
                'message' => '_file',
                'thumbnail_url'=>$thumbnail_url,
                'url'=>$url,
                'self' => 1
            ];
            $customer->notify(new UserNotifications($data));
            $customer->load('notifications');
            event(new ApplicationChat($customer, $data));


            $filename = 'your_filename_here';

//            Storage::disk('local')->append('doc.txt', file_get_contents($fileData));
//            Storage::disk('local')->append('fileUrl.txt', file_get_contents($fileUrl));
//            Storage::disk('uploads')->put(time() . '_' .$dataR['message']['document']['file_name'], file_get_contents($fileData));


            return response('OK', 200);
        }


        $photos = $request->input('message.photo');

        if ($photos) {
            // Get the largest version of the photo
            $photo = end($photos);

            // Get the filename and extension of the photo
            $file_id = $photo['file_id'];
            $mime_type = mime_content_type('https://api.telegram.org/file/bot' . env('TELEGRAM_BOT_TOKEN') . '/' . $photo['file_path']);
            $extension = $this->getExtensionFromMimeType($mime_type);
            $filename = $file_id . '.' . $extension;

            // Download the photo from Telegram's servers
            $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/getFile?file_id=' . $file_id;
            $response = json_decode(file_get_contents($url), true);
            $file_path = $response['result']['file_path'];
            $photo_data = file_get_contents('https://api.telegram.org/file/bot' . env('TELEGRAM_BOT_TOKEN') . '/' . $file_path);

            Storage::disk('uploads')->put($filename, $photo_data);

                Image::make($photo_data)->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path(). '/uploads/thumbnails/' . $filename);
                $thumbnail_url = Storage::disk('uploads')->url('thumbnails/' . $filename);
                $url = Storage::disk('uploads')->url( $filename);

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
                'message' => '_file',
                'thumbnail_url'=>$thumbnail_url,
                'url'=>$url,
                'self' => 1
            ];
            $customer->notify(new UserNotifications($data));
            $customer->load('notifications');
            event(new ApplicationChat($customer, $data));

            return 'OK';

            // Do something with the saved photo, e.g. send it to a user or store its path in a database
        }





        Storage::disk('local')->append('example.txt', json_encode($request->all()));



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
    private function getExtensionFromMimeType($mime_type)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
        ];

        return $extensions[$mime_type] ?? '';
    }
    public function upload(Request $request,Customer $customer,Telegram $telegram)
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
                        $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/unknown.svg');
                        if(file_exists(public_path('uploads').'/thumbnails/'.$singleFile->getClientOriginalExtension().'.svg')){
                            $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/'.$singleFile->getClientOriginalExtension().'.svg');
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

                $telegram->sendFile($customer->telegram_id,public_path('uploads').'/'.basename($url));

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
