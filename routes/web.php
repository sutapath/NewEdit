<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RemoveRoleFromUserController;
use App\Http\Controllers\RevokePermissionFromRoleController;
use App\Http\Controllers\RevokePermissionFromUserController;
use App\Http\Controllers\RevokePermissionsFromRoleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ActivityInformationController;
use App\Http\Controllers\ActivityRegisterController;
use App\Http\Controllers\ActivitySaveController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\DocSendController;
use App\Http\Controllers\OfficerController; 



// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });
use App\Http\Controllers\ApplyScholarController;
use App\Http\Controllers\ScholarshipApplicationsController;
use App\Http\Controllers\UserLineNotifyController;
use App\Http\Controllers\LineNotifyController;
use App\Http\Controllers\PublishRequestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\IntlscholarController; 
use App\Http\Controllers\MemberImportController; 
Route::get('/', [ScholarshipController::class, 'Landing']);
Route::post('/members/import', [MemberImportController::class, 'import'])->name('members.import');
Route::resource('intlscholars', IntlscholarController::class);
Route::resource('members', MemberController::class);
Route::resource('students', StudentController::class);
Route::resource('publish_requests', PublishRequestController::class);
Route::resource('docs_sends', DocSendController::class);
Route::resource('users', UserController::class);
Route::resource('activitysave', UserController::class);
Route::resource('activities', ActivityInformationController::class);
Route::resource('registrations', ActivityRegisterController::class);
Route::resource('saves', ActivitySaveController::class);
Route::resource('scholarship_applications', ScholarshipApplicationsController::class);
Route::resource('scholarships', ScholarshipController::class);
Route::get('/users-with-roles', [UserController::class, 'getUsersWithRoles']);
Route::resource('profiles', ProfileController::class);
Route::resource('/users', UserController::class);
Route::resource('/roles', RoleController::class);
Route::resource('/permissions', PermissionController::class);
 
Route::delete('/roles/{role}/permissions/{permission}', RevokePermissionFromRoleController::class)
    ->name('roles.permissions.destroy');
Route::delete('/users/{user}/permissions/{permission}', RevokePermissionFromUserController::class)
    ->name('users.permissions.destroy');
Route::delete('/users/{user}/roles/{role}', RemoveRoleFromUserController::class)
    ->name('users.roles.destroy');
// Route::put('/users/update-roles', [UserController::class, 'updateRoles'])->name('users.updateRoles');

// Route::get('/dashboard', function () {
//     // return Inertia::render('Dashboard');
//     Route::get('/dashboard', [ScholarshipController::class, 'dashBoard']);
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ScholarshipController::class, 'dashBoard'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::put('/users/update-roles', [UserController::class, 'updateRoles'])->name('users.updateRoles');
Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
Route::get('/users/{user}/edit-scholar', [UserController::class, 'editscholar'])->name('users.editscholar');
Route::put('/users/{user}/update-scholar', [UserController::class, 'updatescholar'])->name('users.updatescholar');
Route::get('/scholarships', [ScholarshipController::class, 'index'])->name('scholarships.index');
Route::get('/scholarships/create', [ScholarshipController::class, 'create'])->name('scholarships.create');
Route::post('/scholarships', [ScholarshipController::class, 'store'])->name('scholarships.store');
    Route::get('/scholarships/{scholarship}', [ScholarshipController::class, 'show'])->name('scholarships.show');
    Route::get('/scholarships/{scholarship}/edit', [ScholarshipController::class, 'edit'])->name('scholarships.edit');
Route::post('/scholarships/{scholarship}', [ScholarshipController::class, 'update'])->name('scholarships.update');
Route::delete('/scholarships/{scholarship}', [ScholarshipController::class, 'destroy'])->name('scholarships.destroy');

