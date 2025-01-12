<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


/**
 * @OA\Tag(
 *     name="Comments",
 *     description="API Endpoints of Comments"
 * )
 * @OA\Schema(
 *     schema="Comment",
 *     type="object",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="review_id", type="integer", example=1),
 *     @OA\Property(property="comment", type="string", example="This is a comment")
 * )
 */
class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @OA\Get(
     *     path="/api/comments",
     *     tags={"Comments"},
     *     summary="Retrieve a list of comments",
     *     @OA\Response(
     *         response=200,
     *         description="A list of comments",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="comments",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Comment")
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
            $comments = Comment::all();
            return response()->json([
                'status' => true,
                'comments' => $comments,
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
     * Store a newly created resource in storage.
     * 
     * @OA\Post(
     *     path="/api/comments",
     *     tags={"Comments"},
     *     summary="Create a new comment",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="review_id", type="integer", example=1),
     *             @OA\Property(property="comment", type="string", example="This is a comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comment created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Comment created successfully"),
     *             @OA\Property(property="comment", ref="#/components/schemas/Comment"),
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
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'], // $user->id
            'review_id' => ['required', 'exists:reviews,id'],
            'comment' => ['required', 'string', 'max:200'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
                'httpCode' => 422
            ]);
        }

        try {
            $comment = Comment::create($request->all());
            return response()->json([
                'status' => true,
                'message' => "Comment created successfully",
                'comment' => $comment,
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
     * Display the specified resource.
     * 
     * @OA\Get(
     *     path="/api/comments/{id}",
     *     tags={"Comments"},
     *     summary="Retrieve a specific comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A specific comment",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            return response()->json($comment, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     * 
     * @OA\Put(
     *     path="/api/comments/{id}",
     *     tags={"Comments"},
     *     summary="Update a specific comment",
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
     *             @OA\Property(property="comment", type="string", example="Updated comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'comment' => ['required', 'string', 'max:200'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors(),
                    'httpCode' => 422
                ]);
            }

            try {
                $comment->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => "Comment updated successfully",
                    'comment' => $comment,
                    'httpCode' => 200
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
     * Remove the specified resource from storage.
     * 
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     tags={"Comments"},
     *     summary="Delete a specific comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Comment deleted successfully"),
     *             @OA\Property(property="httpCode", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->delete();
            return response()->json([
                'status' => true,
                'message' => "Comment deleted successfully",
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
