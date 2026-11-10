<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminWhatsAppController;
use App\Http\Controllers\ChatGroupController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\ConsultantMessageController;
use App\Http\Controllers\ConsultantProfileController;
use App\Http\Controllers\ConsultantVisitorController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GoogleClassroomController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\GoogleMeetController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\VisaApplicationController;
use App\Http\Controllers\UniversityFinderController;
use App\Http\Controllers\Admin\UniversityManagementController;
use App\Http\Controllers\PublicConsultantController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\PublicGeminiController;
use App\Http\Controllers\Student\UniversityMatchController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/api/public/gemini/generate', [
    PublicGeminiController::class,
    'generate',
])
    ->middleware('throttle:5,1')
    ->name('api.gemini.public');

Route::get('/consultants', [
    PublicConsultantController::class,
    'index'
])->name('consultants.index');

Route::get('/consultants/{consultant:slug}', [
    PublicConsultantController::class,
    'show'
])->name('consultants.show');

Route::get('/pending-approval', function () {
    return view('pending-approval');
})->name('pending-approval');


/*
|--------------------------------------------------------------------------
| General Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    $user = Auth::user();

    return redirect()->to(
        $user->dashboardRoute()
    );

})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| General Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Gemini
    |--------------------------------------------------------------------------
    */

   
Route::post('/api/gemini/generate', [
    GeminiController::class,
    'generate'
])
    ->middleware('throttle:10,1')
    ->name('api.gemini.generate');

// Keep the existing URL working for compatibility.
Route::post('/gemini/generate', [
    GeminiController::class,
    'generate'
])
    ->middleware('throttle:10,1')
    ->name('gemini.generate');


    /*
    |--------------------------------------------------------------------------
    | General Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::post('/profile/photo', [
        ProfileController::class,
        'updatePhoto'
    ])->name('profile.photo.update');

    Route::delete('/profile/photo', [
        ProfileController::class,
        'deletePhoto'
    ])->name('profile.photo.delete');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

    Route::get('/bookings/{booking}/join', [
        BookingController::class,
        'join'
    ])->name('bookings.join');

    Route::get('/meeting-attendance', [
        BookingController::class,
        'meetingAttendance'
    ])->name('meeting-attendance.index');


    /*
    |--------------------------------------------------------------------------
    | User Documents
    |--------------------------------------------------------------------------
    */

    Route::get('/documents', [
        DocumentController::class,
        'index'
    ])->name('documents.index');

    Route::post('/documents', [
        DocumentController::class,
        'store'
    ])->name('documents.store');

    Route::put('/documents/{document}', [
        DocumentController::class,
        'update'
    ])->name('documents.update');

    Route::delete('/documents/{document}', [
        DocumentController::class,
        'destroy'
    ])->name('documents.destroy');

    Route::get('/documents/{document}/download', [
        DocumentController::class,
        'download'
    ])->name('documents.download');


    /*
    |--------------------------------------------------------------------------
    | Google Drive
    |--------------------------------------------------------------------------
    */

    Route::get('/auth/google/drive/connect', [
        GoogleDriveController::class,
        'connect'
    ])->name('google.drive.connect');

    Route::get('/auth/google/drive/callback', [
        GoogleDriveController::class,
        'callback'
    ])->name('google.drive.callback');

});


