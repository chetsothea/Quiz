<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    protected $table = 'tblAuthor';

    protected $primaryKey = 'AuthorID';

    protected $fillable = [
        'AuthorName',
        'Gender',
        'DOB',
        'POB',
        'Address',
        'Phone',
        'Email',
        'Photo',
    ];

    protected $casts = [
        'DOB' => 'date:Y-m-d',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'tblBookAuthor', 'AuthorID', 'BookID')
            ->withPivot('AuthorDate', 'Remark')
            ->withTimestamps();
    }
}
