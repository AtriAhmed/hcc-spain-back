<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function userRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|min:8',
        ], [
            'name.required' => 'Le champ Nom est obligatoire.',
            'name.max' => 'La longueur du nom est trop longue. La longueur maximale est de 191.',
            'email.required' => 'Le champ Adresse email est obligatoire.',
            'email.email' => 'Le format de l\'adresse e-mail n\'est pas valide',
            'email.max' => 'La longueur de l\'adresse e-mail est trop longue. La longueur maximale est de 191',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'password.required' => 'Le champ Mot de passe est obligatoire.',
            'password.min' => 'La longueur du mot de passe doit être de 8 caractères ou plus',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ], 422);
        } else {
            $user = UserRequest::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'username' => $user->name,
                'message' => 'Admin Request sent successfully',
            ], 200);
        }
    }


    public function register(Request $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $userRequest = UserRequest::where('email', $user->email);
        $userRequest->delete();

        return response()->json([
            'username' => $user->name,
            'message' => 'Enregistré avec succès',
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|max:191',
                'password' => 'required',
            ],
            [
                'email.required' => 'Le champ Adresse email est obligatoire.',
                'email.max' => 'La longueur de l\'adresse e-mail est trop longue. La longueur maximale est de 191',
                'password.required' => 'Le champ Mot de passe est obligatoire.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ], 422);
        } else {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Login et mot de passe incorrects, veuillez les
                   vérifier.',
                ], 401);
            } else {
                if ($user->role == 1) {
                    $role = 'admin';
                    $token = $user->createToken('_AdminToken', ['server:admin'])->plainTextToken;
                } else {
                    $role = '';
                    $token = $user->createToken($user->email . '_Token', [''])->plainTextToken;
                }
                return response()->json([
                    'username' => $user->name,
                    'token' => $token,
                    'message' => 'Connecté avec succès',
                    'role' => $role,
                ], 200);
            }
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Déconnecté avec succès.',
        ], 200);
    }

    function getUsersRequests()
    {
        $requests = UserRequest::all();
        return response()->json([
            'requests' => $requests,
        ], 200);
    }

    function declineUserRequest($id)
    {

        $request = UserRequest::find($id);

        if ($request) {
            $request->delete();
            return response()->json([
                'message' => 'Request Deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Request not found !',
            ], 404);
        }
    }
}
