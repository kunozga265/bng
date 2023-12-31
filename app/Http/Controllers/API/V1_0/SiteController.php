<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlotResource;
use App\Http\Resources\SiteCollection;
use App\Http\Resources\SiteResource;
use App\Models\Notification;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Psy\Readline\Hoa\FileException;

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
           'name'           => 'required',
           'plot_width'     => 'required',
           'plot_height'    => 'required',
           'plot_price'     => 'required',
           'layout'         => 'required',
           'district'       => 'required',
        ]);

        //Upload layout
        $file=$request->layout;
        $filename="assets/files/".$request->name."-".uniqid().".pdf";

        try {
            Storage::disk('public_uploads')->put(
                $filename, file_get_contents($file)
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => "Failed to upload: $e",
            ], 501);
        }

        $site = Site::create([
            'name'           => $request->name,
            'plot_width'     => $request->plot_width,
            'plot_height'    => $request->plot_height,
            'plot_price'     => $request->plot_price,
            'location'       => $request->location,
            'district'       => $request->district,
            'layout'         => $filename,
        ]);

        $message = $site->name ." Site has been added to the system.";
        Notification::create([
            'type'      => 'NEW_SITE',
            'message'   => $message
        ]);

        (new AppController())->pushNotification("New Site", $message);

        return response()->json([
//           'site'       => new SiteResource($site),
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
            'name'           => 'required',
            'plot_width'     => 'required',
            'plot_height'    => 'required',
            'plot_price'     => 'required',
            'district'       => 'required',
        ]);

        $site->update([
            'name'           => $request->name,
            'plot_width'     => $request->plot_width,
            'plot_height'    => $request->plot_height,
            'plot_price'     => $request->plot_price,
            'location'       => $request->location,
            'district'       => $request->district,
        ]);

        //Upload layout
        if(isset($request->layout)) {
            $file = $request->layout;
            $filename = "assets/files/" . $request->name . "-" . uniqid() . ".pdf";

            try {
                Storage::disk('public_uploads')->put(
                    $filename, file_get_contents($file)
                );

                $site->update([
                    'layout' => $filename
                ]);

            } catch (\RuntimeException $e) {
                return response()->json([
                    'message' => "Site details uploaded but failed to upload layout",
                ], 501);
            }
        }

        $message = "Details for ". $site->name ." Site have been updated.";
        Notification::create([
            'type'      => 'UPDATE_SITE',
            'message'   => $message
        ]);

        (new AppController())->pushNotification("Site Update", $message);

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
        //delete bookings
        foreach ($site->bookings as $booking) {
            $booking->delete();
        }

        $message = $site->name ." Site has been removed from the system.";
        Notification::create([
            'type'      => 'REMOVE_SITE',
            'message'   => $message
        ]);

        (new AppController())->pushNotification("Site Removed", $message);

        $site->delete();

        return response()->json([
            'message'   => "Successfully deleted"
        ]);
    }

    private function getExtension($explodedImage)
    {
        $imageExtensionDecode=explode('/',$explodedImage[0]);
        $imageExtension=explode(';',$imageExtensionDecode[1]);
        $lowercaseExt=strtolower($imageExtension[0]);
        if($lowercaseExt=='jpeg')
            return 'jpg';
        else
            return $lowercaseExt;
    }
}
