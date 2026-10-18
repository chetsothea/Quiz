<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private function rules(bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes|required' : 'required';

        return [
            'BookTitle' => "$required|string|max:255",
            'BookTypeID' => "$required|integer|exists:tblBookType,BookTypeID",
            'PublishDate' => 'nullable|date',
            'NumOfPages' => 'nullable|integer|min:1',
            'NumOfCopies' => 'nullable|integer|min:0',
            'Edition' => 'nullable|string|max:50',
            'Publisher' => 'nullable|string|max:100',
            'BookSource' => 'nullable|string|max:100',
            'Remark' => 'nullable|string',
        ];
    }

    // GET /api/books
    public function index()
    {
        return response()->json(Book::with('bookType')->get());
    }

    // POST /api/books
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['NumOfCopies'] = $data['NumOfCopies'] ?? 0; // column is NOT NULL

        $book = Book::create($data);

        return response()->json([
            'message' => 'Book created successfully',
            'data' => $book->load('bookType'),
        ], 201);
    }

    // GET /api/books/{id}
    public function show($id)
    {
        return response()->json(Book::with(['bookType', 'authors'])->findOrFail($id));
    }

    // PUT /api/books/{id}
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $data = $request->validate($this->rules(true));
        if (array_key_exists('NumOfCopies', $data)) {
            $data['NumOfCopies'] = $data['NumOfCopies'] ?? 0; // column is NOT NULL
        }

        $book->update($data);

        return response()->json([
            'message' => 'Book updated successfully',
            'data' => $book->load('bookType'),
        ]);
    }

    // DELETE /api/books/{id}
    public function destroy($id)
    {
        Book::findOrFail($id)->delete();

        return response()->json(['message' => 'Book deleted successfully']);
    }
}
