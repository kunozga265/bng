<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlotResource;
use App\Models\Notification;
use App\Models\Plot;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlotController extends Controller
{
    const AVAILABLE_STATUS = 1;
    const NEGOTIATING_STATUS = 2;
    const SOLD_STATUS = 3;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexBySite($id)
    {
        $site = Site::findOrFail($id);
        return response()->json([
           'plots'  => PlotResource::collection($site->plots)
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexByUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
           'plots'  => PlotResource::collection($user->plots)
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
           'name'           => 'required',
           'site_id'        => 'required',
           'hectare'        => 'required',
//           'coordinates'    => 'required',
        ]);

        $plot = Plot::create([
            'name'          => $request->name,
            'hectare'       => $request->hectare,
            'status'        => 1,
            'site_id'       => $request->site_id,
        ]);

        return response()->json([
//           'plot'       => new PlotResource($plot),
           'message'    => "Successfully added"
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function negotiate(Request $request, $id)
    {
        $plot = Plot::findOrFail($id);

        $plot->update([
            'status'        => self::NEGOTIATING_STATUS,
            'user_id'       => Auth::id(),
        ]);

        Notification::create([
            'type'      => 'PLOT_NEGOTIATE',
            'message'   => $plot->user->first_name." ".$plot->user->last_name ." is currently negotiating for "
                .$plot->name. " under " .$plot->site->name
        ]);

        return response()->json([
//            'plot'       => new PlotResource($plot),
            'message'    => "Successfully updated. Plot under negotiation."
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelNegotiation(Request $request, $id)
    {
        $plot = Plot::findOrFail($id);

        $plot->update([
            'status'        => self::AVAILABLE_STATUS,
            'user_id'       => null,
        ]);

        Notification::create([
            'type'      => 'PLOT_CANCEL_NEGOTIATION',
            'message'   => "Negotiations for "
                .$plot->name. " under " .$plot->site->name. " have been cancelled."
        ]);

        return response()->json([
//            'plot'       => new PlotResource($plot),
            'message'    => "Successfully updated. Plot Available."
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sell(Request $request, $id)
    {
        $plot = Plot::findOrFail($id);
        $request->validate([
           'user_id'    => 'required'
        ]);

        $plot->update([
            'status'        => self::SOLD_STATUS,
            'user_id'       => $request->user_id,
        ]);

        Notification::create([
            'type'      => 'PLOT_SELL',
            'message'   => $plot->name. " under " .$plot->site->name. " has been sold by ". $plot->user->first_name. " " .$plot->user->last_name
        ]);

        return response()->json([
//            'plot'       => new PlotResource($plot),
            'message'    => "Successfully updated to sold."
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $plot = Plot::findOrFail($id);
        return response()->json([
            'plot'       => new PlotResource($plot),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $plot = Plot::findOrFail($id);

        $request->validate([
            'name'           => 'required',
            'hectare'        => 'required',
        ]);

        $plot->update([
            'name'          => $request->name,
            'hectare'       => $request->hectare,
        ]);

        return response()->json([
//            'plot'       => new PlotResource($plot),
            'message'    => "Successfully updated"
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $plot=Plot::findOrFail($id);

        Notification::create([
            'type'      => 'PLOT_DELETE',
            'message'   => $plot->name. " under " .$plot->site->name. " has been removed"
        ]);

        $plot->delete();
        return response()->json([
            'message'    => "Successfully deleted"
        ]);

    }
}
