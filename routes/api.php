<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});
Route::get('/viewclear', function () {
    Artisan::call('view:clear');
});
Route::get('/routeclear', function () {
    Artisan::call('route:clear');
});
Route::get('/configcache', function () {
    Artisan::call('config:cache');
});
Route::get('/cacheclear', function () {
    Artisan::call('cache:clear');
});
Route::get('/cachereset', function () {
    Artisan::call('cache:forget');
});

//For Mobile App
Route::post('checkUserStatus', 'AppController@checkUserStatus');
Route::post('checkMobileNumber', 'AppController@checkMobileNumber');
Route::post('checkLogin', 'AppController@checkLogin');
Route::post('updateDeviceToken', 'AppController@updateDeviceToken');
Route::post('getCommonAboutUs', 'AppController@getCommonAboutUs');
Route::post('getCourseList', 'AppController@getCourseList');
Route::post('getGalleryList', 'AppController@getGalleryList');

Route::get('sendStudentSignupOTP/{mobile}/{otp}', [AppController::class, 'sendStudentSignupOTP']);
Route::post('changePassword', 'AppController@changePassword');
Route::post('sendPassword', 'AppController@sendPassword');
Route::post('studentProfileImageUpload', 'AppController@studentProfileImageUpload');
Route::post('updateStudentProfileImage', 'AppController@updateStudentProfileImage');
Route::post('removeStudentProfileImage', 'AppController@removeStudentProfileImage');
Route::post('updateStudentFirstName', 'AppController@updateStudentFirstName');
Route::post('updateStudentLastName', 'AppController@updateStudentLastName');
Route::post('updateStudentMobileNumber', 'AppController@updateStudentMobileNumber');
Route::post('getStudentCourseMapping', 'AppController@getStudentCourseMapping');

Route::post('getExamScheduleCourseWise', 'AppController@getExamScheduleCourseWise');
Route::post('getExamCardByStudentCourseWise', 'AppController@getExamCardByStudentCourseWise');
Route::post('getExamInstruction', 'AppController@getExamInstruction');
Route::post('getExamQuestion', 'AppController@getExamQuestion');
Route::post('saveExam', 'AppController@saveExam');
Route::post('getExamReview', 'AppController@getExamReview');

Route::post('getExamAnalysisUserWise', 'AppController@getExamAnalysisUserWise');
Route::post('getExamAnalysisTopUser', 'AppController@getExamAnalysisTopUser');
Route::post('getExamAnalysisTopicWise', 'AppController@getExamAnalysisTopicWise');
Route::post('getExamAnswer', 'AppController@getExamAnswer');

Route::post('savePost', 'AppController@savePost');

Route::post('postImageUpload', 'AppController@postImageUpload');
Route::post('getPost', 'AppController@getPost');
Route::post('savePostLike', 'AppController@savePostLike');
Route::post('deletePost', 'AppController@deletePost');
Route::post('getPostComment', 'AppController@getPostComment');
Route::post('savePostComment', 'AppController@savePostComment');

Route::post('saveEnquiry', 'AppController@saveEnquiry');

Route::post('getOnlineCourseList', 'AppController@getOnlineCourseList');
Route::post('registerUser', 'AppController@registerUser');
Route::post('getNoticeList', 'AppController@getNoticeList');
Route::post('saveReadNotice', 'AppController@saveReadNotice');
Route::post('getBatchList', 'AppController@getBatchList');
Route::post('getBatchSchedule', 'AppController@getBatchSchedule');
Route::post('getSubject', 'AppController@getSubject');
Route::post('getTopic', 'AppController@getTopic');
Route::post('saveHomework', 'AppController@saveHomework');
Route::post('getAssignmentListForTeacher', 'AppController@getAssignmentListForTeacher');
Route::post('assignmentUploadByTeacher', 'AppController@assignmentUploadByTeacher');
Route::post('getUploadedAssignmentDocByTeacher', 'AppController@getUploadedAssignmentDocByTeacher');
Route::post('getSubmittedStudentListForAssignmentForTeacher', 'AppController@getSubmittedStudentListForAssignmentForTeacher');
Route::post('finishUploadAssignment', 'AppController@finishUploadAssignment');
Route::post('evaluateAssignment', 'AppController@evaluateAssignment');

