<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $regions = Region::orderBy('id','desc')->paginate(10);
        if($request->ajax()){
            return view('dashboard.regions.ajax-table', compact('regions'))->render();

        }
        return view('dashboard.regions.index', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        Region::create([
            'name'=>$request->name,
            'status'=>$request->status??0
        ]);
        return response()->json(['success' => 'Region added successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $region = Region::findOrFail($id);
        $region->update([
            'name'=>$request->name,
            'status'=>$request->status??0
        ]);
        return response()->json(['success' => 'Region updated successfully']);
    }

}