/*
|--------------------------------------------------------------------------
| ADMIN PORTAL
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Internal Group Chat - Authenticated Members
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    Route::get('/notifications', [
        \App\Http\Controllers\NotificationController::class,
        'index',
    ])->name('notifications.index');

    Route::patch('/notifications/{id}/read', [
        \App\Http\Controllers\NotificationController::class,
        'markAsRead',
    ])->name('notifications.read');

    Route::patch('/notifications/read-all', [
        \App\Http\Controllers\NotificationController::class,
        'markAllAsRead',
    ])->name('notifications.read-all');

    Route::get('/group-chats', [
        ChatGroupController::class,
        'index',
    ])->name('chat-groups.index');

    Route::get('/group-chats/{chatGroup}', [
        ChatGroupController::class,
        'show',
    ])->name('chat-groups.show');

    Route::post('/group-chats/{chatGroup}/messages', [
        ChatGroupController::class,
        'storeMessage',
    ])->name('chat-groups.messages.store');

    // Fetch saved messages for the internal group chat.
    Route::get('/group-chats/{chatGroup}/messages/new', [
        ChatGroupController::class,
        'fetchNewMessages',
    ])->name('chat-groups.messages.new');
});


Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'role:admin',
        'approved',
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Admin Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            AdminController::class,
            'dashboard'
        ])->name('dashboard');
        /*
        |--------------------------------------------------------------------------
        | Admin WhatsApp Inbox and Group Management
        |--------------------------------------------------------------------------
        */

        // WhatsApp inbox
        Route::get('/whatsapp', [
            AdminWhatsAppController::class,
            'index',
        ])->name('whatsapp.index');
        // Separate WhatsApp management pages
        Route::get('/whatsapp/chats', [
            AdminWhatsAppController::class,
            'chatsPage',
        ])->name('whatsapp.chats');

        Route::get('/whatsapp/groups/{group}/inbox', [
    AdminWhatsAppController::class,
    'groupInbox',
])->name('whatsapp.group-inbox');

Route::post('/whatsapp/groups/{group}/messages', [
    AdminWhatsAppController::class,
    'storeGroupMessage',
])->name('whatsapp.group-messages.store');
Route::get('/whatsapp/groups', [
            AdminWhatsAppController::class,
            'groupsPage',
        ])->name('whatsapp.groups');
        // Central WhatsApp Contacts
        Route::get('/whatsapp/contacts', [
            AdminWhatsAppController::class,
            'contactsPage',
        ])->name('whatsapp.contacts');
                // Add and edit central WhatsApp contacts
        Route::post('/whatsapp/contacts', [
            AdminWhatsAppController::class,
            'storeContact',
        ])->name('whatsapp.contacts.store');

        Route::patch('/whatsapp/contacts/{contact}', [
            AdminWhatsAppController::class,
            'updateContact',
        ])->name('whatsapp.contacts.update');
        Route::get('/whatsapp/broadcasts', [
            AdminWhatsAppController::class,
            'broadcastsPage',
        ])->name('whatsapp.broadcasts');

        // Open one individual WhatsApp chat
        Route::get('/whatsapp/chat/{conversation}', [
            AdminWhatsAppController::class,
            'chatPage',
        ])->name('whatsapp.chat');

        // Individual WhatsApp conversation
        Route::post('/whatsapp/{conversation}/reply', [
            AdminWhatsAppController::class,
            'reply',
        ])->name('whatsapp.reply');

        // Send the approved application-update template
        Route::post('/whatsapp/{conversation}/template', [
            AdminWhatsAppController::class,
            'sendTemplate',
        ])->name('whatsapp.template');

        // Send an attachment to an individual contact
        Route::post('/whatsapp/{conversation}/media', [
            AdminWhatsAppController::class,
            'sendMedia',
        ])->name('whatsapp.media');

        // Share a location explicitly provided by the admin
        Route::post('/whatsapp/{conversation}/location', [
            AdminWhatsAppController::class,
            'sendLocation',
        ])->name('whatsapp.location');

        Route::post('/whatsapp/new-chat', [
            AdminWhatsAppController::class,
            'newChat',
        ])->name('whatsapp.new-chat');
        // Rename a WhatsApp conversation
        Route::patch('/whatsapp/{conversation}/rename', [
            AdminWhatsAppController::class,
            'renameConversation',
        ])->name('whatsapp.rename');

        // Delete a WhatsApp conversation
        Route::delete('/whatsapp/{conversation}', [
            AdminWhatsAppController::class,
            'deleteConversation',
        ])->name('whatsapp.delete');
        // Create a group
        Route::post('/whatsapp/groups', [
            AdminWhatsAppController::class,
            'createGroup',
        ])->name('whatsapp.groups.create');

        // Delete a group
        Route::delete('/whatsapp/groups/{group}', [
            AdminWhatsAppController::class,
            'deleteGroup',
        ])->name('whatsapp.groups.delete');

        // Add a contact to a group
        Route::post('/whatsapp/groups/{group}/members', [
            AdminWhatsAppController::class,
            'addMember',
        ])->name('whatsapp.groups.members.add');

        // Remove a contact from a group
        Route::delete('/whatsapp/groups/{group}/members/{member}', [
            AdminWhatsAppController::class,
            'removeMember',
        ])->name('whatsapp.groups.members.remove');
        
        // Create a broadcast draft and preview eligible recipients.
        // This route does not send WhatsApp messages.
        Route::post('/whatsapp/broadcast-preview', [
            AdminWhatsAppController::class,
            'broadcastPreview',
        ])->name('whatsapp.broadcast-preview');
