<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class GenerateRoutineController extends Controller
{

  public function weekday(Request $request)
  {   

            $timeRange= [
              0 =>["start"=>'05:00 pm',
              "end" => "06:00 pm"],
              1=>["start"=>'06:00 pm',
              "end" => "07:00 pm"],
              2 =>["start"=>'07:00 pm',
              "end" => "08:00 pm"],
              3 =>["start"=>'08:00 pm',
              "end" => "09:00 pm"],
                ];

              $weekDays = [
              1 => 'Sunday',
              2 => 'Monday',
              3 => 'Tuesday',
              4 => 'Wednesday',
              5=> 'Thursday',
              6 => 'Friday',
              7=> 'Saturday',
              ];
      $calendarData = [];
   
      $lessons   = DB::table('lms_batch_slot')
      ->select('lms_subject.lms_subject_name', 'lms_batch_slot.lms_batch_slot_id',
             'lms_batch_slot.lms_batch_slot_start_time',
             'lms_batch_slot.lms_batch_slot_end_time',
            'lms_batch_slot.lms_batch_slot_day',
            'lms_staff.lms_staff_first_name',
            'lms_staff.lms_staff_last_name',
            'lms_staff.lms_staff_id',
            'lms_batch_slot.lms_batch_slot_day',
            
            )
       ->join('lms_subject','lms_subject.lms_subject_id','lms_batch_slot.lms_subject_id')
       ->join('lms_staff', 'lms_batch_slot.lms_user_id', '=', 'lms_staff.lms_user_id')
      ->where('lms_batch_slot.lms_batch_id', '=', $request->batchId)
       ->get();
 
      foreach ($timeRange as $time)
      {
          $timeText = $time['start'] . ' - ' . $time['end'];
          $calendarData[$timeText] = [];

          foreach ($weekDays as $index => $day)
          {
              $lesson = $lessons->where('lms_batch_slot_day', $index)->where('lms_batch_slot_start_time',date('H:i', strtotime($time['start'])))->first();
   
              if ($lesson)
              {
                  array_push($calendarData[$timeText], [
                    'subject_name'   => $lesson->lms_subject_name,
                    'teacher_first_name' => $lesson->lms_staff_first_name,
                    'teacher_last_name' => $lesson->lms_staff_last_name,
                    'lms_staff_id' => $lesson->lms_staff_id,
                    'lms_batch_slot_day' => $lesson->lms_batch_slot_day,
                    'lms_batch_slot_start_time' => $lesson->lms_batch_slot_start_time,
                    'lms_batch_slot_end_time' => $lesson->lms_batch_slot_end_time,
                  ]);
              }
              else if (!$lessons->where('lms_batch_slot_day', $index)->where('lms_batch_slot_start_time', '<', date('H:i', strtotime($time['start'])))->where('lms_batch_slot_end_time', '>=', date('H:i', strtotime($time['end'])))->count())
              {
                  array_push($calendarData[$timeText], 1);
              }
              else
              {
                  array_push($calendarData[$timeText], 0);
              }
          }
      }

      return response()->json(['data'=>$calendarData]);
  }

  
 


  public function weekend1(){
    $currentDate = Carbon::now();
    $timeRange= [
      0 =>["start"=>'05:00 pm',
      "end" => "06:00 pm"],
      1=>["start"=>'06:00 pm',
      "end" => "07:00 pm"],
      2 =>["start"=>'07:00 pm',
      "end" => "08:00 pm"],
      3 =>["start"=>'08:00 pm',
      "end" => "09:00 pm"],
        ];
     
        $childcourses=DB::table('lms_child_course')
        ->select('lms_child_course_id','lms_child_course_name')
        ->where('lms_child_course.lms_child_course_is_active',1)->get();
        $new_array = $childcourses->pluck( 'lms_child_course_name' ,'lms_child_course_id')->toArray();


          $calendarData = [];

$getQuery = DB::table('lms_batch_slot')
->join('lms_subject', 'lms_batch_slot.lms_subject_id', '=', 'lms_subject.lms_subject_id')
->join('lms_staff', 'lms_batch_slot.lms_user_id', '=', 'lms_staff.lms_user_id')
->join('lms_batch_details', 'lms_batch_details.lms_batch_id', '=', 'lms_batch_slot.lms_batch_id')
->join('lms_child_course', 'lms_child_course.lms_child_course_id', '=', 'lms_batch_details.lms_child_course_id')


->where(function($q) use ($currentDate){
    if($currentDate->isSunday()){
        $q->where('lms_batch_slot.lms_batch_slot_day', '=', 1);
    }
    if($currentDate->isMonday()){
        $q->where('lms_batch_slot.lms_batch_slot_day', '=', 2);
    }
    if($currentDate->isTuesday()){
        $q->where('lms_batch_slot.lms_batch_slot_day', '=', 3);
    }
    if($currentDate->isWednesday()){
        $q->where('lms_batch_slot.lms_batch_slot_day', '=', 4);
    }
    if($currentDate->isThursday()){
        $q->where('lms_batch_slot.lms_batch_slot_day', '=', 5);
    }
    if($currentDate->isFriday()){
        $q->where('lms_batch_slot.lms_batch_slot_day', '=', 6);
    }
    if($currentDate->isSaturday()){
        $q->where('lms_batch_slot.lms_batch_slot_day', '=', 7);
    }
  })

->where('lms_batch_slot.lms_batch_slot_is_active', '=', 1)

->select([

    'lms_batch_slot.lms_batch_slot_id',
    'lms_batch_slot.lms_batch_slot_start_time',
    DB::raw('DATE_FORMAT(STR_TO_DATE(lms_batch_slot.lms_batch_slot_start_time, "%H:%i"), "%h:%i %p") as start_time'),
    'lms_batch_slot.lms_batch_slot_end_time',
    DB::raw('DATE_FORMAT(STR_TO_DATE(lms_batch_slot.lms_batch_slot_end_time, "%H:%i"), "%h:%i %p") as end_time'),
    'lms_batch_slot.lms_batch_slot_day_text',
    'lms_subject.lms_subject_name',
    'lms_subject.lms_subject_code',
    'lms_staff.lms_staff_first_name',
    'lms_staff.lms_staff_last_name',
    'lms_staff.lms_staff_full_name',
    'lms_child_course.lms_child_course_name',
    'lms_child_course.lms_child_course_id',

])
->whereYear('lms_batch_slot_created_at', date('Y'))


->get();

foreach ($timeRange as $time)
{

  $timeText = $time['start'] . ' - ' . $time['end'];
  $calendarData[$timeText] = [];
 

  foreach ($new_array as $index => $day)
          {
            
     $lesson = $getQuery->where('lms_child_course_id', $index)->where('lms_batch_slot_start_time',date('H:i', strtotime($time['start'])))->first();
       
              if ($lesson)
              {
                  array_push($calendarData[$timeText], [
                    'subject_name'   => $lesson->lms_subject_name,
                    'teacher_first_name' => $lesson->lms_staff_first_name,
                    'teacher_last_name' => $lesson->lms_staff_last_name,
                    'lms_batch_slot_start_time' => $lesson->lms_batch_slot_start_time,
                    'lms_batch_slot_end_time' => $lesson->lms_batch_slot_end_time,
                    'lms_child_course_name' => $lesson->lms_child_course_name,
                  ]);
              }
              else if (!$getQuery->where('lms_child_course_id', $index)->where('lms_batch_slot_start_time',date('H:i', strtotime($time['start'])))
              ->where('lms_batch_slot_end_time',date('H:i', strtotime($time['end'])))->count())
              {
                  array_push($calendarData[$timeText], 1);
              }
              else
              {
                  array_push($calendarData[$timeText], 0);
              }
          }

        }

return response()->json(['data'=>$calendarData]);
  
}


public function getAllSlot(Request $request)
{
    $lessons   = DB::table('lms_time_slot_table')
    ->select('lms_time_slot_table.slot_id',
           'lms_time_slot_table.slot_name',
           'lms_time_slot_table.slot_start_time',
           'lms_time_slot_table.slot_end_time',

          )
     
    ->get();

    return $lessons;
}
public function getIndividualSlot(Request $request)
{
    $getQuery   = DB::table('lms_time_slot_table')
    ->select('lms_time_slot_table.slot_id',
    'lms_time_slot_table.slot_name',
    'lms_time_slot_table.slot_start_time',
    'lms_time_slot_table.slot_end_time',

   )
    ->where('lms_time_slot_table.slot_id', '=', $request->slotId) 
    ->get();

    return $getQuery;
}




  }


