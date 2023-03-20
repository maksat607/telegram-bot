<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
    public function index()
    {
//        $customers = Customer::with('notifications')->get();
//        foreach ($customers as $customer){
//            $htmlRender =+ view('left', compact('fullname','short','time','unread',))->render();
//        }
        $customers = Customer::with('notifications')->get();
        return view('home',compact('customers'));
    }
}
