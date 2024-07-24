<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\CodeUser;
use App\Models\Product;
use App\Models\Region;
use App\Models\TgUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request){
        if(isset($request->from)){
            $startDate = Carbon::parse($request->from)->startOfDay();
            $endDate = Carbon::parse($request->to)->endOfDay();

            $users = TgUser::whereBetween('created_at', [$startDate, $endDate])->get()->count()??0;

            $codeUsers = CodeUser::distinct('user_id')->whereBetween('created_at', [$startDate, $endDate])->count('user_id');

            $codesActive = Code::where('status',0)->whereBetween('created_at', [$startDate, $endDate])->get()->count()??0;

            $codes = Code::all()->count()??0;
            $regions = Region::all()->count()??0;
            $regionsActive = Region::where('status',1)->get()->count()??0;
            $products = Product::where('status',1)->get()->count()??0;
            return view('dashboard.dashboard',compact('users','codes','codesActive','regions','products','codeUsers','regionsActive'));
        }
        $users = TgUser::where('region_id','!=','')->get()->count()??0;
        $codeUsers = CodeUser::distinct('user_id')->count('user_id')??0;
        $codesActive = Code::where('status',0)->get()->count()??0;
        $codes = Code::all()->count()??0;
        $regions = Region::all()->count()??0;
        $regionsActive = Region::where('status',1)->get()->count()??0;
        $products = Product::all()->count()??0;
        $productsActive = Product::where('status',1)->count()??0;
        return view('dashboard.dashboard',compact('users','codes','codesActive','regions','products','productsActive','codeUsers','regionsActive'));
    }
}
