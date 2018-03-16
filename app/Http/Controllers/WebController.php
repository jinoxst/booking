<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Classes\Times;
use App\Booking;
use App\Mail\BookingCompleted;
use Illuminate\Support\Facades\Mail;

class WebController extends Controller
{
    public $bootstrap_log_only = [
        'index',
        'calendar',
        'zones',
        'time',
        'userinfo',
        'userinfo2',
        'done'
    ];

    public function index()
    {
        // $classes = app('bootstrap-log-only');
        // Log::debug($classes);
        $shops = DB::table('shops')->where('STATUS', 0)->get();
        return view('index', ['shops' => $shops]);
    }

    public function calendar(Request $request)
    {
        // Log::info('*** old:'.var_export($request->old(), true));
        // Log::info('*** request:'.$request);
        $params = $request->all();
        // Log::info('*** params1:'.var_export($params, true));
        $oldArr = $request->old();
        if(isset($oldArr)){
            foreach($oldArr as $key => $value){
                if(isset($value)){
                    $params[$key] = $value;
                }
            }
        }

        if(isset($params['userscnt']) === false){
            $params['userscnt'] = '';
        }
        if(isset($params['book_date']) === false){
            $params['book_date'] = '';
        }
        if(isset($params['book_date_str']) === false){
            $params['book_date_str'] = '';
        }

        // Log::info('*** params2:'.var_export($params, true));

        return view('calendar', $params);
    }

