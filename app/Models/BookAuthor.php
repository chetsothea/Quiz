<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Junction table with a composite primary key (BookID, AuthorID).
 * Eloquent has no native composite keys, so the controller
 * queries by both columns for show/update/delete.
 */
class BookAuthor extends Model
{
    protected $table = 'tblBookAuthor';

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = ['BookID', 'AuthorID', 'AuthorDate', 'Remark'];

    protected $casts = [
        'AuthorDate' => 'date:Y-m-d',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'BookID', 'BookID');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'AuthorID', 'AuthorID');
    }
}
