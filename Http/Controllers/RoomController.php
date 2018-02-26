<?php

namespace App\Http\Controllers;


use App\Room;
use App\Transformers\RoomTransformer;
use Illuminate\Http\Request;

class RoomController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::all();

        return view('rooms.rooms-list', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Room::create([
            'room_number' => $request->room_number,
            'floor'       => $request->floor,
            'capacity'    => $request->capacity,
            'rent'        => $request->rent,
            'length'      => $request->length,
            'width'       => $request->width
        ]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        $room->room_number = $request->room_number;
        $room->capacity = $request->capacity;
        $room->rent = $request->rent;
        $room->width = $request->width;
        $room->floor = $request->floor;

        $room->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $room = Room::find($id);

        $room->delete();

        return redirect()->back();
    }
}
