<?php

namespace App\Http\Controllers\API\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Rules\Password;

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
                'email' => ['The provided credentials are incorrect.'],
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
            "first_name"     => ['required','string', 'max:255'],
            "last_name"      => ['required','string', 'max:255'],
            "email"         => ['required','unique:users','email','string'],
            "password"      => ['required', 'confirmed', new Password, 'string'],
            'national_id'   => ['required','unique:users'],
            'role_id'       => ['required'],
            'phone_number'  => ['required', 'unique:users'],
        ]);

        $user=User::create([
            "first_name"     => ucwords($request->first_name),
            "middle_name"    => ucwords($request->middle_name),
            "last_name"      => ucwords($request->last_name),
            "email"         => $request->email,
            "phone_number"  => $request->phone_number,
            "password"      => bcrypt($request->password),
            "national_id"   => $request->national_id,
            "role_id"       => $request->role_id,
        ]);

//        $token=$user->createToken($request->device_name)->plainTextToken;
        //Email new user with credentials

        return response()->json([
//            'user'  =>  new UserResource($user),
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $user = User::find(Auth::id());
        $request->validate([
            "password"          => ['required', 'confirmed', new Password, 'string'],
            "password_current"  => ['required', 'string'],
        ]);

        if(Auth::guard('web')->attempt([
            'email' => $user->email,
            'password' => $request->password_current
        ])){
            $user->update([
                "password"      => bcrypt($request->password),
            ]);

            return response()->json([
                "message" => "Password successfully updated."
            ]);

        }else{
            return response()->json([
                "message" => "Current password provided is incorrect."
            ], 400);
        }




    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = User::find(Auth::id());
        $request->validate([
            "first_name"     => ['required','string', 'max:255'],
            "last_name"      => ['required','string', 'max:255'],
            "email"         => ['required','email','string', Rule::unique('users')->ignore(Auth::id())],
            'national_id'   => ['required', Rule::unique('users')->ignore(Auth::id())],
            'phone_number'  => ['required', Rule::unique('users')->ignore(Auth::id())],
        ]);

        $user->update([
            "first_name"     => ucwords($request->first_name),
            "middle_name"    => ucwords($request->middle_name),
            "last_name"      => ucwords($request->last_name),
            "email"         => $request->email,
            "phone_number"  => $request->phone_number,
            "national_id"   => $request->national_id,
        ]);

        return response()->json([]);
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
