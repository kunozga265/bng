<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlotResource;
use App\Models\Plot;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;

class PlotController extends Controller
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
//           'coordinates'    => 'required',
        ]);

        $plot = Plot::create([
            'name'          => $request->name,
            'status'        => 0,
            'site_id'       => $request->site_id,
            'coordinates'   => $request->coordinates,
        ]);

        return response()->json([
           'plot'       => new PlotResource($plot),
           'message'    => "Successfully added"
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
            'site_id'        => 'required',
            'status'         => 'required',
//           'coordinates'    => 'required',
        ]);

        $plot->update([
            'name'          => $request->name,
            'status'        => $request->status,
            'site_id'       => $request->site_id,
            'coordinates'   => $request->coordinates,
        ]);

        return response()->json([
            'plot'       => new PlotResource($plot),
            'message'    => "Successfully update"
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
        $plot->delete();
        return response()->json([
            'message'    => "Successfully deleted"
        ]);

    }
}
