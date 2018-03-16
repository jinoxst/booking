@extends('layouts.layout')

@section('content')
<style>
/*thead tr {
    display: block;
    width: 100%;
}
tbody {
    width: 100%;
    height: 300px;
    overflow-y: auto;
    display: block;
}*/
.scrolling table {
    table-layout: inherit;
    margin-bottom:0;
}
.scrolling td {
    padding: 10px;
    min-width: 100px;
    text-align: center;
    font-weight: bold;
}
.scrolling th {
    padding: 10px;
    text-align: center;
    font-weight: bold;
}
table th {
    position: absolute;
    left: 0px;
    background-color:#C0C0C0;
    height:45px;
}
.outer {
    position: relative
}
.inner {
    overflow-x: auto;
    overflow-y: auto;
    margin-left: 80px;
    /*background-color:pink;*/
}
.td_blank {
    background-color: #000;
    cursor: pointer;
}
.hidden {
    display: none;
}
</style>
<script>
$(function(){
    $('#btn_next').hide();
    $('#btn_next').click(function(){
        var bookinfo = getBookTimeInfo();
        var bookinfoArr = bookinfo.split(',');
        var f = document.f;
        f.start_hour.value = bookinfoArr[1];
        f.start_hour24.value = bookinfoArr[2];
        f.stay_hour.value = bookinfoArr[3];
        f.seats_id.value = bookinfoArr[4];

        var ymd = '';
        if(f.start_hour24.value > f.start_hour.value) {
            ymd = getYmdNextDay(f.book_date.value);
            f.auto_nextday.value = 1;
        }else{
            ymd = getYmdDay(f.book_date.value);
            f.auto_nextday.value = 0;
        }
        var ymdArr = ymd.split(',');
        f.book_date.value = ymdArr[0];
        f.book_date_str.value = ymdArr[1];

        f.action='userinfo';
        f.submit();
    });
    $('#btn_back').click(function(){
        document.f.action='zones';
        document.f.submit();
    });
    $('table tr th').each(function(idx, el){
        $(this).css({'min-width':'80px'});
    });
    $('table tr td').each(function(idx, el){
        var seat_using = $(this).children('div.hidden[name=seats_using]').html();
        if(seat_using){
            if(seat_using == '1'){
                $(this).css('background-color','yellow');
                $(this).children('div[name=txt]').html('予約不可');
            }else{
                $(this).addClass('td_blank');
                $(this).click(function(){
                    /* blank:selectable, 1:selected, 2:other seats select ng */
                    var seats_selected = $(this).children('div.hidden[name=seats_selected]').html();
                    if(seats_selected && seats_selected == '2'){
                        return;//2:other seats select ng
                    }

                    var seats_id = $(this).children('div.hidden[name=seats_id]').html();
                    var timeIdxNew = parseInt($(this).children('div.hidden[name=time_idx]').html());
                    if(seats_selected && seats_selected == '1'){
                        if(checkerForSmallOrMax(seats_id, timeIdxNew)){
                            $(this).css('background-color','#000');
                            $(this).children('div.hidden[name=seats_selected]').html('');
                            $(this).children('div[name=txt]').html('');
                            reprintingHour(seats_id);
                        }
                    }else{
                        if(checkerForMoreSmallOrMoreMax(seats_id, timeIdxNew)){
                            var cnt = getSelectedCnt(seats_id) + 1;
                            $(this).children('div[name=txt]').html(cnt + 'H');
                            $(this).css('background-color','pink');
                            $(this).children('div.hidden[name=seats_selected]').html('1');
                            reprintingHour(seats_id);
                        }
                    }
                    otherSeatsSelectable(seats_id, getSelectedCnt(seats_id));
                });
            }
        }
    });
    var stay_hour = "{{ $stay_hour }}";
    if(stay_hour){
        var time_str24 = $('#start_hour24').val();
        var time_str24_arr = time_str24.split(':');
        var time24 = parseInt(time_str24_arr[0]);
        for(var i=0;i<stay_hour;i++){
          $('table tbody tr td div.hidden[name=seats_id]:contains({{ $seats_id }})').each(function(){
              var time_str24 = $(this).siblings('div.hidden[name=time_str24]').html();
              if(time_str24 == time24 + ':00'){
                  $(this).parent().trigger('click');
              }
          });
          time24++;
        }
    }
});

