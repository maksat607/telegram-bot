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
                $query->orderBy('read_at', 'asc') // Unread first (NULL values first)
                ->orderBy('created_at', 'desc'); // Most recent notifications next
            }])
            ->orderByDesc(
                Notification::selectRaw('MAX(created_at)')
                    ->whereColumn('notifiable_id', 'customers.id')
                    ->where('notifiable_type', Customer::class)
            ) // Sort by the most recent notification's created_at
            ->orderByDesc('has_unread') // Sort by whether the customer has unread notifications
            ->orderByDesc('customers.created_at') // Fallback to the customer's creation date
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