Route::post('getUploadedAssignmentDocByStudentForTeacher', 'AppController@getUploadedAssignmentDocByStudentForTeacher');

Route::post('getAssignmentListForStudent', 'AppController@getAssignmentListForStudent');

Route::post('assignmentUploadByStudent', 'AppController@assignmentUploadByStudent');

Route::post('finishUploadAssignmentByStudent', 'AppController@finishUploadAssignmentByStudent');
Route::post('getBatchSlotBatchWise', 'AppController@getBatchSlotBatchWise');
Route::post('getStudentListBatchWise', 'AppController@getStudentListBatchWise');
Route::post('saveAttendance', 'AppController@saveAttendance');
Route::post('getAttendanceStudentWise', 'AppController@getAttendanceStudentWise');
Route::post('getChildCourse', 'AppController@getChildCourse');
Route::post('getOnlineChildCourse', 'AppController@getOnlineChildCourse');
Route::post('getLibrary', 'AppController@getLibrary');

Route::post('teacherProfileImageUpload', 'AppController@teacherProfileImageUpload');
Route::post('updateTeacherProfileImage', 'AppController@updateTeacherProfileImage');
Route::post('removeTeacherProfileImage', 'AppController@removeTeacherProfileImage');

Route::post('getVideoPlayList', 'AppController@getVideoPlayList');
Route::post('getVideo', 'AppController@getVideo');
Route::post('deleteAssignmentDoc', 'AppController@deleteAssignmentDoc');
Route::post('deleteAssignment', 'AppController@deleteAssignment');
Route::post('deleteStudentAssignmentDoc', 'AppController@deleteStudentAssignmentDoc');


// implemented to resolve the exam issue

Route::post('getExamQuestionAndInsertInExamResult', 'AppController@getExamQuestionAndInsertInExamResult');
Route::post('getExamQuestionNew', 'AppController@getExamQuestionNew');
Route::post('getExamResultByExamResultId', 'AppController@getExamResultByExamResultId');

Route::post('autoSubmitExam', 'AppController@autoSubmitExam');
Route::get('getAllTopic', 'AppController@getAllTopic');




//end



Route::post('logoutUser', 'AppController@logoutUser');

//End

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth',

], function ($router) {

    Route::post('web_login', 'UserController@validateLogin')
        ->name('web_login');
    Route::post('web_logout', 'UserController@logout')->name('web_logout');
    Route::post('web_token_refresh', 'UserController@tokenRefresh')->name('web_token_refresh');
    Route::post('web_get_logged_user_details_with_role_permission', 'UserController@getLoggedUserDetailsWithRolesPermission')->name('web_get_logged_user_details_with_role_permission');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Role']], function () {

    // Save Role
    Route::post('web_save_role', 'SystemSettingsController@saveRole')->name('web_save_role');

});
Route::group(['middleware' => ['auth:api', 'permission:Roles Permissions']], function () {

    // View All Roles
    Route::get('web_get_all_roles', 'SystemSettingsController@getAllRoles')->name('web_get_all_roles');

});

Route::group(['middleware' => ['auth:api', 'permission:Edit Role']], function () {

    // Edit Role
    Route::post('web_update_role', 'SystemSettingsController@updateRole')->name('web_update_role');

});

Route::group(['middleware' => ['auth:api', 'permission:Assign Permission']], function () {
    // View & Assign Permission
    Route::post('web_get_assigned_unassigned_permission_role_wise', 'SystemSettingsController@getAssignedUnAssignedPermissionRoleWise')->name('web_get_assigned_unassigned_permission_role_wise');
    Route::post('web_assign_permission_role_wise', 'SystemSettingsController@assignPermissionRoleWise')->name('web_assign_permission_role_wise');

});

Route::group(['middleware' => ['auth:api', 'permission:Department']], function () {

    // View All Department
    Route::get('web_get_all_departments', 'DepartmentController@getAllDepartments')->name('web_get_all_departments');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Department']], function () {

    // Save Department
    Route::post('web_save_department', 'DepartmentController@saveDepartment')->name('web_save_department');

});

