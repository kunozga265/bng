<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlotResource;
use App\Http\Resources\SiteCollection;
use App\Http\Resources\SiteResource;
use App\Models\Notification;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $sites = Site::orderBy('name', 'asc')->get();
        return response()->json(new SiteCollection($sites));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function plots($id)
    {
        $site = Site::findOrFail($id);
        return response()->json(PlotResource::collection($site->plots));
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
           'name'       => 'required',
           'location'   => 'required',
           'district'   => 'required',
        ]);

        $site = Site::create([
           'name'       => $request->name,
           'location'   => $request->location,
           'district'   => $request->district,
           'map'        => $request->map,
        ]);

        Notification::create([
            'type'      => 'NEW_SITE',
            'message'   => $site->name ." Site has been added to the system."
        ]);

        return response()->json([
           'site'       => new SiteResource($site),
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
        $site = Site::findOrFail($id);
        return response()->json([
            'site'   => new SiteResource($site)
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
        $site = Site::findOrFail($id);

        $request->validate([
            'name'       => 'required',
            'location'   => 'required',
            'district'   => 'required',
        ]);

        $site->update([
            'name'       => $request->name,
            'location'   => $request->location,
            'district'   => $request->district,
            'map'        => $request->map,
        ]);

        Notification::create([
            'type'      => 'UPDATE_SITE',
            'message'   => "Detail for ". $site->name ." Site has been updated."
        ]);

        return response()->json([
            'site'   => new SiteResource($site),
            'message'   => "Successfully updated"
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
        $site = Site::findOrFail($id);

        //delete plots under this site
        foreach ($site->plots as $plot) {
            $plot->delete();
        }

        Notification::create([
            'type'      => 'REMOVE_SITE',
            'message'   => $site->name ." Site has been removed from the system."
        ]);

        $site->delete();

        return response()->json([
            'message'   => "Successfully deleted"
        ]);



    }
}