function reprintingHour(seats_id){
    $('table tbody tr td div.hidden[name=seats_selected]:contains(1)').each(function(idx){
        $(this).siblings('div[name=txt]').html((idx + 1) + 'H');
    });
}

function checkerForSmallOrMax(seats_id, timeIdxNew){
    var timeArr = [];
    $('table tr td div.hidden[name=seats_using]:contains(0)').each(function(idx, el){
        var other_seats_id = $(this).siblings('div.hidden[name=seats_id]').html();
        if(seats_id == other_seats_id){
            var seats_selected = $(this).siblings('div.hidden[name=seats_selected]').html();
            if(seats_selected && seats_selected == '1'){
                var timeIdx = parseInt($(this).siblings('div.hidden[name=time_idx]').html());
                timeArr.push(timeIdx);
            }
        }
    });

    if(timeArr && timeArr.length > 0){
        var timeIdxMin = timeArr[0];
        var timeIdxMax = timeArr[timeArr.length-1];

        if(timeIdxNew == timeIdxMin){
            return true;
        }

        if(timeIdxNew == timeIdxMax){
            return true
        }
    }else{
        return true;
    }

    return false;
}

function checkerForMoreSmallOrMoreMax(seats_id, timeIdxNew){
    var timeArr = [];
    $('table tr td div.hidden[name=seats_using]:contains(0)').each(function(idx, el){
        var other_seats_id = $(this).siblings('div.hidden[name=seats_id]').html();
        if(seats_id == other_seats_id){
            var seats_selected = $(this).siblings('div.hidden[name=seats_selected]').html();
            if(seats_selected && seats_selected == '1'){
                var timeIdx = parseInt($(this).siblings('div.hidden[name=time_idx]').html());
                timeArr.push(timeIdx);
            }
        }
    });

    if(timeArr && timeArr.length > 0){
        var timeIdxMin = timeArr[0];
        var timeIdxMax = timeArr[timeArr.length-1];

        if(timeIdxNew < timeIdxMin){
            if ((timeIdxMin - timeIdxNew) > 1){
                return false;
            }
        }

        if(timeIdxNew > timeIdxMax){
            if ((timeIdxNew - timeIdxMax) > 1){
                return false;
            }
        }
    }else{
        return true;
    }

    return true;
}

function getBookTimeInfo(){
    var cnt = 0;
    var startHour = '';
    var startHour24 = '';
    var startIdx = '';
    var seatsId = '';
    $('table tr td div.hidden[name=seats_using]:contains(0)').each(function(idx, el){
        var seats_selected = $(this).siblings('div.hidden[name=seats_selected]').html();
        if(seats_selected && seats_selected == '1'){
            var timeIdx = $(this).siblings('div.hidden[name=time_idx]').html();
            var timeStr = $(this).siblings('div.hidden[name=time_str]').html();
            var timeStr24 = $(this).siblings('div.hidden[name=time_str24]').html();
            seatsId = $(this).siblings('div.hidden[name=seats_id]').html();

            if(cnt == 0){
                startHour = timeStr;
                startHour24 = timeStr24;
                startIdx = timeIdx;
            }
            cnt++;
        }
    });

    return startIdx + ',' + startHour + ',' + startHour24 + ',' + cnt + ',' + seatsId;
}

function getSelectedCnt(seats_id){
    var cnt = 0;
    $('table tr td div.hidden[name=seats_using]:contains(0)').each(function(idx, el){
        var other_seats_id = $(this).siblings('div.hidden[name=seats_id]').html();
        if(seats_id == other_seats_id){
            var seats_selected = $(this).siblings('div.hidden[name=seats_selected]').html();
            if(seats_selected && seats_selected == '1'){
                cnt++;
            }
        }
    });

    return cnt;
}

