<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json(UserResource::collection($users), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = $request->role_id;
        $user->save();

        event(new Registered($user));

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado con éxito.'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if($user) {
            return response()->json([
                'success' => true,
                'data' => new UserResource($user)
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no econtrado.'
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $user = User::find($id);

        if($user) {
            $user->update([
                $user->name = $request->name,
                $user->email = $request->email,
                $user->password = Hash::make($request->password),
                $user->role_id = $request->role_id
            ]);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Usuario modificado correctamente.',
                'data' => new UserResource($user)
            ], 200);

        } else {
            return response()->json([
                'success' => false,
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idArray)
    {
        $ids = explode(",",$idArray);
        $users = User::find($ids);

        if($users) {

            $users->map(function($user) {
                $user->tokens()->delete();
                $user->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente.'
            ], 200);

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }
   
    }
}
