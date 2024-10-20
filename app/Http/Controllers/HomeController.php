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
        $customers = Customer::with('notifications')->orderBy('created_at','desc')->get();
        if($request->has('search')){
            $search = $request->search;
            $customers = Customer::whereHas('notifications', function($query) use ($request) {
                $query->where('data', 'like', "%$request->search%")
                    ->orWhereNull('data');
            })->orderBy('created_at', 'desc')->get();
            return view('ajaxcontent',compact('customers','search'));
        }
        $username = $this->telegram->username();


        $phone = '';
        return view('home',compact('customers','username','phone'));
    }
}
