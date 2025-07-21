<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Exception;

class TelegramWebhookController extends Controller
{
    public function index()
    {
        return view('telegram.webhook');
    }

    public function setWebhook(Request $request)
    {
        try {
            $webhookUrl = $request->input('webhook_url', route('telegram-webhook'));

            $response = Telegram::setWebhook([
                'url' => $webhookUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook set successfully',
                'data' => $response
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting webhook: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getWebhook()
    {
        try {
            $response = Telegram::getWebhookInfo();

            return response()->json([
                'success' => true,
                'data' => $response
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting webhook info: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteWebhook()
    {
        try {
            $response = Telegram::removeWebhook();

            return response()->json([
                'success' => true,
                'message' => 'Webhook deleted successfully',
                'data' => $response
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting webhook: ' . $e->getMessage()
            ], 500);
        }
    }

}