Route::get('/scholarship-applications', [ScholarshipApplicationsController::class, 'index'])->name('scholarship_applications.index');
Route::get('/scholarship-applications/create', [ScholarshipApplicationsController::class, 'intanilcreate'])->name('scholarship_applications.intanilcreate');
Route::get('/scholarship-applications/creates', [ScholarshipApplicationsController::class, 'pertermcreate'])->name('scholarship_applications.pertermcreate');
Route::post('/scholarship-applications', [ScholarshipApplicationsController::class, 'store'])->name('scholarship_applications.store');
Route::get('/scholarship-applications/{application}', [ScholarshipApplicationsController::class, 'show'])->name('scholarship_applications.show');
Route::get('/scholarship-applications/{application}/intanil', [ScholarshipApplicationsController::class, 'intanil'])->name('scholarship_applications.intanil');
Route::get('/scholarship-applications/{application}/perterm', [ScholarshipApplicationsController::class, 'perterm'])->name('scholarship_applications.perterm');
Route::post('/scholarship-applications/{application}', [ScholarshipApplicationsController::class, 'update'])->name('scholarship_applications.update');
Route::put('/scholarship-application/{application}', [ScholarshipApplicationsController::class, 'check'])->name('scholarship_application.check'); 
Route::delete('/scholarship-applications/{application}', [ScholarshipApplicationsController::class, 'destroy'])->name('scholarship_applications.destroy'); 
Route::get('/interview/scholarship-applications', [ScholarshipApplicationsController::class, 'interview'])->name('scholarship_applications.interview');
Route::put('/interviewapprove/scholarship-applications/{application}', [ScholarshipApplicationsController::class, 'interviewedit'])->name('scholarship_application.interviewedit');
Route::get('/interviewdetail/scholarship-applications/{application}', [ScholarshipApplicationsController::class, 'interviewdetail'])->name('scholarship_applications.interviewdetail');
Route::get('/admin/scholarship-applications', [ScholarshipApplicationsController::class, 'indexadmin'])->name('scholarship_applications.indexadmin');
Route::get('/admin/scholarstudents', [ScholarshipApplicationsController::class, 'scholarstudents'])->name('scholarship_applications.scholarstudents');
Route::get('/admin/AllStudents', [ScholarshipApplicationsController::class, 'AllStudents'])->name('scholarship_applications.AllStudents');
Route::get('/apply-scholars/admin-index', [ScholarshipApplicationsController::class, 'index'])->name('applications.index'); // อันนี้
Route::get('/user/activity-saves/{user_id}', [ScholarshipApplicationsController::class, 'userSaves'])->name('activity_saves.user');
Route::get('/admin/report', [ScholarshipApplicationsController::class, 'secoundreport'])->name('scholarship_applications.secoundreport');
Route::post('/Update/interviewResults', [ScholarshipApplicationsController::class, 'interviewResults'])->name('scholarship_applications.interviewResults');
Route::get('/ContractsDetail/{application}', [ScholarshipApplicationsController::class, 'ContractsDetail'])->name('scholarship_applications.ContractsDetail');
Route::get('/AddContract/{application}', [ScholarshipApplicationsController::class, 'AddContract'])->name('scholarship_applications.AddContract');
    // ใช้ PUT หรือ PATCH สำหรับการอัปเดต
Route::post('/ContractsSave/{id}', [ScholarshipApplicationsController::class, 'Contracts'])->name('Contracts.Save');
Route::post('/deleteContracts/{id}', [ScholarshipApplicationsController::class, 'deleteContracts'])->name('Contracts.deleteContracts');

    Route::get('activities', [ActivityInformationController::class, 'index'])->name('activities.index');
Route::get('activities/create', [ActivityInformationController::class, 'create'])->name('activities.create');
Route::post('activities', [ActivityInformationController::class, 'store'])->name('activities.store');
Route::get('activities/{activity}/edit', [ActivityInformationController::class, 'edit'])->name('activities.edit');
Route::put('activities/{activity}', [ActivityInformationController::class, 'update'])->name('activities.update');
Route::post('activities/{activity}', [ActivityInformationController::class, 'destroy'])->name('activities.destroy');

Route::get('activity-registrations', [ActivityRegisterController::class, 'index'])->name('activity_registrations.index');
Route::post('activity-registrations', [ActivityRegisterController::class, 'store'])->name('activity_registrations.store');
Route::delete('activity-registrations/{registration}', [ActivityRegisterController::class, 'destroy'])->name('activity_registrations.destroy');
Route::get('/activity-registrations/create', [ActivityRegisterController::class, 'create'])->name('activity_registrations.create');
Route::get('/activities/{registration}/edit', [ActivityRegisterController::class, 'edit'])->name('activity_registrations.edit');
Route::put('/activity/{registration}', [ActivityRegisterController::class, 'update'])->name('activity_registrations.update');
Route::patch('/activity-registrations/{registration}', [ActivityRegisterController::class, 'updatecheck'])->name('activity_registrations.updatecheck');
 

Route::get('activity-saves', [ActivitySaveController::class, 'index'])->name('activity_saves.index');
Route::get('activity-saves/create', [ActivitySaveController::class, 'create'])->name('activity_saves.create');
Route::get('activity-saves/creates', [ActivitySaveController::class, 'creates'])->name('activity_saves.creates');
Route::post('activity-saves', [ActivitySaveController::class, 'store'])->name('activity_saves.store');
Route::delete('activity-saves/{save}', [ActivitySaveController::class, 'destroy'])->name('activity_saves.destroy');
Route::get('activity-saves/{save}/edit', [ActivitySaveController::class, 'edit'])->name('activity_saves.edit');
Route::post('activity-saves/{save}', [ActivitySaveController::class, 'update'])->name('activity_saves.update'); 
Route::get('activity-saves/{save}/show', [ActivitySaveController::class, 'show'])->name('activity_saves.show'); 
Route::put('activity-saves/check/{save}', [ActivitySaveController::class, 'updatecheck'])->name('activity_saves.updatecheck');  
Route::get('/activity-report', [ActivitySaveController::class, 'summary'])->name('scholarship_applications.report');



