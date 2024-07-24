<?php

namespace App\Http\Controllers;

use App\Models\CodeUser;
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
        return view('dashboard.winners.index', compact('winnerGroups'));
    }

    public function store(Request $request)
    {
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
            return view('dashboard.winners.user.ajax-table', compact('users','id'))->render();
        }
        return view('dashboard.winners.user.index', compact('users', 'id'));
    }


    public function saveWinners(Request $request, $id)
    {
        try {
            $count = $request->input('count', 5); // Default count is 5 if not provided

        // Barcha region_id larni olish
        $regions = CodeUser::where('status',1)->select('region_id')->distinct()->pluck('region_id');
        if($regions->count() == 0){
            return response()->json(['error' => 'Foydalanuvchi yoq'], 404);
        }
        // G'oliblar ro'yxatini saqlash uchun bo'sh array yaratish
        $winners = collect();

        // Har bir region uchun foydalanuvchilarni to'plash
        $regionUsers = [];
        foreach ($regions as $region) {
            $regionUsers[$region] = CodeUser::where('region_id', $region)
                ->select('user_id', 'code_id', 'region_id')
                ->distinct()
                ->inRandomOrder()
                ->get();
        }

        // Teng taqsimlash uchun har bir regiondan $count/$regions->count() miqdorda foydalanuvchilarni olish
        $perRegionCount = (int) ceil($count / $regions->count());
        foreach ($regionUsers as $users) {
            foreach ($users as $user) {
                if (!$winners->contains('user_id', $user->user_id)) {
                    $winners->push($user);
                    if ($winners->count() >= $perRegionCount) {
                        break;
                    }

                }
            }
        }

        // Agar g'oliblar soni yetarli bo'lmasa qolgan foydalanuvchilarni tanlash
        if ($winners->count() < $count) {
            foreach ($regionUsers as $users) {
                foreach ($users as $user) {
                    if (!$winners->contains('user_id', $user->user_id)) {
                        $winners->push($user);
                        if ($winners->count() >= $count) {
                            break 2;
                        }

                    }
                }
            }
        }

        // G'oliblarni WinnerUser jadvaliga saqlash
        foreach ($winners as $winner) {
            WinnerUser::create([
                'winner_group_id' => $id,
                'user_id' => $winner->user_id,
                'code_id' => $winner->code_id,
                'region_id' => $winner->region_id,
            ]);
        }
        CodeUser::query()->update(['status' => 0]);
        return response()->json(['success' => 'Winners saved successfully'], 200);
        } catch (\Throwable $th) {
         return response()->json(['error' => 'Foydalanuvchi yoq'], 404);
        }
    }
}
