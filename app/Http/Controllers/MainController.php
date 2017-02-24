<?php
/**
 * Created by PhpStorm.
 * User: plume
 * Date: 2017/2/14
 * Time: 16:43
 */
namespace App\Http\Controllers;

use App\Footprint;
use App\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MainController extends Controller {
    public function __construct()
    {
        //
    }

    public function getGoalDetail($goal_id) {
        $user = Auth::user();
        $goal = Goal::where('id', $goal_id)->where('user_id', $user->id)->first();
        if($goal) {
            $today = date("Y-m-d ", time());
//            $today_footprints = Footprint::where('goal_id', $goal->id)
//                ->where('created_at', '>=', $today."00:00:00")
//                ->where('created_at', '<=', $today."23:59:59")
//                ->orderBy('id', 'asc')
//                ->get();
            $footprints = $this->getMonthlyFootprints($goal_id, date('Y', time()), date('m',time()));
        }
        return ['code'=>0, 'data'=>['goal'=>$goal,'footprints'=>$footprints['data']]];
    }

    public function getMonthlyFootprints($goal_id, $year, $month) {
        $user = Auth::user();
        $goal = Goal::where('id', $goal_id)->where('user_id', $user->id)->first();
        if(empty($goal)) {
            return ['code'=>1, 'msg'=>"invalid goal id"];
        }
        $date = $year."-".$month."-";
        $days_number = [31,28,31,30,31,30,31,31,30,31,30,31];
        if(($year%4==0 && $year%100!=0) || $year%40==0) {
            $days_number[1] += 1;
        }
        $result = Footprint::select(DB::raw('id, created_at, UNIX_TIMESTAMP(created_at) as checked_at'))
            ->where('goal_id', $goal->id)
            ->where('created_at', '>=', $date."01 00:00:00")
            ->where('created_at', '<=', $date.$days_number[intval($month)-1]." 23:59:59")
            ->orderBy('id', 'asc')
            ->get();
        $footprints = [];
        foreach($result as $v) {
            $t = date('Ymd', $v['checked_at']);
            $footprints[$t][] = $v;
        }
        return ['code'=>0, 'data'=>$footprints];
    }

    public function getDailyFootprints($goal_id, $year, $month, $day_of_m) {
        $user = Auth::user();
        $goal = Goal::where('id', $goal_id)->where('user_id', $user->id)->first();
        if(empty($goal)) {
            return ['code'=>1, 'msg'=>"invalid goal id"];
        }
        $date = $year."-".$month."-".$day_of_m." ";
        $footprints = Footprint::where('goal_id', $goal->id)
            ->where('created_at', '>=', $date."00:00:00")
            ->where('created_at', '<=', $date."23:59:59")
            ->orderBy('id', 'asc')
            ->get();
        return ['code'=>0, 'data'=>$footprints];
    }

    public function getGoalList() {
        $user = Auth::user();
        $goals = Goal::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        return ['code'=>0, 'data'=>$goals];
    }

    public function addGoal(Request $request) {
        $user = Auth::user();
        $title = $request->input('title');
        $note = $request->input('note');
        $color_list = ['fd9494','ffe13e','6ce66e','2bd8d2','5ec9ff', 'c6a0ff', 'fda1de'];

        if(empty($title)) {
            return ['code'=>1, 'msg'=>'invalid title'];
        }

        $new_goal = new Goal();
        $new_goal->user_id = $user->id;
        $new_goal->name = $title;
        $new_goal->color = $color_list[random_int(0,count($color_list))];
        if(!empty($note)) {
            $new_goal->note = $note;
        }
        $result = $new_goal->save();
        if($result) {
            return ['code'=>0, 'msg'=>'success'];
        } else {
            return ['code'=>2, 'msg'=>'db error'];
        }
    }

    public function checkIn($goal_id) {
        $user = Auth::user();
        $goal = Goal::where('id', $goal_id)->where('user_id', $user->id)->first();
        if(empty($goal)) {
            return ['code'=>1,'msg'=>'invalid goal id'];
        }
        $new_footprint = new Footprint();
        $new_footprint->user_id = $user->id;
        $new_footprint->goal_id = $goal->id;
        $new_footprint->index_number = $goal->footprints()->count() + 1;
        $new_footprint->created_at = date('Y-m-d H:i:s', time());
        $result = $new_footprint->save();
        if($result) {
            return ['code'=>0, 'msg'=>'success'];
        } else {
            return ['code'=>2, 'msg'=>'db error'];
        }
    }

    public function getCheckedDaysInWeek($goal_id) {
        $now = time();
        $t = date('N', $now)-1;
        // 86400 = 24 * 3600
        $monday = date('Y-m-d 00:00:00', $now-$t*86400);
        $sunday = date('Y-m-d 23:59:59', $now+(7-$t)*86400);
        $footprints = Footprint::select(DB::raw('UNIX_TIMESTAMP(created_at) as checked_at'))
            ->where('goal_id', $goal_id)
            ->where('created_at', '>=', $monday)
            ->where('created_at', '<=', $sunday)
            ->orderBy('id', 'asc')
            ->get();
        $days = [];
        foreach ($footprints as $v) {
            $days[date('d', $v->checked_at)] = true;
        }
        return $days;
    }

    public function getCheckedInDays($goal_id, $year, $month) {
        $year = intval($year);
        $month = intval($month);
        $user = Auth::user();
        $goal = Goal::where('id', $goal_id)->where('user_id', $user->id)->first();
        if(empty($goal)) {
            return ['code'=>1,'msg'=>'invalid goal id'];
        }
        $star_date = $year."-".sprintf("%02d", $month)."-01 00:00:00";
        $month = $month>11?$month-11:$month+1;
        $end_date = $year."-".sprintf("%02d", $month)."-01 00:00:00";
        $footprints = Footprint::select(DB::raw('UNIX_TIMESTAMP(created_at) as checked_at'))
            ->where('goal_id', $goal->id)
            ->where('created_at', '>=', $star_date)
            ->where('created_at', '<', $end_date)
            ->orderBy('id', 'asc')
            ->get();
        $days = [];
        foreach ($footprints as $v) {
            $days[date('d', $v->checked_at)] = true;
        }
        return ['code'=>0, 'data'=>$days];
    }
}