<?php

namespace App\Http\Controllers;

use App\Models\WinnerGroup;
use Illuminate\Http\Request;

class WinnerController extends Controller
{
    public function index()
    {
        $winnerGroups = WinnerGroup::all();
        return view('dashboard.winner_groups.index', compact('winnerGroups'));
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
}
