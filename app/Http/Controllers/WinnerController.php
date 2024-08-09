<?php

namespace App\Http\Controllers;

use App\Models\CodeUser;
use App\Models\Product;
use App\Models\Region;
use App\Models\WinnerGroup;
use App\Models\WinnerUser;
use Illuminate\Http\Request;

class WinnerController extends Controller
{
    public function index(Request $request)
    {
        $winnerGroups = WinnerGroup::all();
        if ($request->ajax()) {
            return view('dashboard.winners.ajax-table', compact('winnerGroups'))->render();
        }
        $regions = Region::all();
        $products = Product::all();
        return view('dashboard.winners.index', compact('winnerGroups', 'regions', 'products'));
    }

    public function store(Request $request)
    {
        // return dd($request);
        $winnerGroup = WinnerGroup::create($request->all());
        return response()->json($winnerGroup);
    }

    public function update(Request $request, $id)
    {
        $winnerGroup = WinnerGroup::findOrFail($id);
        $winnerGroup->update($request->all());
        return response()->json($winnerGroup);
    }
    public function userIndex(Request $request, $id)
    {
        $users = WinnerUser::where('winner_group_id', $id)->get();
        if ($request->ajax()) {
            return view('dashboard.winners.user.ajax-table', compact('users', 'id'))->render();
        }
        return view('dashboard.winners.user.index', compact('users', 'id'));
    }

    public function saveWinners(Request $request, $id)
    {
        $count = $request->input('count', 5); // Default count is 5 if not provided

        // WinnerGroup ni topish
        $winn = WinnerGroup::find($id);
        if (!$winn) {
            return response()->json(['error' => 'WinnerGroup topilmadi'], 404);
        }

        // Har bir region uchun ajratib olish kerak bo'lgan foydalanuvchilar soni
        $regionsCount = count($winn->region_ids);
        $perRegionCount = (int) ceil($count / $regionsCount);

        $winners = collect();

        foreach ($winn->region_ids as $region) {
            // Oldindan tanlangan foydalanuvchilarni istisno qilish
            $regionUsers = CodeUser::where('region_id', $region)
                ->whereIn('product_id', $winn->product_ids)
                ->whereNotIn('user_id', $winners->pluck('user_id')->toArray())
                ->select('user_id', 'code_id', 'region_id')
                ->inRandomOrder()
                ->get()
                ->unique('user_id')
                ->take($perRegionCount);

            if ($regionUsers->isEmpty()) {
                return response()->json(['error' => "Region ID $region uchun foydalanuvchi topilmadi"], 404);
            }

            $winners = $winners->merge($regionUsers);
        }

        // Agar umumiy son yetarli bo'lmasa, qolgan foydalanuvchilarni qo'shish
        if ($winners->count() < $count) {
            $additionalUsers = CodeUser::whereIn('region_id', $winn->region_ids)
                ->whereIn('product_id', $winn->product_ids)
                ->whereNotIn('user_id', $winners->pluck('user_id')->toArray())
                ->select('user_id', 'code_id', 'region_id')
                ->inRandomOrder()
                ->get()
                ->unique('user_id')
                ->take($perRegionCount);

            $winners = $winners->merge($additionalUsers);
        }

        return response()->json(['success' => 'Foydalanuvchilar topildi', 'users' => $winners], 200);
    }

}
