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
use GuzzleHttp\Client;
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

    function pushNotification($subject,$message){
        //notification
        try{
            $client=new Client();
//            $to=str_replace(' ','',$title);
            $notificationRequest=$client->request('POST','https://fcm.googleapis.com/fcm/send',[
                'headers'=>[
                    'Authorization' => 'key=AAAA_9k1hjA:APA91bEXDacyaUlPTbMKLXhJj4FQu2Ml9h873jJbXJ7K9L0FUKfLcgPf-2nPL2y2E3vVuFHwnIvAHMk8OreBX-xwVEuyN4HgQb_OSN0qixOLBBX2mxMG6aiIUykUW7FNCj6YoA6v25jj',
                    'Content-Type'   =>  'application/json',
                ],
                'json'=>[
                    "priority"=>"high",
                    "content_available"=>true,
                    "to"=>"/topics/general",
                    "notification"=>[
                        "title"=>$subject,
                        "body"=>$message
                    ]
                ]
            ]);

            // Develop a use for this
            if ($notificationRequest->getStatusCode()==200){}


        }catch (\GuzzleHttp\Exception\GuzzleException $e){
            //Log information
        }
    }
}
