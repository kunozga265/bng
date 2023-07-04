<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            "email"         => ['required'],
            "password"      => ['required'],
            'device_name'   => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'The provided credentials are incorrect.',
            ]);
        }
        $token=$user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'user'  =>  new UserResource($user),
            'token' =>  $token
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request)
    {
        $request->validate([
            "firstName"     => ['required','string', 'max:255'],
            "lastName"      => ['required','string', 'max:255'],
            "email"         => ['required','unique:users','email','string'],
            "password"      => ['required', 'confirmed', new \Laravel\Fortify\Rules\Password, 'string'],
            'national_id'   => ['required'],
            'role_id'       => ['required'],
            'phone_number'  => ['required', 'unique:users'],
            'device_name'   => ['required'],
        ]);

        $user=User::create([
            "first_name"     => ucwords($request->firstName),
            "middle_name"    => ucwords($request->middleName),
            "last_name"      => ucwords($request->lastName),
            "email"         => $request->email,
            "phone_number"  => $request->phone_number,
            "password"      => bcrypt($request->password),
            "national_id"   => $request->national_id,
            "role_id"       => $request->role_id,
        ]);

//        $token=$user->createToken($request->device_name)->plainTextToken;
        //Email new user with credentials

        return response()->json([
            'user'  =>  new UserResource($user),
//            'token' =>  $token
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
