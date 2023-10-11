<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingCollection;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Notification;
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
           'user_id'    => 'required',
           'name'       => 'required',
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
            'name'       => $request->name,
            'user_id'    => $request->user_id,
            'site_id'    => $site->id,
        ]);

        $message = $booking->user->first_name." ". $booking->user->last_name . " has scheduled a visit at "
            .$site->name." on "
            .date("jS F Y", $from)
            ." from "
            .Carbon::createFromTimestamp($from,'Africa/Lusaka')->format('H:i')
            ." to "
            .Carbon::createFromTimestamp($to,'Africa/Lusaka')->format('H:i')
            .".";

        Notification::create([
            'type'      => 'NEW_BOOKING',
            'message'   => $message
        ]);

        (new AppController())->pushNotification("New Scheduled Visit", $message);

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

        $message = $booking->user->first_name." ". $booking->user->last_name . " has cancelled their scheduled a visit at "
            .$booking->site->name." on "
            .date("jS F Y", $booking->from)
            ." from"
            .Carbon::createFromTimestamp($booking->from,'Africa/Lusaka')->format('H:i')
            ." to "
            .Carbon::createFromTimestamp($booking->to,'Africa/Lusaka')->format('H:i')
            .".";
        Notification::create([
            'type'      => 'REMOVE_BOOKING',
            'message'   => $message
        ]);

        (new AppController())->pushNotification("Scheduled Visit Cancelled", $message);

        $booking->delete();

        return response()->json([
            'message' => "Successfully deleted"
        ]);
    }
}
