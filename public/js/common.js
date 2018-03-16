var _PROCESS_DIALOG_ = null;
$(function(){
    $(document).ajaxStart(function(){
        showProcessDialog();
    }).ajaxStop(function() {
        hideProcessDialog();
    });    
});

function showProcessDialog(){
    _PROCESS_DIALOG_ = $("#preparing-modal");
    _PROCESS_DIALOG_.dialog({
        modal: true,
        draggable: false,
        closeOnEscape: false,
        resizable: false,
        minWidth: 100,
        minHeight: 0,
        width: 'auto',
        open: function(event, ui) {
            $(".ui-dialog-titlebar", $(this).parent()).hide();
        }
    });
    _PROCESS_DIALOG_.css('padding','0em 0em');
}

function hideProcessDialog(){
    if(_PROCESS_DIALOG_){
        _PROCESS_DIALOG_.dialog('close');
    }
}

function chgDate(date){
    return date.substring(0,4)+'/'+date.substring(4,6)+'/'+date.substring(6,8);
}

function getYmdDay(date){
    var y = date.substr(0,4);
    var m = date.substr(4,2);
    var d = date.substr(6,2);

    var nextDate = new Date(y + '-' + m + '-' + d);
    var y2 = nextDate.getFullYear();
    var m2 = nextDate.getMonth() + 1;
    var d2 = nextDate.getDate();
    var day = nextDate.getDay();
    var day_ja = '';
    switch(day){
        case 0:
            day_ja = '日';
            break;
        case 1:
            day_ja = '月';
            break;
        case 2:
            day_ja = '火';
            break;
        case 3:
            day_ja = '水';
            break;
        case 4:
            day_ja = '木';
            break;
        case 5:
            day_ja = '金';
            break;
        case 6:
            day_ja = '土';
            break;
    }

    if(m2 < 10){
        m2 = '0' + m2;
    }
    if(d2 < 10){
        d2 = '0' + d2;
    }

    return y2 + "" + m2 + "" + d2 + "," + y2 + '年' + m2 + '月' + d2 + '日 (' + day_ja + ')';
}

function getYmdNextDay(date){
    var y = date.substr(0,4);
    var m = date.substr(4,2);
    var d = date.substr(6,2);

    var nextDate = new Date(y + '-' + m + '-' + d);
    nextDate.setDate(nextDate.getDate() + 1);//next date

    var y2 = nextDate.getFullYear();
    var m2 = nextDate.getMonth() + 1;
    var d2 = nextDate.getDate();
    var day = nextDate.getDay();
    var day_ja = '';
    switch(day){
        case 0:
            day_ja = '日';
            break;
        case 1:
            day_ja = '月';
            break;
        case 2:
            day_ja = '火';
            break;
        case 3:
            day_ja = '水';
            break;
        case 4:
            day_ja = '木';
            break;
        case 5:
            day_ja = '金';
            break;
        case 6:
            day_ja = '土';
            break;
    }

    if(m2 < 10){
        m2 = '0' + m2;
    }
    if(d2 < 10){
        d2 = '0' + d2;
    }

    return y2 + "" + m2 + "" + d2 + "," + y2 + '年' + m2 + '月' + d2 + '日 (' + day_ja + ')';
}