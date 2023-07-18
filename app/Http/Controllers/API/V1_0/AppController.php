<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\PlotResource;
use App\Http\Resources\SiteResource;
use App\Models\Booking;
use App\Models\Plot;
use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = User::find(Auth::id());

        $sites = Site::orderBy('name', 'asc')->get();
        $plots = Plot::where('status',2)->limit(20)->get();

        $now = Carbon::now()->getTimestamp();
        if ($user->hasRole("administrator"))
            $bookings = Booking::where('from','>=', $now)->orderBy("from","asc")->get();
        else
            $bookings = $user->bookings()->where('from','>=', $now)->orderBy("from","asc")->get();

        return response()->json([
           'sites'      => SiteResource::collection($sites),
           'plots'      => PlotResource::collection($plots),
           'bookings'   => BookingResource::collection($bookings)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
