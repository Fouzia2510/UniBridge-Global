<?php

namespace App\Models;

use App\Models\Application;
use App\Models\ConsultantProfile;
use App\Models\StudentProfile;
use App\Models\UserDocument;
use App\Models\VisaApplication;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\PasskeyAuthenticatable;

class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable;

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    const ROLE_STUDENT = 'student';

    const ROLE_CONSULTANT = 'consultant';

    const ROLE_ADMIN = 'admin';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
        'profile_photo_path',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',

            'password' => 'hashed',

            'is_approved' => 'boolean',

            /*
            |--------------------------------------------------------------------------
            | Google OAuth
            |--------------------------------------------------------------------------
            */

            'google_oauth_token' => 'encrypted:array',

            'google_classroom_oauth_token' => 'encrypted:array',

            'google_drive_oauth_token' => 'encrypted:array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => route('admin.dashboard'),

            self::ROLE_CONSULTANT => route('consultant.dashboard'),

            self::ROLE_STUDENT => route('student.dashboard'),

            default => route('dashboard'),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Student Profile
    |--------------------------------------------------------------------------
    */

    public function studentProfile(): HasOne
    {
        return $this->hasOne(
            StudentProfile::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Consultant Profile
    |--------------------------------------------------------------------------
    */

    public function consultantProfile(): HasOne
    {
        return $this->hasOne(
            ConsultantProfile::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Student Applications
    |--------------------------------------------------------------------------
    */

    public function applications(): HasMany
    {
        return $this->hasMany(
            Application::class,
            'student_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Visa Applications
    |--------------------------------------------------------------------------
    |
    | Stores visa procedures belonging to this student.
    |
    */

    public function visaApplications(): HasMany
    {
        return $this->hasMany(
            VisaApplication::class,
            'student_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | User Documents
    |--------------------------------------------------------------------------
    |
    | Stores CNIC, passport, degree, transcript, English certificate,
    | and other uploaded documents for students, consultants, and admins.
    |
    */

    public function documents(): HasMany
    {
        return $this->hasMany(
            UserDocument::class
        );
    }
}