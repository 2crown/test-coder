<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admission_number',
        'class_id',
        'date_of_birth',
        'gender',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subjects');
    }

    public function submissions()
    {
        return $this->hasMany(AssessmentSubmission::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'student_parent_links', 'student_id', 'parent_id');
    }
}
