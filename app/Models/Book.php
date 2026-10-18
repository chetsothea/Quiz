<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $table = 'tblBook';

    protected $primaryKey = 'BookID';

    protected $fillable = [
        'BookTitle',
        'BookTypeID',
        'PublishDate',
        'NumOfPages',
        'NumOfCopies',
        'Edition',
        'Publisher',
        'BookSource',
        'Remark',
    ];

    protected $casts = [
        'PublishDate' => 'date:Y-m-d',
    ];

    public function bookType(): BelongsTo
    {
        return $this->belongsTo(BookType::class, 'BookTypeID', 'BookTypeID');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'tblBookAuthor', 'BookID', 'AuthorID')
            ->withPivot('AuthorDate', 'Remark')
            ->withTimestamps();
    }
}
