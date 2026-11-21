<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'category_id',
        'title',
        'slug',
        'job_type',
        'experience_level',
        'salary_min',
        'salary_max',
        'is_negotiable',
        'location',
        'work_address',
        'vacancies',
        'description',
        'requirements',
        'benefits',
        'deadline',
        'status',
        'views_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'is_negotiable' => 'boolean',
            'deadline' => 'date',
            'vacancies' => 'integer',
            'views_count' => 'integer',
        ];
    }

    /**
     * Get the company that posted this job.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the category this job belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the skills required for this job (Many-to-Many).
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skill');
    }

    /**
     * Get all applications for this job.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get all saved/bookmarked entries for this job.
     */
    public function savedByUsers(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }
}

