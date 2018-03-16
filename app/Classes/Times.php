<?php

namespace App\Classes;

class Times {
    public function getTimes($today){
        if($today){
            $h = (int)date('H') + 2;
        }else{
            $h = 0;
        }
        $times = array();
        for($i=$h;$i<36;$i++){
            if($i<10){
                $time12_s = '0'.$i.':00';
                $time24_s = '0'.$i.':00';
            }else{
                if($i > 23){
                    $j = $i - 24;
                    if($j < 10){
                        $time12_s = '0'.$j.':00';
                    }else{
                        $time12_s = $j.':00';
                    }
                }else{
                    $time12_s = $i.':00';
                }
                $time24_s = $i.':00';
            }
            $times[$time24_s] = $time12_s;
        }

        return $times;
    }

    public function getStartEndTime($book_date){
        $d1 = $book_date;
        $d2 = $this->getDateStr($d1, '+1 day');
        if($book_date == date('Ymd')){
            return array($d1 . '' . (int)date('H') + 2, $d2. '11');
        }else{
            return array($d1 . '00', $d2. '11');
        }
    }

    public function getTimeH24($today){
        if($today){
            return (int)date('H') + 2;
        }else{
            return 0;
        }
    }

    public function isToday($date){
        if($date == date('Ymd')){
            return true;
        }else{
            return false;
        }
    }

    public function getDateStr($ymd, $daysFactor){
        return date('Ymd', strtotime($daysFactor, strtotime($ymd)));
    }

    public function getDateJP($ymd){
        $date = date($ymd);
        $dayOfWeek = date('w', strtotime($date));
        $day_ja = '';
        switch($dayOfWeek){
            case '0':
                $day_ja = '日';
                break;
            case '1':
                $day_ja = '月';
                break;
            case '2':
                $day_ja = '火';
                break;
            case '3':
                $day_ja = '水';
                break;
            case '4':
                $day_ja = '木';
                break;
            case '5':
                $day_ja = '金';
                break;
            case '6':
                $day_ja = '土';
                break;
        }

        return substr($date, 0, 4) . '年' . substr($date, 4, 2) . '月' . substr($date, 6, 2) . '日 (' . $day_ja . ')';
    }
}