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
    public function notify(Request $request,Customer $customer, $data){
        $customer->notify(new UserNotifications($data));
        $customer->load('notifications');
        event(new ApplicationChat($customer, $data));
        if( $message = $request->input('message.caption')){
            $data['message'] = $message;
            $this->notify($customer,$data);
        }
    }
    public function getInfo(Request $request){
        $message = $request->input('message.text');
        $chatId = $request->input('message.chat.id');
        $first_name = $request->input('message.chat.first_name');
        $last_name = $request->input('message.chat.last_name');
        $username = $request->input('message.chat.username');
        $chatId = $chatId==null ? $request->input('callback_query.message.chat.id') : $chatId;
        return compact('message','chatId','first_name','last_name','username');
    }
    public function uploadAudio(Request $request, Customer $customer ) {
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            $audioName = time().'.ogg';
            $audio->move(public_path('uploads'), $audioName);

            $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/voice.png');
            $url = Storage::disk('uploads')->url($audioName);
            $data = [
                'user_id' => auth()->id(),
                'curomer_id' => $customer->id,
                'message' => '_file',
                'thumbnail_url'=>$thumbnail_url,
                'url'=>$url,
                'self' => 0
            ];
            $this->notify($customer,$data);

            $this->telegram->sendVoice($customer->telegram_id,public_path('uploads').'/'.basename($url));

            return response()->json([
                'success' => true,
                'message' => 'Audio uploaded successfully!',
                'audio_url' => asset('uploads/'.$audioName.'.ogg')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No audio file uploaded.'
            ]);
        }
    }
    public function firstOrCreate($request,$chatId){
        extract($this->getInfo($request));
        $customer = Customer::where('telegram_id', $chatId)->first();
        if (!$customer) {
            $customer = Customer::create([
                'telegram_id' => $chatId,
                'fullname' => $first_name . ' ' . $last_name,
                'telegram_id' => $chatId,
                'username' => $username,
            ]);
        }
        return $customer;
    }
    public function handleVoice(Request $request,$dataR){
        extract($this->getInfo($request));
        $voice = $dataR['message']['voice'];
        $file_id = $voice['file_id'];
        $file_size = $voice['file_size'];

        // Download the voice recording using the Telegram Bot API
        $bot_token =env('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$bot_token}/getFile?file_id={$file_id}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['result']['file_path'])) {
            $file_path = $data['result']['file_path'];
            $file_url = "https://api.telegram.org/file/bot{$bot_token}/{$file_path}";

            // Save the voice recording to disk
            $destination_path = storage_path("app/voice/{$file_id}.ogg");
            Storage::disk('uploads')->put(basename($file_path), file_get_contents($file_url));
            $url = Storage::disk('uploads')->url( basename($file_path));


            $path_parts = pathinfo($file_url);
            $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/voice.png');


            if(file_exists(public_path('uploads').'/thumbnails/'.$path_parts['extension'].'.svg')){
                $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/'.$path_parts['extension'].'.svg');
            }


            $customer = $this->firstOrCreate($request,$chatId);
            $data = [
                'user_id' => 0,
                'curomer_id' => $customer->id,
                'message' => '_file',
                'thumbnail_url'=>$thumbnail_url,
                'url'=>$url,
                'self' => 1
            ];
            $this->notify($customer,$data);

            return 'OK';

        }
    }
    public function handleDocument(Request $request,$dataR){
        extract($this->getInfo($request));
        // Get the document file ID
        $fileId = $dataR['message']['document']['file_id'];

        $response = file_get_contents("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/getFile?file_id=$fileId");
        $data = json_decode($response, true);
        Storage::disk('local')->append('response.txt', $response);
        $filePath = $data['result']['file_path'];
        $fileUrl = "https://api.telegram.org/file/bot".env('TELEGRAM_BOT_TOKEN')."/".$filePath;

        Storage::disk('uploads')->put(basename($fileUrl), file_get_contents($fileUrl));

        $url = Storage::disk('uploads')->url( basename($fileUrl));
        $path_parts = pathinfo($fileUrl);
        $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/unknown.svg');
        if(file_exists(public_path('uploads').'/thumbnails/'.$path_parts['extension'].'.svg')){
            $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/'.$path_parts['extension'].'.svg');
        }

        $chatId = $request->input('message.chat.id');
        $customer = $this->firstOrCreate($request,$chatId);
        $data = [
            'user_id' => 0,
            'curomer_id' => $customer->id,
            'message' => '_file',
            'thumbnail_url'=>$thumbnail_url,
            'url'=>$url,
            'self' => 1
        ];
        $this->notify($customer,$data);


        $filename = 'your_filename_here';

//            Storage::disk('local')->append('doc.txt', file_get_contents($fileData));
//            Storage::disk('local')->append('fileUrl.txt', file_get_contents($fileUrl));
//            Storage::disk('uploads')->put(time() . '_' .$dataR['message']['document']['file_name'], file_get_contents($fileData));


        return response('OK', 200);
    }
    public function handlePhoto(Request $request,$photos)
    {
        $photo = end($photos);
        extract($this->getInfo($request));

        // Get the filename and extension of the photo
        $file_id = $photo['file_id'];

        $response = file_get_contents("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/getFile?file_id=$file_id");
        $data = json_decode($response, true);
        Storage::disk('local')->append('response.txt', $response);
        $filePath = $data['result']['file_path'];
        $fileUrl = "https://api.telegram.org/file/bot".env('TELEGRAM_BOT_TOKEN')."/".$filePath;

        Storage::disk('uploads')->put(basename($fileUrl), file_get_contents($fileUrl));


        $filename = basename($fileUrl);
        Image::make(file_get_contents($fileUrl))->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();

        })->save(public_path(). '/uploads/thumbnails/' . $filename);
        $thumbnail_url = Storage::disk('uploads')->url('thumbnails/' . $filename);
        $url = Storage::disk('uploads')->url( $filename);

        $customer = $this->firstOrCreate($request,$chatId);

        $data = [
            'user_id' => 0,
            'curomer_id' => $customer->id,
            'message' => '_file',
            'thumbnail_url'=>$thumbnail_url,
            'url'=>$url,
            'self' => 1
        ];
        $this->notify($request,$customer,$data);

        return 'OK';

    }
    public function handleButtons(Request $request,$data){
        extract($this->getInfo($request));
        $this->telegram->sendMessage($chatId,'clicked: '.$data);
        return 'ok';
    }
    public function handleTextMessages(Request $request){
        extract($this->getInfo($request));
        $customer = $this->firstOrCreate($request,$chatId);
        $data = [
            'user_id' => 0,
            'curomer_id' => $customer->id,
            'message' => $message,
            'self' => 1
        ];
        $customer->notify(new UserNotifications($data));
        $customer->load('notifications');
        event(new ApplicationChat($customer, $data));
    }
    public function handle(Request $request, Telegram $telegram)
    {
        extract($this->getInfo($request));
        $dataR = json_decode($request->getContent(), true);
        Storage::disk('local')->append('json.txt', json_encode(($dataR)));

        switch (true) {
            case isset($dataR['message']['voice']):
                return $this->handleVoice($request, $dataR);
            case isset($dataR['message']['document']['file_id']):
                return $this->handleDocument($request, $dataR);
            case $photos = $request->input('message.photo'):
                return $this->handlePhoto($request, $photos);
            case $callbackQuery = $request->input('callback_query'):
                return $this->handleButtons($request,$callbackQuery['data']);
            default:
                return $this->handleTextMessages($request);
        }
        return 'Error';

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
