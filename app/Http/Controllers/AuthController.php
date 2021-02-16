<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::whereEmail($request->email)->first();


        if (!is_null($user) && Hash::check($request->password, $user->password)) {

            $token = $user->createToken('glassy-backend')->accessToken;

            return response()->json([
                'res' => true,
                'token' => $token,
                'message' => 'Bienvenido al sistema'
            ], 200);
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Email o contraseña son incorrectos'
            ], 401);
        }
    }

    public function signup(SignUpRequest $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($request->password);

        User::create($input);

        return $this->login($request);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->tokens->each(function($token, $key){
            $token->delete();
        });

        return response()->json([
            'res' => true,
            'message' => 'Logout exitoso'
        ], 200);
    }
}
