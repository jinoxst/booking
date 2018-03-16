@extends('layouts.layout')

@section('content')
<div id='display_done'>
<style>

</style>
<script>
$(function(){
    var users_name = '{{ $users_name }}';
    if(users_name){
        $('#users_name').val(users_name);
    }
    var cellno = '{{ $cellno }}';
    if(cellno){
        $('#cellno').val(cellno);
    }
    var email = '{{ $email }}';
    if(email){
        $('#email').val(email);
    }
    $('#btn_next').click(function(){
        var form_key_values = {};
        $.each($('form[name=f]').serializeArray(), function() {
            form_key_values[this.name] = this.value;
        });
        $.ajax({
            url: 'done',
            type: "POST",
            dataType: "html",
            data: form_key_values,
            async: true,
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(contents){
                $('#display_done').html(contents);
            },
            complete: function(){
            },error: function(xmlRequest, status, thrown){
                alert(xmlRequest.status + ' / ' + status + ' / ' + thrown);
            }
        });
    });
    $('#btn_back').click(function(){
        document.f.action='time';
        document.f.submit();
    });
});


</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('shared.tenpo_title')
<p style='font-size:12px;'>TOP -> ルームタイプ選択 -> 時間選択 -> <font class='tltle_selector'>予約確認</font> -> 完了</p>
    <br/>
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
    ご利用日：{{ $book_date_str }}<br>
    予約時間：{{ $start_hour }}({{ $stay_hour }}時間)<br>
    ご利用人数：{{ $userscnt }}名<br>
    座席タイプ：{{ $zones_type_name }}<br>
    座席番号：席{{ $seats_id }}<br>
    お一人様あたりの予定ご利用金額：1,620円<br>
    <br/>
    <font color='red'>*</font>氏名:<br>
    <input type='text' name='users_name' id='users_name' /><br>
    <font color='red'>*</font>携帯電話番号:<br>
    <input type='number' name='cellno' id='cellno' /><br>
    <font color='red'>*</font>メールアドレス:<br>
    <input type='email' name='email' id='email'/><br><br>
    <font color='red'>*は必須です。</font>
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
</div>
@endsection