@extends('layouts.layout')

@section('content')
<script>
$(function(){
    $('.tenpo_select').click(function(){
        var shopInfo = $(this).attr('data-tenpo-info');
        var arr = shopInfo.split(':');
        $('#acc_cd').val(arr[0]);
        $('#shop_nm').val(arr[1]);
        document.f.submit();
    });
});
</script>
<form name='f' method='POST' action='calendar'>
{{ csrf_field() }}
<div class="navbar navbar-dark bg-dark">
    <div class="container d-flex justify-content-between">
       <a href="/" class="navbar-brand"><img src='/storage/image/system/logo.png'/></a>
    </div>
</div>
<table class="table table-bordered table-inverse">
<tbody>
@foreach ($shops as $shop)
<tr style='opacity:1;background-color:#BAD3FF'>
    <th width='130px'><a href='#' class='tenpo_select' data-tenpo-info='{{ $shop->acc_cd }}:{{ $shop->name }}'><p>{{ $shop->name }}</p></a></th>
</tr>
@endforeach
</tbody>
</table>
<input type='hidden' name='acc_cd' id='acc_cd'/>
<input type='hidden' name='shop_nm' id='shop_nm'/>
</form>
@endsection