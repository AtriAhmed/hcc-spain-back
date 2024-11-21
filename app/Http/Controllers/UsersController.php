<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'users' => $users,
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json([
                'message' => 'User deleted successfuly',
            ], 200);
        } else {
            return response()->json([
                'message' => 'User not found !',
            ], 404);
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ], 422);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'username' => $user->name,
                'message' => 'User added successfully',
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191',
            'saudi' => 'boolean', // Add this line
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag(),
            ], 422);
        } else {
            $user = User::find($id);
            if ($user) {
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->role = $request->input('role');
                $user->saudi = $request->input('saudi') ? 1 : 0; // Add this line
                if ($request->input('password')) {
                    $user->password = Hash::make($request->input('password'));
                }
                $user->save();
                return response()->json([
                    'message' => 'User updated successfully',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'User not found !'
                ], 404);
            }
        }
    }

    public function edit($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json([
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => 'User not found !'
            ], 404);
        }
    }
}
