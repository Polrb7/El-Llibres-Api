<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="El_Llibres API",
 *     version="1.0.0",
 *     description="API for managing Books",
 *     @OA\Contact(
 *         email="pol.romeu@cirvianum.cat"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost",
 *     description="Local development server"
 * )
 * 
 * @OA\Tag(
 *     name="Books",
 *     description="API Endpoints of Books"
 * )
 * @OA\Schema(
 *     schema="Book",
 *     type="object",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="New Book"),
 *     @OA\Property(property="author", type="string", example="Book Author"),
 *     @OA\Property(property="genre", type="string", example="Book Genre"),
 *     @OA\Property(property="description", type="string", example="Book description"),
 *     @OA\Property(property="book_img", type="string", example="Book image"),
 * )
 */
class BookController extends Controller
{
    /**
     * List all Books.
     * 
     * @OA\Get(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="Retrieve a list of books",
     *     @OA\Response(
     *         response=200,
     *         description="A list of books",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="books",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="New Book"),
     *                     @OA\Property(property="author", type="string", example="Book Ahtor"),
     *                     @OA\Property(property="genre", type="string", example="Book Genre"),
     *                     @OA\Property(property="description", type="string", example="Book description"),
     *                     @OA\Property(property="user_id", type="integer", example=1),
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
            $books = Book::all();
            return response()->json([
                'status' => true,
                'books' => $books,
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
     * Create a new book.
     * 
     * @OA\Post(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="Create a new book",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Book details",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="New Book"),
     *             @OA\Property(property="author", type="string", example="Book Ahtor"),
     *             @OA\Property(property="genre", type="string", example="Book Genre"),
     *             @OA\Property(property="description", type="string", example="Book description"),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'], // $user->id
            'title' => ['required', 'string', 'max:50'],
            'author' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string'],
            'book_img' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
                'httpCode' => 422
            ]);
        }

        try {
            $book = Book::create($request->all());
            return response()->json([
                'status' => true,
                'message' => "Book created successfully",
                'book' => $book,
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
     * Retrieve a specific book by ID.
     * 
     * @OA\Get(
     *     path="/api/books/{id}",
     *     tags={"Books"},
     *     summary="Retrieve a specific book",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Book ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Sample Book"),
     *             @OA\Property(property="author", type="string", example="Book Ahtor"),
     *             @OA\Property(property="genre", type="string", example="Book Genre"),
     *             @OA\Property(property="description", type="string", example="Book description"),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $book = Book::find($id);

        if (is_null($book)) {
            return response()->json([
                'status' => false,
                'message' => "Book not found",
                'httpCode' => 404
            ]);
        }
        try {
            return response()->json([
                'status' => true,
                'book' => $book,
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
     * @OA\Get(
     *     path="/api/users/{userId}/books",
     *     summary="Retrieve a list of books by user",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Book")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function showBooksByUser(string $id)
    {
        try {
            $books = Book::all();
            $userBooks = $books->where('user_id', $id);
            return response()->json([
                'status' => true,
                'books' => $userBooks,
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
     * Update a specific Book.
     * 
     * @OA\Put(
     *     path="/api/book/{id}",
     *     tags={"Books"},
     *     summary="Update a specific book",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Book ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated book details",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Book"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="user_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $book = Book::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:50'],
                'author' => ['required', 'string', 'max:100'],
                'genre' => ['required', 'string', 'max:50'],
                'description' => ['required', 'string'],
                'book_img' => ['string'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors(),
                    'httpCode' => 422
                ]);
            }
            try {
                $book->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => "Book updated successfully",
                    'book' => $book,
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
     * Delete a specific Book.
     * 
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     tags={"Books"},
     *     summary="Delete a specific book",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Book ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();
            return response()->json([
                'status' => true,
                'message' => "Book deleted successfully",
                'book' => $book,
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
