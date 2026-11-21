<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'dob',
        'gender',
        'address',
        'bio',
        'education',
        'experience_years',
        'skills_text',
        'default_cv_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'experience_years' => 'integer',
        ];
    }

    /**
     * Get the user that owns this candidate profile (One-to-One inverse).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

