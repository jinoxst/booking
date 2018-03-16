{{ $booking->users_name }}様
予約が正常に終りました。
下記の予約内容をご確認下さい。

予約番号：{{ $booking->id }}
ご利用日：{{ $booking->book_date_str }}
予約時間：{{ $booking->start_hour }}（{{ $booking->stay_hour }}時間）
ご利用人数：{{ $booking->userscnt }}名
座席タイプ：{{ $booking->zones_type_name }}
座席番号：席{{ $booking->seats_id }}
お一人様あたりの予定ご利用金額：1,620円
携帯電話番号:{{ $booking->cellno }}
メールアドレス:{{ $booking->email }}


このメールは{{ config('app.name') }}により自動配信されています。
返信は受付できませんのでご了承下さい。