<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'subject_id',
        'class_id',
        'term_id',
        'total_marks',
        'due_date',
        'description',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    const TYPE_ASSIGNMENT = 'assignment';
    const TYPE_TEST = 'test';
    const TYPE_EXAM = 'exam';

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(AssessmentSubmission::class);
    }
}
