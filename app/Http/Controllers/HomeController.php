<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Notification;
use App\Services\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(public Telegram $telegram)
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->withCount([
                'notifications as has_unread' => function ($query) {
                    $query->whereNull('read_at'); // Count only unread notifications
                }
            ])
            ->with(['notifications' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Order notifications by created_at
            }])
            ->orderByDesc('has_unread') // Prioritize customers with unread notifications
            ->orderByDesc(
                Notification::selectRaw('CASE WHEN read_at IS NULL THEN created_at ELSE read_at END')
                    ->whereColumn('notifiable_id', 'customers.id')
                    ->where('notifiable_type', Customer::class)
                    ->latest()
                    ->take(1)
            ) // Sort by `created_at` for unread, `read_at` for read
            ->orderByDesc('customers.created_at') // Fallback to customer creation date
            ->get();




        if($request->has('search')){
            $search = $request->search;
            $customers = Customer::whereHas('notifications', function($query) use ($request) {
                $query->where('data', 'like', "%$request->search%")
                    ->orWhereNull('data');
            })->orderBy('created_at', 'asc')->get();
            return view('ajaxcontent',compact('customers','search'));
        }
        $username = $this->telegram->username();


        $phone = '';
        return view('home',compact('customers','username','phone'));
    }
}