// Send one explicitly confirmed sandbox test message.
Route::post('/whatsapp/broadcast-test-send/{broadcast}', [
    AdminWhatsAppController::class,
    'sendTestBroadcast',
])->name('whatsapp.broadcast-test-send');


/*
        |--------------------------------------------------------------------------
        | Admin Universities
        |--------------------------------------------------------------------------
        */

        // Internal group chat management, separate from WhatsApp groups.
        Route::get('/chat-groups', [
            ChatGroupController::class,
            'index',
        ])->name('chat-groups.index');

        Route::get('/chat-groups/create', [
            ChatGroupController::class,
            'create',
        ])->name('chat-groups.create');

        Route::post('/chat-groups', [
            ChatGroupController::class,
            'store',
        ])->name('chat-groups.store');


       Route::get('/universities', [
    UniversityManagementController::class,
    'index',
])->name('universities.index');

Route::get('/universities/create', [
    UniversityManagementController::class,
    'create',
])->name('universities.create');

Route::post('/universities', [
    UniversityManagementController::class,
    'store',
])->name('universities.store');

Route::get('/universities/{university}/edit', [
    UniversityManagementController::class,
    'edit',
])->name('universities.edit');

Route::put('/universities/{university}', [
    UniversityManagementController::class,
    'update',
])->name('universities.update');


        /*
        |--------------------------------------------------------------------------
        | Admin Users
        |--------------------------------------------------------------------------
        */

        Route::get('/users', [
            AdminController::class,
            'users'
        ])->name('users.index');

        Route::get('/students', [
            AdminController::class,
            'students'
        ])->name('students');

        Route::get('/consultants', [
            AdminController::class,
            'consultants'
        ])->name('consultants');

        Route::get('/administrators', [
            AdminController::class,
            'administrators'
        ])->name('administrators');

        Route::get('/role-management', [
            AdminController::class,
            'roleManagement'
        ])->name('role-management');


        /*
        |--------------------------------------------------------------------------
        | Admin Visa
        |--------------------------------------------------------------------------
        */

        Route::get('/visa', [
            VisaApplicationController::class,
            'adminIndex'
        ])->name('visa.index');

        Route::get('/applications/{application}/visa/create', [
            VisaApplicationController::class,
            'create'
        ])->name('visa.create');

        Route::post('/applications/{application}/visa', [
            VisaApplicationController::class,
            'store'
        ])->name('visa.store');

        Route::get('/visa/{visaApplication}', [
            VisaApplicationController::class,
            'adminShow'
        ])->name('visa.show');

        Route::post('/visa/{visaApplication}/submit', [
            VisaApplicationController::class,
            'adminSubmit'
        ])->name('visa.submit');


        /*
        |--------------------------------------------------------------------------
        | User Role / Approval
        |--------------------------------------------------------------------------
        */

        Route::patch('/users/{user}/role', [
            AdminController::class,
            'updateRole'
        ])->name('users.role');

        Route::patch('/users/{user}/approve', [
            AdminController::class,
            'approveUser'
        ])->name('users.approve');

        Route::get('/users/{user}/edit', [
            AdminController::class,
            'editUser'
        ])->name('users.edit');

        Route::put('/users/{user}', [
            AdminController::class,
            'updateUser'
        ])->name('users.update');

        Route::delete('/users/{user}', [
            AdminController::class,
            'destroyUser'
        ])->name('users.destroy');


        /*
        |--------------------------------------------------------------------------
        | Admin Applications
        |--------------------------------------------------------------------------
        */

        Route::get('/applications', [
            AdminController::class,
            'applications'
        ])->name('applications');

        Route::get('/applications/{application}', [
            AdminController::class,
            'showApplication'
        ])->name('applications.show');

        /*
        | Assign an approved consultant to an application
        */

        Route::patch('/applications/{application}/consultant', [
            AdminController::class,
            'assignConsultant'
        ])->name('applications.consultant.assign');

        Route::patch('/applications/{application}/status', [
            AdminController::class,
            'updateApplicationStatus'
        ])->name('applications.status');

        Route::get('/applications/{application}/payment', [
            AdminController::class,
            'showPayment'
        ])->name('applications.payment');

        Route::get('/applications/{application}/payments/{payment}/proof', [
            AdminController::class,
            'downloadPaymentProof'
        ])->name('applications.payment.proof');

        Route::patch('/applications/{application}/payment/verify', [
            AdminController::class,
            'verifyPayment'
        ])->name('applications.payment.verify');

        Route::patch('/applications/{application}/payment/reject', [
            AdminController::class,
            'rejectPayment'
        ])->name('applications.payment.reject');


        /*
        |--------------------------------------------------------------------------
        | Consultant Profiles
        |--------------------------------------------------------------------------
        */

        Route::get('/consultant-profiles', [
            AdminController::class,
            'consultantProfiles'
        ])->name('consultant-profiles');

        Route::patch('/consultant-profiles/{consultant}', [
            AdminController::class,
            'updateConsultantProfile'
        ])->name('consultant-profiles.update');


        /*
        |--------------------------------------------------------------------------
        | Document Verification
        |--------------------------------------------------------------------------
        */

        Route::get('/documents', [
            AdminDocumentController::class,
            'index'
        ])->name('documents.index');

        Route::get('/documents/{document}', [
            AdminDocumentController::class,
            'show'
        ])->name('documents.show');

        Route::get('/documents/{document}/download', [
            AdminDocumentController::class,
            'download'
        ])->name('documents.download');

        Route::patch('/documents/{document}/approve', [
            AdminDocumentController::class,
            'approve'
        ])->name('documents.approve');

        Route::patch('/documents/{document}/reject', [
            AdminDocumentController::class,
            'reject'
        ])->name('documents.reject');

    });