Route::get('/docs_sends', [DocSendController::class, 'index'])->name('docs_sends.index');
Route::get('/docs_sends/create', [DocSendController::class, 'create'])->name('docs_sends.create');
Route::post('/docs_sends', [DocSendController::class, 'store'])->name('docs_sends.store');
Route::get('/docs_sends/{docSend}', [DocSendController::class, 'show'])->name('docs_sends.show');
Route::get('/docs_sends/{docSend}/edit', [DocSendController::class, 'edit'])->name('docs_sends.edit');
Route::post('/docs_sends/{docSend}', [DocSendController::class, 'update'])->name('docs_sends.update');

Route::delete('/docs_sends/{docSend}', [DocSendController::class, 'destroy'])->name('docs_sends.destroy');

Route::put('/docs_sends/{docSend}/check', [DocSendController::class, 'check'])->name('docs_sends.check');
Route::get('/user_line_notify', [UserLineNotifyController::class, 'index'])->name('user_line_notify.index');
Route::get('/user_line_notify/create', [UserLineNotifyController::class, 'create'])->name('user_line_notify.create');
Route::post('/user_line_notify', [UserLineNotifyController::class, 'store'])->name('user_line_notify.store');
Route::get('/user_line_notify/{id}', [UserLineNotifyController::class, 'show'])->name('user_line_notify.show');
Route::get('/user_line_notify/{id}/edit', [UserLineNotifyController::class, 'edit'])->name('user_line_notify.edit');
Route::put('/user_line_notify/{id}', [UserLineNotifyController::class, 'update'])->name('user_line_notify.update');
Route::delete('/user_line_notify/{id}', [UserLineNotifyController::class, 'destroy'])->name('user_line_notify.destroy');

Route::post('/line-notify/{userId}', [LineNotifyController::class, 'sendNotification'])->name('line.notify');
Route::post('/line-notify', [LineNotifyController::class, 'notify'])->name('notify');

    Route::post('/line-notify/send-to-all', [LineNotifyController::class, 'sendNotificationAll'])->name('line-notify.send-to-all');
    Route::get('/docs_sends/{id}', [PublishRequestController::class, 'show']);
    Route::post('/publish_requests/createInfo', [PublishRequestController::class, 'createInfo'])->name('publish_requests.createInfo');
    Route::get('/publish_requests/EditInfo/{id}', [PublishRequestController::class, 'EditInfo'])->name('publish_requests.EditInfo');
    // Route::post('/publish_requests/show', [PublishRequestController::class, 'show'])->name('publish_requests.show');
    Route::post('/publish_requests/create', [PublishRequestController::class, 'create'])->name('publish_requests.create');
    Route::post('/publish_requests/sends', [PublishRequestController::class, 'store'])->name('publish_requests.store');



    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::post('/importUsers', [UserController::class, 'importUsers'])->name('students.importUsers');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
Route::post('/import-users', [StudentController::class, 'import'])->name('students.import');



Route::get('/members', [MemberController::class, 'index'])->name('members.index');
Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
Route::post('/members', [MemberController::class, 'store'])->name('members.store');
Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
 
Route::get('/officers', [OfficerController::class, 'index'])->name('officers.index');
Route::get('/officers/create', [OfficerController::class, 'create'])->name('officers.create');
Route::post('/officers', [OfficerController::class, 'store'])->name('officers.store');
Route::get('/officers/{officer}', [OfficerController::class, 'show'])->name('officers.show');
Route::get('/officers/{officer}/edit', [OfficerController::class, 'edit'])->name('officers.edit');
Route::put('/officers/{officer}', [OfficerController::class, 'update'])->name('officers.update');
Route::delete('/officers/{officer}', [OfficerController::class, 'destroy'])->name('officers.destroy');

Route::get('/intlScholars', [IntlScholarController::class, 'index'])->name('intlScholars.index');
Route::get('/intlScholars/create', [IntlScholarController::class, 'create'])->name('intlScholars.create');
Route::post('/intlScholars', [IntlScholarController::class, 'store'])->name('intlScholars.store');
Route::get('/intlScholars/{intlScholar}', [IntlScholarController::class, 'show'])->name('intlScholars.show');
Route::get('/intlScholars/{intlScholar}/edit', [IntlScholarController::class, 'edit'])->name('intlScholars.edit');
Route::put('/intlScholars/{intlScholar}', [IntlScholarController::class, 'update'])->name('intlScholars.update');
Route::delete('/intlScholars/{intlScholar}', [IntlScholarController::class, 'destroy'])->name('intlScholars.destroy');

 
});

Route::middleware(['auth', 'role:admin'])->group(function () {
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::put('/users/update-roles', [UserController::class, 'updateRoles'])->name('users.updateRoles');

});

require __DIR__ . '/auth.php';
