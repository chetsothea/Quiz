<?php

namespace App\Http\Controllers;

use App\Models\BookAuthor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookAuthorController extends Controller
{
    // Composite key lookup: a record is identified by BookID + AuthorID
    private function findOrFail($bookId, $authorId)
    {
        return BookAuthor::where('BookID', $bookId)
            ->where('AuthorID', $authorId)
            ->firstOrFail();
    }

    // GET /api/book-authors
    public function index()
    {
        return response()->json(BookAuthor::with(['book', 'author'])->get());
    }

    // POST /api/book-authors
    public function store(Request $request)
    {
        $data = $request->validate([
            'BookID' => 'required|integer|exists:tblBook,BookID',
            'AuthorID' => [
                'required',
                'integer',
                'exists:tblAuthor,AuthorID',
                Rule::unique('tblBookAuthor')->where('BookID', $request->input('BookID')),
            ],
            'AuthorDate' => 'nullable|date',
            'Remark' => 'nullable|string',
        ]);

        $bookAuthor = BookAuthor::create($data);

        return response()->json([
            'message' => 'Book author created successfully',
            'data' => $bookAuthor->load(['book', 'author']),
        ], 201);
    }

    // GET /api/book-authors/{bookId}/{authorId}
    public function show($bookId, $authorId)
    {
        return response()->json($this->findOrFail($bookId, $authorId)->load(['book', 'author']));
    }

    // PUT /api/book-authors/{bookId}/{authorId}
    public function update(Request $request, $bookId, $authorId)
    {
        $this->findOrFail($bookId, $authorId);

        $data = $request->validate([
            'AuthorDate' => 'nullable|date',
            'Remark' => 'nullable|string',
        ]);

        BookAuthor::where('BookID', $bookId)->where('AuthorID', $authorId)->update($data);

        return response()->json([
            'message' => 'Book author updated successfully',
            'data' => $this->findOrFail($bookId, $authorId)->load(['book', 'author']),
        ]);
    }

    // DELETE /api/book-authors/{bookId}/{authorId}
    public function destroy($bookId, $authorId)
    {
        $this->findOrFail($bookId, $authorId);

        BookAuthor::where('BookID', $bookId)->where('AuthorID', $authorId)->delete();

        return response()->json(['message' => 'Book author deleted successfully']);
    }
}
