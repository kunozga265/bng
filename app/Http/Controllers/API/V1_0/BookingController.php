<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingCollection;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $now = Carbon::now()->getTimestamp();
        $bookings = Booking::where('from','>=', $now)->orderBy("from","asc")->get();
        return response()->json( new BookingCollection($bookings));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexBySite($id)
    {
        $site = Site::findOrFail($id);
        $now = Carbon::now()->getTimestamp();
        $bookings = $site->bookings()->where('from','>=', $now)->orderBy("from","asc")->get();
        return response()->json( new BookingCollection($bookings));
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
           'from'       => 'required',
           'to'         => 'required',
           'site_id'    => 'required',
        ]);

        $site = Site::findOrFail($request->site_id);

        $from = intval($request->from);
        $to = intval($request->to);

        //fail if to is less than from
        if ($from > $to){
            return response()->json([
                'message' => "The time period is invalid"
            ],400);
        }
        /*elseif ($site->bookings()->where('from','>=',$from)->where('to','<',$from)->exists()){
            return response()->json([
                'message' => "This time period is already booked"
            ],400);
        }*/

        $booking = Booking::create([
            'from'       => $from,
            'to'         => $to,
            'user_id'    => Auth::id(),
            'site_id'    => $site->id,
        ]);

        return response()->json([
//            'booking' => new BookingResource($booking),
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
        $user = User::find(Auth::id());
        $booking = $user->bookings()->findOrFail($id);
//        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json([
            'message' => "Successfully deleted"
        ]);
    }
}
