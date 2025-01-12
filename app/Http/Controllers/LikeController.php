<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Likes",
 *     description="API Endpoints of Likes"
 * )
 * @OA\Schema(
 *     schema="Like",
 *     type="object",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="book_id", type="integer", example=1),
 * )
 */
class LikeController extends Controller
{
    /**
     * List all Likes.
     * 
     * @OA\Get(
     *     path="/api/likes",
     *     tags={"Likes"},
     *     summary="Retrieve a list of likes",
     *     @OA\Response(
     *         response=200,
     *         description="A list of likes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="likes",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="book_id", type="integer", example=1),
     *                 )
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
            $likes = Like::all();
            return response()->json([
                'status' => true,
                'likes' => $likes,
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
     * Create a new like.
     * 
     * @OA\Post(
     *     path="/api/likes",
     *     tags={"Likes"},
     *     summary="Create a new like",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Like details",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="book_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Like created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'], // $user->id
            'book_id' => ['required', 'exists:books,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
                'httpCode' => 422
            ]);
        }

        try {
            $like = Like::create($request->all());
            return response()->json([
                'status' => true,
                'message' => "Like created successfully",
                'like' => $like,
                'httpCode' => 201
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
     * Retrieve a specific like by ID.
     * 
     * @OA\Get(
     *     path="/api/likes/{id}",
     *     tags={"Likes"},
     *     summary="Retrieve a specific like",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Like ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="book_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Like not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $like = Like::find($id);

        if (is_null($like)) {
            return response()->json([
                'status' => false,
                'message' => "Like not found",
                'httpCode' => 404
            ]);
        }
        try {
            return response()->json([
                'status' => true,
                'like' => $like,
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
     * Update a specific like.
     * 
     * @OA\Put(
     *     path="/api/likes/{id}",
     *     tags={"Likes"},
     *     summary="Update a specific like",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Like ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated like details",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="book_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Like not found"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $like = Like::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id' => ['required', 'exists:users,id'], // $user->id
                'book_id' => ['required', 'exists:books,id'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors(),
                    'httpCode' => 422
                ]);
            }
            try {
                $like->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => "Like updated successfully",
                    'like' => $like,
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
     * Delete a specific like.
     * 
     * @OA\Delete(
     *     path="/api/likes/{id}",
     *     tags={"Likes"},
     *     summary="Delete a specific like",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Like ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Like not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $like = Like::findOrFail($id);
            $like->delete();
            return response()->json([
                'status' => true,
                'message' => "Like deleted successfully",
                'like' => $like,
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