/*
|--------------------------------------------------------------------------
| Google Meet
|--------------------------------------------------------------------------
|
| Only admin and consultant accounts can connect Google Meet.
|
*/

Route::middleware([
    'auth',
    'role:admin,consultant',
    'approved',
])->group(function () {

    Route::get('/auth/google/meet/connect', [
        GoogleMeetController::class,
        'connect'
    ])->name('google.meet.connect');

    Route::get('/auth/google/meet/callback', [
        GoogleMeetController::class,
        'callback'
    ])->name('google.meet.callback');

});


/*
|--------------------------------------------------------------------------
| Google Classroom OAuth
|--------------------------------------------------------------------------
|
| Admin, consultant and student accounts can connect Google Classroom.
|
| The GoogleClassroomController decides which OAuth scopes to use
| according to the user's role.
|
*/

Route::middleware([
    'auth',
    'role:admin,consultant,student',
    'approved',
])->group(function () {

    Route::get('/auth/google/classroom/connect', [
        GoogleClassroomController::class,
        'connect'
    ])->name('google.classroom.connect');

    Route::get('/auth/google/classroom/callback', [
        GoogleClassroomController::class,
        'callback'
    ])->name('google.classroom.callback');

});


/*
|--------------------------------------------------------------------------
| STUDENT PORTAL
|--------------------------------------------------------------------------
*/

