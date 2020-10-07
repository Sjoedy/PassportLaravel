<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function AdminLogin(Request $request){
        $http = new \GuzzleHttp\Client();
        try {
            $response = $http->post(config('services.passport_password.password_grant_uri'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport_password.password_grant_client_id'),
                    'client_secret' => config('services.passport_password.password_grant_client_secret'),
                    'username' => $request->email,
                    'password' => $request->password,
                ]
            ]);
        } catch (\GuzzleHttp\Exception\BadResponeException $e) {
            if ($e->getCode() == 400) {
                return response()->json('Invalid Request. Please enter a username or password', $e->getCode());
            } else if ($e->getCode() == 401) {
                return response()->json('Your Credentials are incorrect. Please try again', $e->getCode());
            }
            return response()->json('Something went wrong on the serve', $e->getCode());
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getCode() == 400) {
                return response()->json('Invalid Request. Please enter a username or password', $e->getCode());
            } else if ($e->getCode() == 401) {
                return response()->json(['errors' => 'ກະລຸນາກວດສອບລະຫັດຜ່ານກ່ອນ...'], $e->getCode());
            }
            return response()->json('Something went wrong on the serve', $e->getCode());
        }
        $data = json_decode((string)$response->getBody(), true);

        $data['user'] = User::where('email', $request->email)->first();
        $data['success'] = true;
        return $data;
    }

    public function Listusers(){
        return Auth::guard('api')->user();
    }
}
