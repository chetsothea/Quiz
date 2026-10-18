<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    private function rules(bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes|required' : 'required';

        return [
            'AuthorName' => "$required|string|max:100",
            'Gender' => 'nullable|string|max:10',
            'DOB' => 'nullable|date',
            'POB' => 'nullable|string|max:255',
            'Address' => 'nullable|string|max:255',
            'Phone' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:100',
            'Photo' => 'nullable|string|max:255',
        ];
    }

    // GET /api/authors
    public function index()
    {
        return response()->json(Author::all());
    }

    // POST /api/authors
    public function store(Request $request)
    {
        $author = Author::create($request->validate($this->rules()));

        return response()->json([
            'message' => 'Author created successfully',
            'data' => $author,
        ], 201);
    }

    // GET /api/authors/{id}
    public function show($id)
    {
        return response()->json(Author::with('books')->findOrFail($id));
    }

    // PUT /api/authors/{id}
    public function update(Request $request, $id)
    {
        $author = Author::findOrFail($id);
        $author->update($request->validate($this->rules(true)));

        return response()->json([
            'message' => 'Author updated successfully',
            'data' => $author,
        ]);
    }

    // DELETE /api/authors/{id}
    public function destroy($id)
    {
        Author::findOrFail($id)->delete();

        return response()->json(['message' => 'Author deleted successfully']);
    }
}
