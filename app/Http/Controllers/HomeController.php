<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $customers = Customer::with('notifications')->orderBy('created_at','desc')->get();
        if ($request->has('search')){
            $notifications = Notification::where('data','like',"%{$request->get('search')}%")->get();
        }
        return view('home',compact('customers'));
    }
}
