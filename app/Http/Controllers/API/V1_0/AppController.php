<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PlotResource;
use App\Http\Resources\SiteResource;
use App\Models\Booking;
use App\Models\Notification;
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

        $now = Carbon::now()->getTimestamp();
        $bookings = Booking::where('from','>=', $now)->orderBy("from","asc")->get();
        $notifications = Notification::latest()->get();

        if ($user->hasRole("administrator"))
            $plots = Plot::where('status',2)->limit(20)->get();
        else
            $plots = $user->plots()->where('status',2)->limit(20)->get();

        return response()->json([
           'sites'          => SiteResource::collection($sites),
           'plots'          => PlotResource::collection($plots),
           'bookings'       => BookingResource::collection($bookings),
           'notifications'  => NotificationResource::collection($notifications),
        ]);
    }
}