function otherSeatsSelectable(seats_id, cnt){    
    var flag = false;
    if(cnt == 0){
        flag = true;
        $('#btn_next').hide();
    }else{
        $('#btn_next').show();
    }
    $('table tr td div.hidden[name=seats_using]:contains(0)').each(function(idx, el){
        var other_seats_id = $(this).siblings('div.hidden[name=seats_id]').html();
        if(seats_id != other_seats_id){
            if(flag === false){
                $(this).parent().css({'background-color':'#00f', 'cursor':'default', 'color':'#fff'});
                $(this).siblings('div[name=txt]').html('選択不可');
                $(this).siblings('div.hidden[name=seats_selected]').html('2');
            }else{
                $(this).parent().css({'background-color':'#000', 'cursor':'pointer', 'color':'#555'});
                $(this).siblings('div[name=txt]').html('');
                $(this).siblings('div.hidden[name=seats_selected]').html('');
            }
        }
    });
}
</script>
@include('shared.tenpo_title')
<p style='font-size:12px;'>TOP -> ルームタイプ選択 -> <font class='tltle_selector'>時間選択</font> -> 予約確認 -> 完了</p>
<br>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form name='f' method='POST' action=''>
{{ csrf_field() }}
ご利用日：{{ $book_date_str }}<br/>
ご利用人数：{{ $userscnt }}名<br/>
座席タイプ：{{ $zones_type_name }}
<br>
<div class="scrolling outer">
    <div class="inner">
        <table class="table table-bordered table-inverse table-fixed">
        <thead>
        <tr style='background-color:#C0C0C0;'>
            <th style='height:46px;width:80px;'></th>
        @foreach ($seats as $seat)
            <td style='font-weight:bold;'>席{{ $seat->seats_name }}</td>
        @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach ($times_seats as $time24 => $tvalue)
        <tr style='height:45px;'>
            <th style='background-color:#C0C0C0;'>{{ $controller->getFirstSeatsTime12($tvalue) }}</th>
            @foreach ($tvalue as $seat)
            <td><div name='txt'></div>
                <div class='hidden' name='seats_using'>{{ $seat->using_yn }}</div>
                <div class='hidden' name='seats_selected'></div>
                <div class='hidden' name='seats_id'>{{ $seat->seats_id }}</div>
                <div class='hidden' name='time_idx'>{{ $loop->parent->index }}</div>
                <div class='hidden' name='time_str'>{{ $seat->time12 }}</div>
                <div class='hidden' name='time_str24'>{{ $time24 }}</div>
            </td>
            @endforeach
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
</div>
<br>
<a class="btn btn-primary" href="#" role="button" id='btn_back'>« BACK</a>
<a class="btn btn-primary" href="#" role="button" id='btn_next'>NEXT »</a>
<input type='hidden' id='zones_id' name='zones_id' value='{{ $zones_id }}'/>
<input type='hidden' id='zones_type_name' name='zones_type_name' value='{{ $zones_type_name }}'/>
<input type='hidden' id='capacity' name='capacity' value='{{ $capacity }}'/>
<input type='hidden' id='userscnt' name='userscnt' value='{{ $userscnt }}'/>
<input type='hidden' id='book_date' name='book_date' value='{{ $book_date }}'/>
<input type='hidden' id='book_date_str' name='book_date_str' value='{{ $book_date_str }}'/>
<input type='hidden' id='start_hour' name='start_hour' value='{{ $start_hour }}'/>
<input type='hidden' id='start_hour24' name='start_hour24' value='{{ $start_hour24 }}'/>
<input type='hidden' id='auto_nextday' name='auto_nextday' value='{{ $auto_nextday }}'/>
<input type='hidden' id='stay_hour' name='stay_hour' value='{{ $stay_hour }}'/>
<input type='hidden' id='seats_id' name='seats_id' value='{{ $seats_id }}'/>
<input type='hidden' id='acc_cd' name='acc_cd' value='{{ $acc_cd }}'/>
<input type='hidden' id='shop_nm' name='shop_nm' value='{{ $shop_nm }}'/>
</form>
@endsection