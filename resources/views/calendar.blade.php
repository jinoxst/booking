@extends('layouts.layout')

@section('content')
<script>
$(function(){
    var book_date = "{{ $book_date }}";
    if(book_date){
        $('#book_date').val(book_date);
        $('#book_date_str').val("{{ $book_date_str }}");
        $('#cal_trigger').html("{{ $book_date_str }}");
    }
    var userscnt = "{{ $userscnt }}";
    if(userscnt){
        $('[name=userscnt]').val(userscnt);
    }
    $("#cal_trigger").click(function(){
        $("#datepicker").datepicker({
            minDate:"+0d",
            maxDate:"+1m",
            onSelect: function (date) {
                var d = $(this).datepicker('getDate');
                var dayOfWeek = d.getUTCDay() == 6 ? -1 : d.getUTCDay();
                $('#book_date').val(date.replace(/\//g, ''));
                var dateArr = date.split('/');
                var trg = getYmdDay(dateArr[0] + '' + dateArr[1] + '' + dateArr[2]);
                var trgArr = trg.split(',');
                var trgStr = trgArr[1];
                $('#cal_trigger').html(trgStr);
                $('#book_date_str').val(trgStr);
            }
        });
        $("#datepicker").datepicker("show");
    });
    $('#btn_back').click(function(){
        document.f.action = '/';
        document.f.method = 'GET';
        document.f.submit();
    });
    $('#btn_next').click(function(){
        document.f.submit();
    });
});
</script>
@include('shared.tenpo_title')
<p style='font-size:12px;'><font class='tltle_selector'>TOP</font> -> ルームタイプ選択 -> 時間選択 -> 予約確認 -> 完了</p>
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
<form name='f' method='POST' action='zones'>
{{ csrf_field() }}
<input type="hidden" id="datepicker">
<font color='red'>*</font>ご利用日：<div id="cal_trigger" class='cal_trigger'>選択して下さい▼</div>
<br/>
<font color='red'>*</font>ご利用人数：<br/>
<select name='userscnt'>
    <option value=''>選択してください</option>
    <option value='1'>1名</option>
    <option value='2'>2名</option>
    <option value='3'>3名</option>
</select>

<br><br>
<font color='red'>*は必須です。</font>
<br>
<a class="btn btn-primary" href="#" role="button" id='btn_back'>« BACK</a>
<a class="btn btn-primary" href="#" role="button" id='btn_next'>NEXT »</a>
<input type='hidden' id='book_date' name='book_date'/>
<input type='hidden' id='book_date_str' name='book_date_str'/>
<input type='hidden' id='acc_cd' name='acc_cd' value='{{ $acc_cd }}'/>
<input type='hidden' id='shop_nm' name='shop_nm' value='{{ $shop_nm }}'/>
</form>
@endsection