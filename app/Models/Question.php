<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'body', 'user_id'
    ];

    public function tags() {
        return $this->belongsToMany(Tag::class, 'question_tags');
    }

    public function images() {
        return $this->belongsToMany(Image::class, 'question_images');
    }
}
