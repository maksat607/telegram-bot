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
        $customers = Customer::with(['notifications' => function ($query) {
            $query->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
                ->orderBy('created_at', 'desc');
        }])
            ->orderBy('created_at', 'desc') // Sorting customers by their own creation date
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
