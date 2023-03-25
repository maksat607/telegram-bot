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
    public function uploadAudio(Request $request, Customer $customer, Telegram $telegram) {
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
            $customer->notify(new UserNotifications($data));
            $customer->load('notifications');
            event(new ApplicationChat($customer, $data));

            $telegram->sendVoice($customer->telegram_id,public_path('uploads').'/'.basename($url));

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
    /**
     * Handle incoming Telegram messages.
     *
     * @param Request $request
     * @return string
     */
    public function handle(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        Storage::disk('local')->append('json.txt', json_encode($data));

        if (isset($data['message']['voice'])) {
            $this->handleVoiceMessage($data);
        }

        if (isset($data['message']['document']['file_id'])) {
            $this->handleDocumentMessage($data);
        }

        return 'OK';
    }

    /**
     * Handle incoming voice messages.
     *
     * @param array $data
     */
    protected function handleVoiceMessage(array $data)
    {
        $voice = $data['message']['voice'];
        $file_id = $voice['file_id'];
        $file_size = $voice['file_size'];

        $file_path = $this->downloadTelegramFile($file_id);

        if ($file_path) {
            // Save the voice recording to disk
            $destination_path = storage_path("app/voice/{$file_id}.ogg");
            Storage::disk('uploads')->put(basename($file_path), file_get_contents($file_path));
            $url = Storage::disk('uploads')->url(basename($file_path));

            $path_parts = pathinfo($file_path);
            $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/voice.png');

            if (file_exists(public_path('uploads').'/thumbnails/'.$path_parts['extension'].'.svg')) {
                $thumbnail_url = Storage::disk('uploads')->url('/thumbnails/'.$path_parts['extension'].'.svg');
            }

            $customer = $this->getOrCreateCustomer($data);
            $this->sendUserNotification($customer, '_file', $thumbnail_url, $url);
        }
    }

    /**
     * Handle incoming document messages.
     *
     * @param array $data
     */
    protected function handleDocumentMessage(array $data)
    {
        $fileId = $data['message']['document']['file_id'];

        Storage::disk('local')->append('file_id.txt', $fileId);

        $file_path = $this->downloadTelegramFile($fileId);

        if ($file_path) {
            $fileUrl = "https://api.telegram.org/file/bot".env('TELEGRAM_BOT_TOKEN')."/".$file_path;

            Storage::disk('uploads')->put(basename($fileUrl), file_get_contents($fileUrl));

            $url = Storage::disk('uploads')->url(basename($fileUrl));

            $customer = $this->getOrCreateCustomer($data);
            $this->sendUserNotification($customer, '_file', '', $url);
        }
    }

    /**
     * Download a file from Telegram using its file ID.
     *
     * @param string $fileId
     * @return string|null
     */
    /**
     * Download the file with the given Telegram file ID.
     *
     * @param string $fileId
     * @return string|null
     */
    protected function downloadTelegramFile(string $fileId): ?string
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        // Get the file information from Telegram API
        $response = file_get_contents("https://api.telegram.org/bot{$botToken}/getFile?file_id={$fileId}");
        $data = json_decode($response, true);

        if (!isset($data['result']['file_path'])) {
            return null;
        }

        // Build the URL to download the file from Telegram
        $filePath = $data['result']['file_path'];
        $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";

        // Download the file content and save it to disk
        $fileContent = file_get_contents($fileUrl);

        if (!$fileContent) {
            return null;
        }

        $fileName = basename($fileUrl);
        Storage::disk('uploads')->put($fileName, $fileContent);

        return $fileName;
    }
    /**
     * Get or create a customer based on their Telegram user information.
     *
     * @param array $data
     * @return Customer
     */
    protected function getOrCreateCustomer(array $data): Customer
    {
        $chat_id = $data['message']['chat']['id'];
        $first_name = $data['message']['chat']['first_name'] ?? '';
        $last_name = $data['message']['chat']['last_name'] ?? '';
        $username = $data['message']['chat']['username'] ?? '';

        $customer = Customer::where('telegram_chat_id', $chat_id)->first();
        if (!$customer) {
            $customer = new Customer();
            $customer->telegram_chat_id = $chat_id;
            $customer->name = trim("{$first_name} {$last_name}");
            $customer->email = "{$username}@telegram.com";
            $customer->save();
        }

        return $customer;
    }

    /**
     * Send a notification to the customer.
     *
     * @param Customer $customer
     * @param string $type
     * @param string|null $thumbnail_url
     * @param string|null $url
     */
    protected function sendUserNotification(Customer $customer, string $type, ?string $thumbnail_url, ?string $url)
    {
        $message = [
            'type' => $type,
            'thumbnail_url' => $thumbnail_url,
            'url' => $url,
        ];
        $customer->notify(new UserNotifications($message));

        // Send a message to our application chat notifying us of the new message
        $telegram = new Telegram();
        $message_text = "New message from {$customer->name}: {$type}";
        $telegram->sendMessage(env('TELEGRAM_APPLICATION_CHAT_ID'), $message_text);
        event(new ApplicationChat($message_text));
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






}