Route::group(['middleware' => ['auth:api', 'permission:Edit Role']], function () {

    // Edit Department
    Route::post('web_update_department', 'DepartmentController@updateDepartment')->name('web_update_department');

});

Route::group(['middleware' => ['auth:api', 'permission:Designation']], function () {

    // View All Designation
    Route::get('web_get_all_designations', 'DesignationController@getAllDesignations')->name('web_get_all_designations');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Designation']], function () {

    // Save Designation
    Route::post('web_save_designation', 'DesignationController@saveDesignation')->name('web_save_designation');

});

Route::group(['middleware' => ['auth:api', 'permission:Edit Designation']], function () {

    // Edit Designation
    Route::post('web_update_designation', 'DesignationController@updateDesignation')->name('web_update_designation');

});

Route::group(['middleware' => ['auth:api', 'permission:Prefix']], function () {

    // View All Prefix
    Route::get('web_get_all_prefix', 'PrefixController@getAllPrefix')->name('web_get_all_prefix');

});

Route::group(['middleware' => ['auth:api', 'permission:Edit Prefix']], function () {

    // Save Prefix
    Route::post('web_save_prefix', 'PrefixController@savePrefix')->name('web_save_prefix');

});

Route::group(['middleware' => ['auth:api', 'permission:Staff']], function () {

    // View All Staff
    Route::get('web_get_all_staff', 'StaffController@getAllStaff')->name('web_get_all_staff');

});

Route::group(['middleware' => ['auth:api', 'permission:Staff']], function () {

    // View All Active Roles
    Route::get('web_get_all_active_roles_without_pagination', 'SystemSettingsController@getAllActiveRolesWithoutPagination')->name('web_get_all_active_roles_without_pagination');

});

Route::group(['middleware' => ['auth:api', 'permission:Edit Staff']], function () {

    // Enable disable staff
    Route::post('web_enable_disable_staff', 'StaffController@enableDisableStaff')->name('web_enable_disable_staff');

});

Route::group(['middleware' => ['auth:api']], function () {

    // Get Prefix Module Wise
    Route::get('web_get_prefix_module_wise', 'PrefixController@getPrefixModuleWise')->name('web_get_prefix_module_wise');

});

Route::group(['middleware' => ['auth:api']], function () {

    // Get Active Designation without pagination
    Route::get('web_get_active_designation_without_pagination', 'DesignationController@getActiveDesignationWithoutPagination')->name('web_get_active_designation_without_pagination');

});

Route::group(['middleware' => ['auth:api']], function () {

    // Get Active Department without pagination
    Route::get('web_get_active_department_without_pagination', 'DepartmentController@getActiveDepartmentWithoutPagination')->name('web_get_active_department_without_pagination');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Staff|Edit Staff']], function () {

    // save staff basic info
    Route::post('web_save_edit_staff_basic_info', 'StaffController@saveEditStaffBasicInfo')->name('web_save_edit_staff_basic_info');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Staff|Edit Staff']], function () {

    // Edit payroll info
    Route::post('web_edit_staff_payroll_info', 'StaffController@editStaffPayrollInfo')->name('web_edit_staff_payroll_info');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Staff|Edit Staff']], function () {

    // Edit payroll info
    Route::post('web_edit_staff_bank_details_info', 'StaffController@editStaffBankDetailsInfo')->name('web_edit_staff_bank_details_info');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Staff|Edit Staff']], function () {

    // Edit social link info
    Route::post('web_edit_staff_social_link_info', 'StaffController@editStaffSocialLinkInfo')->name('web_edit_staff_social_link_info');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Staff|Edit Staff']], function () {

    // Edit social link info
    Route::post('web_edit_staff_upload_document_info', 'StaffController@editStaffUploadDocumentInfo')->name('web_edit_staff_upload_document_info');

});

