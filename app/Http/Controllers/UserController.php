<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\CodeUser;
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
    public function CodeUser(Request $request)
    {
        $users = CodeUser::paginate(20);
        if($request->ajax()){
            return view('dashboard.usersCodes.ajax-table', compact('users'));
        }
        return view('dashboard.usersCodes.index', compact('users'));
    }
    public function deleteCode($id){
        $code = CodeUser::find($id);
        Code::where('id',$code->code_id)->first()->update([
            'status'=>1
        ]);
        $code->delete();
        return response()->json(['success','deleted']);
    }
}
