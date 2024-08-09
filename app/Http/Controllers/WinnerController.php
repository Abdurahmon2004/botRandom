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

        $regionProductCombinations = collect($winn->region_ids)
            ->crossJoin($winn->product_ids)
            ->map(function($combination) {
                return ['region_id' => $combination[0], 'product_id' => $combination[1]];
            });

        $combinationsCount = $regionProductCombinations->count();
        $perCombinationCount = (int) ceil($count / $combinationsCount);

        $winners = collect();

        foreach ($regionProductCombinations as $combination) {
            $regionUsers = CodeUser::where('status',1)->where('region_id', $combination['region_id'])
                ->where('product_id', $combination['product_id'])
                ->whereNotIn('user_id', $winners->pluck('user_id')->toArray())
                ->select('user_id', 'code_id', 'region_id', 'product_id')
                ->inRandomOrder()
                ->get()
                ->unique('user_id')
                ->take($perCombinationCount);

            $winners = $winners->merge($regionUsers);
        }

        // Agar umumiy son yetarli bo'lmasa, qolgan foydalanuvchilarni qo'shish
        if ($winners->count() < $count) {
            $remainingCount = $count - $winners->count();
            $additionalUsers = CodeUser::where('status',1)->whereIn('region_id', $winn->region_ids)
                ->whereIn('product_id', $winn->product_ids)
                ->whereNotIn('user_id', $winners->pluck('user_id')->toArray())
                ->select('user_id', 'code_id', 'region_id', 'product_id')
                ->inRandomOrder()
                ->get()
                ->unique('user_id')
                ->take($remainingCount);

            $winners = $winners->merge($additionalUsers);
        }
        if(count($winners) == 0){
            return response()->json(['errorr' => 'Foydalanuvchilar topilmadi'], 404);
        }


        // Transaktsiya ichida saqlash
        \DB::transaction(function () use ($winners, $id, $winn) {
            foreach ($winners as $winner) {
                WinnerUser::create([
                    'user_id' => $winner->user_id,
                    'code_id' => $winner->code_id,
                    'winner_group_id' => $id,
                ]);
            }
            CodeUser::whereIn('region_id', $winn->region_ids)->whereIn('product_id', $winn->product_ids)->update(['status' => 0]);
        });

        return response()->json(['success' => 'Foydalanuvchilar topildi', 'users' => $winners], 200);
    }

}