Route::group(['middleware' => ['auth:api', 'permission:Staff']], function () {

    // Get Staff Details ID wise
    Route::post('web_get_staff_details_id_wise', 'StaffController@getStaffDetailsIdWise')->name('web_get_staff_details_id_wise');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Course|Edit Course']], function () {

    // Save Edit Course
    Route::post('web_save_update_course', 'CourseController@saveUpdateCourse')->name('web_save_update_course');

});

Route::group(['middleware' => ['auth:api', 'permission:Staff']], function () {

    // View All Course
    Route::get('web_get_all_course', 'CourseController@getAllCourse')->name('web_get_all_course');

});

Route::group(['middleware' => ['auth:api', 'permission:Edit Course']], function () {

    // Enable disable course
    Route::post('web_enable_disable_course', 'CourseController@enableDisableCourse')->name('web_enable_disable_course');

});

Route::group(['middleware' => ['auth:api', 'permission:Subject']], function () {

    // View All Subject
    Route::get('web_get_all_subject', 'SubjectController@getAllSubject')->name('web_get_all_subject');
    // View All Active Courses for Subject Page
    Route::get('web_get_all_active_courses_without_pagination', 'SubjectController@getActiveCourseWithoutPagination')->name('web_get_all_active_courses_without_pagination');
    // Save Edit Subject
    Route::post('web_save_update_subject', 'SubjectController@saveUpdateSubject')->name('web_save_update_subject');
    // Enable disable Subject
    Route::post('web_enable_disable_subject', 'SubjectController@enableDisableSubject')->name('web_enable_disable_subject');

});

Route::group(['middleware' => ['auth:api', 'permission:Topic']], function () {

    // View All Subject
    Route::get('web_get_all_topic', 'TopicController@getAllTopic')->name('web_get_all_topic');

    // View All Active Courses for Subject Page
    Route::get('web_get_all_active_subject_based_on_course_without_pagination', 'TopicController@getActiveSubjectBasedOnCourseWithoutPagination')->name('web_get_all_active_subject_based_on_course_without_pagination');
    // Save Edit Subject
    Route::post('web_save_update_topic', 'TopicController@saveUpdateTopic')->name('web_save_update_topic');
    // Enable disable Subject
    Route::post('web_enable_disable_topic', 'TopicController@enableDisableTopic')->name('web_enable_disable_topic');

});

Route::group(['middleware' => ['auth:api', 'permission:Edit Information Source']], function () {

    // Enable disable Information Source
    Route::post('web_enable_disable_information_source', 'InformationSourceController@enableDisableInformationSource')->name('web_enable_disable_information_source');

});

Route::group(['middleware' => ['auth:api', 'permission:Add Information Source|Edit Information Source']], function () {

    // Save Edit Information source
    Route::post('web_save_update_information_source', 'InformationSourceController@saveUpdateInformationSource')->name('web_save_update_information_source');

});

Route::group(['middleware' => ['auth:api', 'permission:Staff']], function () {

    // View All Information Source
    Route::get('web_get_all_information_source', 'InformationSourceController@getAllInformationSource')->name('web_get_all_information_source');

});

Route::group(['middleware' => ['auth:api', 'permission:Staff']], function () {

    // View All Active Enquiry Sources
    Route::get('web_get_all_active_sources_without_pagination', 'SystemSettingsController@getAllActiveSourcesWithoutPagination')->name('web_get_all_active_sources_without_pagination');

});

Route::group(['middleware' => ['auth:api', 'permission:Staff']], function () {

    // View All Enquiry
    Route::get('web_get_all_enquiry', 'EnquiryController@getAllEnquiry')->name('web_get_all_enquiry');

});

Route::group(['middleware' => ['auth:api']], function () {
    // Get Active Courses without pagination
    Route::get('web_get_active_course_without_pagination', 'CourseController@getActiveCourseWithoutPagination')->name('web_get_active_course_without_pagination');
});

Route::group(['middleware' => ['auth:api']], function () {
    // Get Active Courses without pagination
    Route::get('web_get_active_school_without_pagination', 'SystemSettingsController@web_get_active_school_without_pagination')->name('web_get_active_school_without_pagination');
});

