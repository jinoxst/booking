@extends('layouts.layout')

@section('content')
<script>
$(function(){
    $('#btn_back').click(function(){
        document.f.action='calendar';
        document.f.submit();
    });
    $('a[name=seat_usable]').click(function(){
        var zones_info = $(this).siblings('input:hidden[name=zones_info]').val();
        var zones_info_arr = zones_info.split(':');
        $('#zones_id').val(zones_info_arr[0]);
        $('#zones_type_name').val(zones_info_arr[1]);
        $('#capacity').val(zones_info_arr[2]);
        document.f.action='time';
        document.f.submit();
    });
});
</script>
@include('shared.tenpo_title')
<p style='font-size:12px;'>TOP -> <font class='tltle_selector'>ルームタイプ選択</font> -> 時間選択 -> 予約確認 -> 完了</p>
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
<form name='f' method='POST'>
{{ csrf_field() }}
ご利用日：{{ $book_date_str }}<br/>
ご利用人数：{{ $userscnt }}名<br/>
<br>
@if (count($zones) > 0)
    <table class="table table-bordered table-inverse">
    <tbody>
    @foreach ($zones as $zone)   
    @if ($zone->seats_using_cnt < $zone->seats_cnt)
    <tr style='opacity:1;background-color:#BAD3FF'>
    @else
    <tr style='opacity:0.7;background-color:#CCCCCC;'>
    @endif
        <th width='130px'><img src='/storage/image/{{ $acc_cd }}/{{ $zone->image }}' style='width:130px;height:100px;'/></th>
        <th style='font-size:16px;vertical-align:middle;text-align: center;'>
            {{ $zone->name }} <br>
            <font style='font-size:12px;font-weight:normal;'>({{ $zone->capacity }}人用)</font><br/>
            @if ($zone->seats_using_cnt < $zone->seats_cnt)
            <a class="btn btn-primary" href="#" role="button" name='seat_usable'>空 »</a>
            <input type='hidden' name='zones_info' value='{{ $zone->zones_id }}:{{ $zone->name }}({{ $zone->capacity }}人用):{{ $zone->capacity }}'/>
            @else
            <a class="btn btn-primary disabled" href="#" role="button" id='btn_next'>満 »</a>
            @endif
        </th>
    </tr>
    @endforeach
    </tbody>
    </table>
@endif
<a class="btn btn-primary" href="#" role="button" id='btn_back'>« BACK</a>
<input type='hidden' id='zones_id' name='zones_id'/>
<input type='hidden' id='zones_type_name' name='zones_type_name'/>
<input type='hidden' id='capacity' name='capacity'/>
<input type='hidden' id='userscnt' name='userscnt' value='{{ $userscnt }}'/>
<input type='hidden' id='book_date' name='book_date' value='{{ $book_date }}'/>
<input type='hidden' id='book_date_str' name='book_date_str' value='{{ $book_date_str }}'/>
<input type='hidden' id='acc_cd' name='acc_cd' value='{{ $acc_cd }}'/>
<input type='hidden' id='shop_nm' name='shop_nm' value='{{ $shop_nm }}'/>
</form>
@endsection