Route::prefix('student')
    ->name('student.')
    ->middleware([
        'auth',
        'role:student',
        'approved',
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Student Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', function () {
            return view('Student.dashboard');
        })->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Student Profile
        |--------------------------------------------------------------------------
        */

        Route::get('/profile', [
            StudentProfileController::class,
            'show'
        ])->name('profile');

        Route::get('/profile/edit', [
            StudentProfileController::class,
            'edit'
        ])->name('profile.edit');

        Route::put('/profile', [
            StudentProfileController::class,
            'update'
        ])->name('profile.update');

        Route::get('/eligibility', [
            StudentProfileController::class,
            'eligibility'
        ])->name('eligibility');


        /*
        |--------------------------------------------------------------------------
        | University Matching
        |--------------------------------------------------------------------------
        */

        Route::get('/university-match', [
            UniversityMatchController::class,
            'index'
        ])->name('university-match');

        Route::get('/universities', [
            UniversityFinderController::class,
            'index'
        ])->name('universities.index');

        Route::get('/universities/{university}', [
            UniversityFinderController::class,
            'show'
        ])->name('universities.show');


        /*
        |--------------------------------------------------------------------------
        | Applications
        |--------------------------------------------------------------------------
        */

        Route::get('/applications', [
            ApplicationController::class,
            'index'
        ])->name('applications.index');

        Route::get('/applications/create/{university}', [
            ApplicationController::class,
            'create'
        ])->name('applications.create');

        Route::post('/applications', [
            ApplicationController::class,
            'store'
        ])->name('applications.store');

        Route::get('/applications/{application}', [
            ApplicationController::class,
            'show'
        ])->name('applications.show');

        Route::put('/applications/{application}', [
            ApplicationController::class,
            'update'
        ])->name('applications.update');


        /*
        |--------------------------------------------------------------------------
        | Application Payments
        |--------------------------------------------------------------------------
        */

        Route::get('/applications/{application}/payment', [
            StudentPaymentController::class,
            'create'
        ])->name('applications.payment');

        Route::post('/applications/{application}/payment', [
            StudentPaymentController::class,
            'store'
        ])->name('applications.payment.store');


        /*
        |--------------------------------------------------------------------------
        | Visa Procedure
        |--------------------------------------------------------------------------
        */

        Route::get('/visa', [
            VisaApplicationController::class,
            'index'
        ])->name('visa.index');

        Route::get('/applications/{application}/visa/create', [
            VisaApplicationController::class,
            'create'
        ])->name('visa.create');

        Route::post('/applications/{application}/visa', [
            VisaApplicationController::class,
            'store'
        ])->name('visa.store');

        Route::get('/visa/{visaApplication}', [
            VisaApplicationController::class,
            'show'
        ])->name('visa.show');

        Route::post('/visa/{visaApplication}/submit', [
            VisaApplicationController::class,
            'submit'
        ])->name('visa.submit');


        /*
        |--------------------------------------------------------------------------
        | Student Consultants
        |--------------------------------------------------------------------------
        */

        Route::get('/consultants', [
            PublicConsultantController::class,
            'index'
        ])->name('consultants.index');

        Route::get('/consultants/{consultant:slug}', [
            PublicConsultantController::class,
            'show'
        ])->name('consultants.show');


        /*
        |--------------------------------------------------------------------------
        | Consultant Booking
        |--------------------------------------------------------------------------
        */

        Route::get('/book-consultant', [
            BookingController::class,
            'create'
        ])->name('book-consultant');

        Route::post('/book-consultant', [
            BookingController::class,
            'store'
        ])->name('book-consultant.store');

        Route::get('/my-meetings', [
            BookingController::class,
            'studentIndex'
        ])->name('my-meetings');

        Route::get('/consultants/{consultant}/availability', [
            AvailabilityController::class,
            'studentAvailability'
        ])->name('consultants.availability');

        Route::get('/bookings', [
            BookingController::class,
            'studentIndex'
        ])->name('bookings.index');

        Route::get('/bookings/create', [
            BookingController::class,
            'create'
        ])->name('bookings.create');

        Route::post('/bookings', [
            BookingController::class,
            'store'
        ])->name('bookings.store');


        /*
        |--------------------------------------------------------------------------
        | Student Courses
        |--------------------------------------------------------------------------
        */

        Route::get('/courses', [
            CourseController::class,
            'index'
        ])->name('courses');

        Route::get('/courses/index', [
            CourseController::class,
            'index'
        ])->name('courses.index');

        Route::post('/courses/{course}/enroll', [
            CourseController::class,
            'enroll'
        ])->name('courses.enroll');

        Route::get('/my-courses', [
            CourseController::class,
            'myCourses'
        ])->name('my-courses.index');

        Route::patch('/my-courses/{enrollment}/progress', [
            CourseController::class,
            'updateProgress'
        ])->name('my-courses.update');

        Route::get('/courses/{course}', [
            CourseController::class,
            'show'
        ])->name('courses.show');

        Route::get('/my-courses/{course}', [
            CourseController::class,
            'show'
        ])->name('my-courses.show');

        Route::post('/my-courses/{course}/ai', [
            CourseController::class,
            'courseAi'
        ])->name('my-courses.ai');

        Route::post('/my-courses/{course}/modules/{module}/ai', [
            CourseController::class,
            'courseAi'
        ])->name('my-courses.module.ai');

        Route::get('/my-courses/{course}/modules/{module}', [
            CourseController::class,
            'module'
        ])->name('my-courses.module');

        Route::get(
            '/my-courses/{course}/modules/{module}/lessons/{lesson}',
            [
                CourseController::class,
                'lesson'
            ]
        )->name('my-courses.lesson');

        Route::post(
            '/my-courses/{course}/modules/{module}/lessons/{lesson}/complete',
            [
                CourseController::class,
                'completeLesson'
            ]
        )->name('my-courses.lesson.complete');


        /*
        |--------------------------------------------------------------------------
        | Student Messages / Contact Consultant
        |--------------------------------------------------------------------------
        */

        Route::get('/contact-consultant', [
            ConsultantMessageController::class,
            'create'
        ])->name('contact-consultant.create');

        Route::post('/contact-consultant', [
            ConsultantMessageController::class,
            'store'
        ])->name('contact-consultant.store');

        Route::get('/messages', [
            ConsultantMessageController::class,
            'studentIndex'
        ])->name('messages.index');

        Route::get('/messages/{conversation}', [
            ConsultantMessageController::class,
            'studentShow'
        ])->name('messages.show');

        Route::post('/messages/{conversation}', [
            ConsultantMessageController::class,
            'studentStore'
        ])->name('messages.store');


        /*
        |--------------------------------------------------------------------------
        | Student Google Classroom
        |--------------------------------------------------------------------------
        |
        | Students connect their own Google Classroom account.
        | After connection, they can view their courses, coursework
        | and their own submissions.
        |
        */

        Route::get('/classroom/courses', [
            GoogleClassroomController::class,
            'studentCourses'
        ])->name('classroom.courses');

        Route::get('/classroom/courses/{courseId}', [
            GoogleClassroomController::class,
            'studentCourse'
        ])->name('classroom.course');

        Route::get('/classroom/courses/{courseId}/assignments', [
            GoogleClassroomController::class,
            'studentCoursework'
        ])->name('classroom.course.assignments');

        /*
        |--------------------------------------------------------------------------
        | Student Google Classroom Assignment Detail
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/classroom/courses/{courseId}/assignments/{assignmentId}',
            [
                GoogleClassroomController::class,
                'studentAssignment'
            ]
        )->name('classroom.assignment');

    });


/*
|--------------------------------------------------------------------------
| CONSULTANT PORTAL
|--------------------------------------------------------------------------
*/