Route::group(['middleware' => ['auth:api', 'permission:Add Staff|Edit Staff']], function () {

    // save staff basic info
    Route::post('web_save_edit_enquiry_basic_info', 'EnquiryController@saveEditEnquiryBasicInfo')->name('web_save_edit_enquiry_basic_info');

});

//Exam------------------------------

Route::group(['middleware' => ['auth:api', 'permission:Exam Schedule']], function () {

    // View All Active Enquiry Sources
    Route::get('web_get_all_active_exam_type_without_pagination', 'ExamController@getAllActiveExamTypeWithoutPagination')->name('web_get_all_active_exam_type_without_pagination');

    //Get All Exam Schedule
    Route::get('web_get_all_exam_schedule', 'ExamController@getAllExamSchedule')->name('web_get_all_exam_schedule');

    // View All Active Enquiry Sources
    Route::get('web_get_all_active_instruction_without_pagination', 'ExamController@getAllActiveInstructionWithoutPagination')->name('web_get_all_active_instruction_without_pagination');

    // Get Active Schedule Type without pagination
    Route::get('web_get_active_schedule_type_without_pagination', 'ExamController@web_get_active_schedule_type_without_pagination')->name('web_get_active_schedule_type_without_pagination');

    // save Exam Schedule
    Route::post('web_save_edit_exam_schedule', 'ExamController@saveEditExamSchedule')->name('web_save_edit_exam_schedule');

    // Enable disable Exam schedule
    Route::post('web_enable_disable_exam_schedule', 'ExamController@enableDisableExamSchedule')->name('web_enable_disable_exam_schedule');

    // View All Instruction
    Route::get('web_get_all_instruction', 'ExamController@getAllInstruction')->name('web_get_all_instruction');

    // Save Instruction
    Route::post('web_save_instruction', 'ExamController@saveInstruction')->name('web_save_instruction');

    // Disable Instruction
    Route::post('web_enable_disable_instruction', 'ExamController@enableDisableInstruction')->name('web_enable_disable_instruction');

    // Active Topic Based on Subject
    Route::get('web_get_all_active_topic_based_on_subject_without_pagination', 'SystemSettingsController@getActiveTopicBasedOnSubject')->name('web_get_all_active_topic_based_on_subject_without_pagination');

    // Active Difficulty Level
    Route::get('web_get_active_difficulty_level_wp', 'QuestionBankController@getActiveDifficultyLevel')->name('web_get_active_difficulty_level_wp');
    // save Question Bank
    Route::post('web_save_edit_qestion_bank', 'QuestionBankController@saveEditQuestionBank')->name('web_save_edit_qestion_bank');

    //Get Question Bank
    Route::get('web_get_all_question_bank', 'QuestionBankController@getAllQuestionBank')->name('web_get_all_question_bank');
    //Get Question Bank CourseWise Based on Exam
    Route::get('web_get_all_question_bank_exam_wise', 'QuestionBankController@getAllQuestionBankExamWise')->name('web_get_all_question_bank_exam_wise');

    //Get Question & Maerks Added
    Route::get('web_get_question_marks_added_exam_wise', 'QuestionBankController@getAddedMarksQuestionExamWise')->name('web_get_question_marks_added_exam_wise');

    // save Question Bank
    Route::post('web_add_or_remove_question', 'QuestionBankController@AddRemoveQuestion')->name('web_add_or_remove_question');
    Route::post('web_delete_question_bank', 'QuestionBankController@deleteQuestonBank')->name('web_delete_question_bank');
});

Route::group(['middleware' => ['auth:api']], function () {

    Route::get('web_get_playlist', 'PlayListController@getPlaylist')->name('web_get_playlist');
    Route::get('web_get_course_list', 'PlayListController@getCourseList')->name('web_get_course_list');
    Route::get('web_get_subject_list', 'PlayListController@getSubjectList')->name('web_get_subject_list');
    Route::get('web_get_topic_list', 'PlayListController@getTopicList')->name('web_get_topic_list');
    Route::post('web_save_playlist', 'PlayListController@savePlaylist')->name('web_save_playlist');

});