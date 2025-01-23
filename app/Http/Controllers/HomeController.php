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
            ->leftJoin('notifications', 'customers.id', '=', 'notifications.customer_id')
            ->select('customers.*')
            ->selectRaw('MAX(CASE WHEN notifications.read_at IS NULL THEN 1 ELSE 0 END) as has_unread')
            ->selectRaw('MAX(notifications.created_at) as latest_notification')
            ->groupBy('customers.id')
            ->orderByDesc('has_unread') // Unread notifications first
            ->orderByDesc('latest_notification') // Most recent notification next
            ->orderByDesc('customers.created_at') // Fallback to customer creation date
            ->with('notifications') // Eager load notifications
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
