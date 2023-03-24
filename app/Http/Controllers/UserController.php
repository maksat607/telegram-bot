<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Notifications\UserNotifications;
use Illuminate\Http\Request;
use ProtoneMedia\Splade\SpladeTable;

class UserController extends Controller
{
    public function toggle(Customer $customer){
        $customer->active = !$customer->active;
        $customer->save();
    }
    public function delete(Customer $customer){
        $customer->delete();
    }
    public function index(){
//        for ($i =0 ; $i<5; $i++ ){
//            Customer::all()->map(function ($item){
//                $item->notify(new UserNotifications([
//                    'message'=>fake()->text(100),
//                    'self'=>rand(0,1)
//                ]));
//            });
//        }


        return view('users', [
            'users' => SpladeTable::for(User::class)
                ->column('id')
                ->column('name',sortable: true)
                ->column('email')
                ->paginate(15),
        ]);



    }

}
