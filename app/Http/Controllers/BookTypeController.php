<?php

namespace App\Http\Controllers;

use App\Models\BookType;
use Illuminate\Http\Request;

class BookTypeController extends Controller
{
    // GET /api/book-types
    public function index()
    {
        return response()->json(BookType::all());
    }

    // POST /api/book-types
    public function store(Request $request)
    {
        $data = $request->validate([
            'BookTypeName' => 'required|string|max:100',
        ]);

        $bookType = BookType::create($data);

        return response()->json([
            'message' => 'Book type created successfully',
            'data' => $bookType,
        ], 201);
    }

    // GET /api/book-types/{id}
    public function show($id)
    {
        return response()->json(BookType::with('books')->findOrFail($id));
    }

    // PUT /api/book-types/{id}
    public function update(Request $request, $id)
    {
        $bookType = BookType::findOrFail($id);

        $data = $request->validate([
            'BookTypeName' => 'required|string|max:100',
        ]);

        $bookType->update($data);

        return response()->json([
            'message' => 'Book type updated successfully',
            'data' => $bookType,
        ]);
    }

    // DELETE /api/book-types/{id}
    public function destroy($id)
    {
        $bookType = BookType::findOrFail($id);

        if ($bookType->books()->exists()) {
            return response()->json([
                'message' => 'Cannot delete: this book type is used by some books',
            ], 409);
        }

        $bookType->delete();

        return response()->json(['message' => 'Book type deleted successfully']);
    }
}
