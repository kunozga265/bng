<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AppController extends Controller
{
    public function isApi(Request $request)
    {
        //get cookie object
        $CSRF_TOKEN=$request->cookie();
        return count($CSRF_TOKEN) == 0;
    }

    public function getAuthUser(Request $request)
    {
        if ($this->isApi($request)) {
            //API User
            $requestToken=substr($request->server('HTTP_AUTHORIZATION'),7);

            if ($requestToken) {
                $token = PersonalAccessToken::findToken($requestToken);
                return $token->tokenable;
            }else
                return null;
        }
        else{
            return User::find(Auth::id());
        }

    }
}
