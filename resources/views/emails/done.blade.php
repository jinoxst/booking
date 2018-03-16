@component('mail::message')
#{{ $booking->users_name }}様
予約が正常に終りました。<br>
下記の予約内容をご確認下さい。<br><br>

@component('mail::panel')
#予約番号：{{ $booking->id }}<br>
#ご利用日：{{ $booking->book_date_str }}<br>
#予約時間：{{ $booking->start_hour }}（{{ $booking->stay_hour }}時間）<br>
#ご利用人数：{{ $booking->userscnt }}名<br>
#座席タイプ：{{ $booking->zones_type_name }}<br>
#座席番号：席{{ $booking->seats_id }}<br>
#お一人様あたりの予定ご利用金額：1,620円<br>
#携帯電話番号:{{ $booking->cellno }}<br>
#メールアドレス:{{ $booking->email }}<br>
@endcomponent

<br>
このメールは{{ config('app.name') }}により自動配信されています。
返信は受付できませんのでご了承下さい。
@endcomponent