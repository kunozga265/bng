<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingCollection;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Site;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexBySite($id)
    {
        $site = Site::findOrFail($id);
        return response()->json([
           'bookings'   => new BookingCollection($site->bookings)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
           'date'       => 'required',
           'from'       => 'required',
           'to'         => 'required',
           'user_id'    => 'required',
           'site_id'    => 'required',
        ]);

        $booking = Booking::create([
            'date'       => $request->date,
            'from'       => $request->from,
            'to'         => $request->to,
            'user_id'    => $request->user_id,
            'site_id'    => $request->site_id,
        ]);

        return response()->json([
            'booking' => new BookingResource($booking),
            'message' => "Successfully booked"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json([
            'message' => "Successfully deleted"
        ]);
    }
}
