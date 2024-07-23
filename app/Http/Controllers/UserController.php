<?php

namespace App\Http\Controllers;

use App\Models\TgUser;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $tg_users = TgUser::where('region_id','!=','')->paginate(10);
        if($request->ajax()){
            return view('dashboard.users.ajax-table', compact('tg_users'));
        }
        return view('dashboard.users.index', compact('tg_users'));
    }
}
