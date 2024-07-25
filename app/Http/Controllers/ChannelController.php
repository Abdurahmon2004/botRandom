<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index(Request $request)
    {
        $channels = Channel::all();
        if($request->ajax()){
            return view('dashboard.channels.ajax-table', compact('channels'))->render();
        }
        return view('dashboard.channels.index', compact('channels'));
    }

    public function store(Request $request)
    {
       if(Channel::all()->count() == 0){
        $request->validate([
            'channel' => 'required|string',
        ]);

        Channel::create($request->all());
       }

        return response()->json(['success' => 'Channel added successfully']);
    }

    public function update(Request $request, Channel $channel)
    {
        $request->validate([
            'channel' => 'required|string',
        ]);

        $channel->update($request->all());

        return response()->json(['success' => 'Channel updated successfully']);
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();
        return response()->json(['success' => 'Channel deleted successfully']);
    }
}
