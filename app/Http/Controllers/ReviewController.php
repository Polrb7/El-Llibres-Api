<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Reviews",
 *     description="API Endpoints of Reviews"
 * )
 * @OA\Schema(
 *     schema="Review",
 *     type="object",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="book_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Great Book"),
 *     @OA\Property(property="text", type="string", example="This book was amazing!"),
 *     @OA\Property(property="valoration", type="integer", example=5)
 * )
 */
class ReviewController extends Controller
{
    /**
     * List all Reviews.
     * 
     * @OA\Get(
     *     path="/api/reviews",
     *     tags={"Reviews"},
     *     summary="Retrieve a list of reviews",
     *     @OA\Response(
     *         response=200,
     *         description="A list of reviews",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="reviews",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Review")
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
            $reviews = Review::all();
            return response()->json([
                'status' => true,
                'reviews' => $reviews,
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
     * Store a newly created review in storage.
     * 
     * @OA\Post(
     *     path="/api/reviews",
     *     tags={"Reviews"},
     *     summary="Create a new review",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="book_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Great Book"),
     *             @OA\Property(property="text", type="string", example="This book was amazing!"),
     *             @OA\Property(property="valoration", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Review created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Review created successfully"),
     *             @OA\Property(property="review", ref="#/components/schemas/Review"),
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
            'book_id' => ['required', 'exists:books,id'],
            'title' => ['required', 'string', 'max:50'],
            'text' => ['required', 'string', 'max:150'],
            'valoration' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
                'httpCode' => 422
            ]);
        }

        try {
            $review = Review::create($request->all());
            return response()->json([
                'status' => true,
                'message' => "Review created successfully",
                'review' => $review,
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
     * Display the specified review.
     * 
     * @OA\Get(
     *     path="/api/reviews/{id}",
     *     tags={"Reviews"},
     *     summary="Retrieve a specific review",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A specific review",
     *         @OA\JsonContent(ref="#/components/schemas/Review")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $review = Review::find($id);

        if (is_null($review)) {
            return response()->json([
                'status' => false,
                'message' => "Review not found",
                'httpCode' => 404
            ]);
        }
        try {
            return response()->json([
                'status' => true,
                'review' => $review,
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
     * Display reviews for a specific book.
     * 
     * @OA\Get(
     *     path="/api/books/{book_id}/reviews",
     *     tags={"Reviews"},
     *     summary="Retrieve reviews for a specific book",
     *     @OA\Parameter(
     *         name="book_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of reviews for a specific book",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Review")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function showReviewsInBook(string $id)
    {
        try {
            $reviews = Review::all();
            $bookReviews = $reviews->where('book_id', $id);
            return response()->json([
                'status' => true,
                'reviews' => $bookReviews,
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
     * Display reviews by a specific user.
     * 
     * @OA\Get(
     *     path="/api/users/{user_id}/reviews",
     *     tags={"Reviews"},
     *     summary="Retrieve reviews by a specific user",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of reviews by a specific user",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Review")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function getReviewsByUserId(string $user_id)
    {
        try {
            $reviews = Review::where('user_id', $user_id)->get();
            return response()->json([
                'status' => true,
                'reviews' => $reviews,
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
     * Update the specified review in storage.
     * 
     * @OA\Put(
     *     path="/api/reviews/{id}",
     *     tags={"Reviews"},
     *     summary="Update a specific review",
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
     *             @OA\Property(property="title", type="string", example="Updated Title"),
     *             @OA\Property(property="text", type="string", example="Updated text"),
     *             @OA\Property(property="valoration", type="integer", example=4)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Review")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review not found"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $review = Review::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:50'],
                'text' => ['required', 'string', 'max:150'],
                'valoration' => ['required', 'integer', 'min:1', 'max:5'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors(),
                    'httpCode' => 422
                ]);
            }
            try {
                $review->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => "Review updated successfully",
                    'review' => $review,
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
     * Remove the specified review from storage.
     * 
     * @OA\Delete(
     *     path="/api/reviews/{id}",
     *     tags={"Reviews"},
     *     summary="Delete a specific review",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Review deleted successfully"),
     *             @OA\Property(property="httpCode", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();
            return response()->json([
                'status' => true,
                'message' => "Review deleted successfully",
                'review' => $review,
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
