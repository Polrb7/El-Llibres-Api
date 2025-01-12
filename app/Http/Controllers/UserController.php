<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints of Users"
 * )
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Pol"),
 *     @OA\Property(property="surname", type="string", example="Romeu"),
 *     @OA\Property(property="username", type="string", example="Polrb7"),
 *     @OA\Property(property="age", type="integer", example="25"),
 *     @OA\Property(property="email", type="string", example="pol@example.com"),
 *     @OA\Property(property="password", type="string", example="password"),
 *     @OA\Property(property="admin", type="boolean", example="true"),
 *     @OA\Property(property="profile_img", type="string", example="example.jpg"),
 * )
 */
class UserController extends Controller
{
    /**
     * List all Users.
     * 
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Retrieve a list of users",
     *     @OA\Response(
     *         response=200,
     *         description="A list of users",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="users",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             ),
     *             @OA\Property(
     *                 property="httpCode",
     *                 type="integer",
     *                 example=200
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json([
                'status' => true,
                'users' => $users,
                'httpCode' => 200
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'httpCode' => 500
            ]);
        }
    }

    /**
     * Store a newly created user in storage.
     * 
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="httpCode", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="object"),
     *             @OA\Property(property="httpCode", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'age' => 'required|integer',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
                'errorCode' => 422
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'username' => $request->username,
            'age' => $request->age,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin' => false,
            'profile_img' => Null,
        ]);
        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => "User created successfully",
            'httpCode' => 200
        ]);
    }

    /**
     * Display the specified user.
     * 
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Retrieve a specific user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A specific user",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return response()->json([
                'status' => false,
                'message' => "User not found",
                'httpCode' => 404
            ]);
        }
        try {
            return response()->json([
                'status' => true,
                'user' => $user,
                'httpCode' => 200
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'httpCode' => 200
            ]);
        }
    }

    /**
     * Update the specified user in storage.
     * 
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Update a specific user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", example="UpdatedUsername"),
     *             @OA\Property(property="email", type="string", example="updated@example.com"),
     *             @OA\Property(property="password", type="string", example="updatedpassword"),
     *             @OA\Property(property="profile_img", type="string", example="updated.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'username' => 'string|max:255|unique:users,username,',
                'email' => 'email|unique:users,email,',
                'password' => 'confirmed|string',
                'profile_img' => 'string',
                'admin' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors(),
                    'httpCode' => 422
                ]);
            }
            try {
                $user->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => "User updated successfully",
                    'user' => $user,
                    'httpCode' => 201
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                    'httpCode' => 500
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'httpCode' => 404
            ]);
        }
    }

    /**
     * Remove the specified user from storage.
     * 
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a specific user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User deleted successfully"),
     *             @OA\Property(property="httpCode", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => "User deleted successfully",
                'user' => $user,
                'httpCode' => 200
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'httpCode' => 404
            ]);
        }
    }
}