Route::prefix('consultant')
    ->name('consultant.')
    ->middleware([
        'auth',
        'role:consultant',
        'approved',
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Consultant Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            ConsultantController::class,
            'dashboard'
        ])->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Consultant Students
        |--------------------------------------------------------------------------
        */

        Route::get('/students', [
            ConsultantController::class,
            'students'
        ])->name('students');

        Route::get('/students/index', [
            ConsultantController::class,
            'students'
        ])->name('students.index');

        Route::get('/students/{student}', [
            ConsultantController::class,
            'showStudent'
        ])->name('students.show');


        /*
        |--------------------------------------------------------------------------
        | Consultant Visitors
        |--------------------------------------------------------------------------
        */

        Route::get('/visitors', [
            ConsultantVisitorController::class,
            'index'
        ])->name('visitors');

        Route::get('/visitors/{visitor}', [
            ConsultantVisitorController::class,
            'show'
        ])->name('visitors.show');

        Route::patch('/visitors/{visitor}/remarks', [
            ConsultantVisitorController::class,
            'updateRemarks'
        ])->name('visitors.remarks.update');

        Route::post('/visitors/{visitor}/lead', [
            ConsultantVisitorController::class,
            'updateLead'
        ])->name('visitors.lead');


        /*
        |--------------------------------------------------------------------------
        | Consultant Availability
        |--------------------------------------------------------------------------
        */

        Route::get('/availability', [
            AvailabilityController::class,
            'index'
        ])->name('availability.index');

        Route::post('/availability', [
            AvailabilityController::class,
            'store'
        ])->name('availability.store');

        Route::delete('/availability/{slot}', [
            AvailabilityController::class,
            'destroy'
        ])->name('availability.destroy');


        /*
        |--------------------------------------------------------------------------
        | Consultant Meetings / Bookings
        |--------------------------------------------------------------------------
        */

        Route::get('/meetings', [
            BookingController::class,
            'consultantIndex'
        ])->name('meetings');

        Route::patch('/meetings/{booking}', [
            BookingController::class,
            'updateStatus'
        ])->name('meetings.status');

        Route::get('/bookings', [
            BookingController::class,
            'consultantIndex'
        ])->name('bookings.index');

        Route::patch('/bookings/{booking}/status', [
            BookingController::class,
            'updateStatus'
        ])->name('bookings.status');


        /*
        |--------------------------------------------------------------------------
        | Consultant Google Classroom
        |--------------------------------------------------------------------------
        */

        Route::get('/classroom/courses', [
            GoogleClassroomController::class,
            'courses'
        ])->name('classroom.courses');

        Route::get('/classroom/courses/{courseId}', [
            GoogleClassroomController::class,
            'course'
        ])->name('classroom.course');

        Route::post('/classroom/courses/{courseId}/students', [
            GoogleClassroomController::class,
            'addStudent'
        ])->name('classroom.course.students.add');


        /*
        |--------------------------------------------------------------------------
        | Consultant Messages
        |--------------------------------------------------------------------------
        */

        Route::get('/messages', [
            ConsultantMessageController::class,
            'consultantIndex'
        ])->name('messages.index');

        Route::get('/messages/{conversation}', [
            ConsultantMessageController::class,
            'show'
        ])->name('messages.show');

        Route::post('/messages/{conversation}', [
            ConsultantMessageController::class,
            'store'
        ])->name('messages.store');

        Route::post('/messages/{message}/reply', [
            ConsultantMessageController::class,
            'reply'
        ])->name('messages.reply');


        /*
        |--------------------------------------------------------------------------
        | Consultant Profile
        |--------------------------------------------------------------------------
        */

        Route::get('/profile', [
            ConsultantProfileController::class,
            'show'
        ])->name('profile');

        Route::get('/profile/edit', [
            ConsultantProfileController::class,
            'edit'
        ])->name('profile.edit');

        Route::put('/profile', [
            ConsultantProfileController::class,
            'update'
        ])->name('profile.update');

    });


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
/*
|--------------------------------------------------------------------------
| Kapso Webhook
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\KapsoWebhookController;

Route::post('/webhooks/kapso', [
    KapsoWebhookController::class,
    'handle'
])->name('kapso.webhook');