    public function zones(Request $request)
    {
        $request->validate([
            'userscnt' => 'required|numeric',
            'book_date' => 'required|numeric',
        ]);

        $params = $request->all();
        $params['zones'] = DB::select('
            select a.id as zones_id, 
                   a.name, 
                   a.capacity, 
                   a.image,
                   count(b.id) as seats_cnt,
                   sum(case when b.using_yn=1 then 1 else 0 end) as seats_using_cnt
              from zones a, seats b
             where a.acc_cd=? 
               and a.acc_cd=b.acc_cd
               and a.status=0
               and a.capacity>=?
               and a.id=b.zones_id
             group by a.id
        ', [$params['acc_cd'], $params['userscnt']]);

        return view('zones', $params);
    }

    public function time(Request $request)
    {
        $times = new Times();

        $params = $request->all();
        if(isset($params['start_hour']) === false){
            $params['start_hour'] = '';
        }
        if(isset($params['start_hour24']) === false){
            $params['start_hour24'] = '';
        }
        if(isset($params['stay_hour']) === false){
            $params['stay_hour'] = '';
        }
        if(isset($params['auto_nextday']) === false){
            $params['auto_nextday'] = '';
        }else{
            if($params['auto_nextday'] == '1'){
                $params['book_date'] = $times->getDateStr($params['book_date'], '-1 day');
                $params['book_date_str'] = $times->getDateJP($params['book_date']);
            }
        }
        if(isset($params['seats_id']) === false){
            $params['seats_id'] = '';
        }
        Log::info($params);
        // dd($params);
        $qry = 'select id as seats_id,
                       zones_id,
                       name as seats_name,
                       using_yn
                  from seats a
                 where acc_cd=? 
                   and zones_id=?
                   and status = 0';
        if ($params['capacity'] != '1') {
            $qry .= ' and childseatsgrp is not null';
        }
        $params['seats'] = DB::select($qry, [$params['acc_cd'], $params['zones_id']]);
        // dd($params['seats']);

        $today = $times->isToday($params['book_date']);
        $timesArr = $times->getTimes($today);
        // dd($timesArr);
        $times_seats = array();
        foreach($timesArr as $time24 => $time12){
            $i = 0;
            foreach($params['seats'] as $seats){
                $seats = (array)$seats;
                $seats['time12'] = $time12;
                $seats = (object)$seats;
                $times_seats[$time24][$seats->seats_id] = $seats;
            }
        }
        $params['times_seats'] = $times_seats;
        // dd($params);

        list($start_time, $end_time) = $times->getStartEndTime($params['book_date']);
        $qry = "select book_date,
                       seats_id,
                       start_hour,
                       start_hour24,
                       stay_hour
                  from bookings 
                 where acc_cd=? 
                   and zones_id=?
                   and concat(book_date, start_hour) between ? and ?
                 order by book_date, seats_id, start_hour";
        $bookingDatas = DB::select($qry, [
            $params['acc_cd'],
            $params['zones_id'],
            $start_time,
            $end_time
        ]);
        Log::info('*** cnt : '.count($bookingDatas));
        foreach($bookingDatas as $bookingData){
            $seats_id = $bookingData->seats_id;
            if($bookingData->book_date == $params['book_date']){
                $time24 = $bookingData->start_hour;
            }else{
                $time24 = $bookingData->start_hour24;
            }
            
            $stay_hour = $bookingData->stay_hour;
            $time24_str = '';
            for($i=0;$i<$stay_hour;$i++){
                if(strlen($time24) == 1){
                    $time24_str = '0'.$time24;
                }else{
                    $time24_str = $time24;
                }
                $start_hour24 = $time24_str . ':00';
                // Log::info('1 start_hour:'.$start_hour24.', seats_id:'.$seats_id);
                if(isset($params['times_seats'][$start_hour24])){
                    // Log::info('2 start_hour:'.$start_hour24.', seats_id:'.$seats_id);
                    $seats = &$params['times_seats'][$start_hour24][$seats_id];
                    if($seats_id == $seats->seats_id){
                        $seats->using_yn = '1';
                    }
                }
                $time24++;
            }
        }

        $params['controller'] = $this;

        // dd($params);

        return view('time', $params);
    }

    public function getFirstSeatsTime12(array $arr){
        return reset($arr)->time12;
    }

    public function userinfo(Request $request)
    {
        $params = $request->all();
        $oldArr = $request->old();
        if(isset($oldArr)){
            foreach($oldArr as $key => $value){
                if(isset($value)){
                    $params[$key] = $value;
                }
            }
        }

        if(isset($params['users_name']) === false){
            $params['users_name'] = '';
        }
        if(isset($params['cellno']) === false){
            $params['cellno'] = '';
        }
        if(isset($params['email']) === false){
            $params['email'] = '';
        }

        // dd($params);

        return view('userinfo', $params);
    }

    public function userinfo2(Request $request)
    {
        $params = $request->all();
        $oldArr = $request->old();
        if(isset($oldArr)){
            foreach($oldArr as $key => $value){
                if(isset($value)){
                    $params[$key] = $value;
                }
            }
        }

        if(isset($params['users_name']) === false){
            $params['users_name'] = '';
        }
        if(isset($params['cellno']) === false){
            $params['cellno'] = '';
        }
        if(isset($params['email']) === false){
            $params['email'] = '';
        }
        // dd($params);

        return view('userinfo2', $params);
    }

    public function done(Request $request)
    {
        // dd($request->all());
        // $request->validate([
        //     'users_name' => 'required',
        //     'cellno' => 'required|numeric',
        //     'email' => 'required|email',
        // ]);

        $validator = Validator::make($request->all(), [
            'users_name' => 'required',
            'cellno' => 'required|numeric',
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return redirect('userinfo2')->withErrors($validator)->withInput();
        }

        $params = $request->all();
        // dd($params);

        $booking = new Booking;
        $booking->acc_cd = $params['acc_cd'];
        $booking->zones_id = $params['zones_id'];
        $booking->seats_id = $params['seats_id'];
        $booking->book_date = $params['book_date'];
        $booking->start_hour = $params['start_hour'];
        $booking->start_hour24 = $params['start_hour24'];
        $booking->auto_nextday = $params['auto_nextday'];
        $booking->stay_hour = $params['stay_hour'];
        $booking->users_name = $params['users_name'];
        $booking->cellno = $params['cellno'];
        $booking->email = $params['email'];
        $booking->save();

        $params['booking_id'] = $booking->id;

        $booking->book_date_str = $params['book_date_str'];
        $booking->userscnt = $params['userscnt'];
        $booking->zones_type_name = $params['zones_type_name'];
        //Synchronize Mail Sender OK
        Mail::to($booking->email)->send(new BookingCompleted($booking));
        //Queueing Mail Sender Format NG
        // Mail::to($booking->email)->queue(new BookingCompleted($booking));

        return view('done', $params);
    }
}