<?php

namespace App\Http\Controllers;

use App\Exports\CodesExport;
use App\Models\Code;
use App\Models\Group;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groups = Group::all();
        $products = Product::where('status', 1)->get();
        if ($request->ajax()) {
            return view('dashboard.group.ajax-table', compact('groups'));
        }
        return view('dashboard.group.index', compact('groups', 'products'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'product_id' => 'required',
            'code_count' => 'required|integer',
        ]);

        $group = Group::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'status' => $request->status ?? 0,
        ]);

        $count = $request->code_count;
        $codes = [];

        // Barcha mavjud kodlarni olish va arrayga joylash
        $existingCodes = Code::pluck('code')->toArray();

        // Yagona unikal kodlarni yaratish
        while (count($codes) < $count) {
            $code = Str::random(8);
            if (!in_array($code, $existingCodes) && !in_array($code, array_column($codes, 'code'))) {
                $codes[] = [
                    'group_id' => $group->id,
                    'code' => $code,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Massaviy yaroqlilikni o'chirish
        DB::beginTransaction();
        try {
            foreach (array_chunk($codes, 1000) as $chunk) {
                Code::insert($chunk);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error inserting codes: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Product added successfully']);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $product = Group::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'status' => $request->status ?? 0,
        ]);
        return response()->json(['success' => 'Product updated successfully']);
    }
    public function codesAdd(Request $request)
    {
        $request->validate([
            'group_id' => 'required',
            'code_count' => 'required',
        ]);

        $count = $request->code_count;
        $codes = [];

        // Barcha mavjud kodlarni olish va arrayga joylash
        $existingCodes = Code::pluck('code')->toArray();

        // Yagona unikal kodlarni yaratish
        while (count($codes) < $count) {
            $code = Str::random(8);
            if (!in_array($code, $existingCodes) && !in_array($code, array_column($codes, 'code'))) {
                $codes[] = [
                    'group_id' => $request->group_id,
                    'code' => $code,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Massaviy yaroqlilikni o'chirish
        DB::beginTransaction();
        try {
            foreach (array_chunk($codes, 1000) as $chunk) {
                Code::insert($chunk);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error inserting codes: ' . $e->getMessage()], 500);
        }
        return response()->json(['success' => 'Product updated successfully']);
    }

    public function export($groupId)
    {
        $name = Group::find($groupId)->name;
        return Excel::download(new CodesExport($groupId), $name.'.xlsx');
    }
}
