<style>

</style>
<script>
$(function(){
    $('#btn_next').click(function(){
        document.f.action='/';
        document.f.submit();
    });
});


</script>
@include('shared.tenpo_title')
<p style='font-size:12px;'>TOP -> ルームタイプ選択 -> 時間選択 -> 予約確認 -> <font class='tltle_selector'>完了</font></p>
<p style='font-weight:bold'>{{ $users_name }}様</p>
予約登録が終りました。<br>
下記の内容をご確認下さい。<br><br>
<form name='f' method='GET' action=''>
予約番号：{{ $booking_id }}<br>
ご利用日：{{ $book_date_str }}<br>
予約時間：{{ $start_hour }}（{{ $stay_hour }}時間）<br>
ご利用人数：{{ $userscnt }}名<br>
座席タイプ：{{ $zones_type_name }}<br>
座席番号：席{{ $seats_id }}<br>
お一人様あたりの予定ご利用金額：1,620円<br>
携帯電話番号:{{ $cellno }}<br>
メールアドレス:{{ $email }}<br>

<br>
<a class="btn btn-primary" href="#" role="button" id='btn_next'>HOME »</a>