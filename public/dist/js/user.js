$(document).ready(function () {
    $('.setting_heavy').click(function () {
        $(this).next().slideToggle();
    });
    $('.panel_admins').click(function () {
        $(this).find('.members').slideToggle();
    });
$('.form-group:has(span.value)').find('input[type=range]').on('input', function(){
    $(this).prev().text($(this).val());
});
    // grant notification
    setTimeout(function() {
        
        if (Notification.permission != "granted") {
            Notification.requestPermission()
          }
    }, 5000)

    // order filter
        $('#start_date').persianDatepicker({
            initialValue: false,
            observer: true,
            format: 'YYYY/MM/DD',
            altField: '#observer-start'
        });
        $('#end_date').persianDatepicker({
            initialValue: false,
            observer: true,
            autoClose: true,
            format: 'YYYY/MM/DD',
            altField: '#observer-end',
        });
// chart filter
        $('#start_time').persianDatepicker({
            initialValue: true,
            observer: true,
            autoClose: true,
            format: 'YYYY/MM/DD',
            altField: '#observer-start-time'
        });
        $('#end_time').persianDatepicker({
            initialValue: false,
            observer: true,
            autoClose: true,
            format: 'YYYY/MM/DD',
            altField: '#observer-end-time',
        });

     // add alert icon where state have new order

    var tabs = [];
    $('.nobaar_indexes > div').each(function(e){
        if($(this).has('.label.label-danger').length > 0){
            tabs.push($(this).attr('id'))
        }
    })
    for (var i = 0; i < tabs.length; i++) {
        var tab = tabs[i]
        $('.'+tab).after('<span class="text-red new_alert">&#10687;</span>');
        $('.'+tab).parent().css('display', 'flex')
    }
     /*add comma every three degit*/
        


    /* ./ add comma every three degit*/

    $('.panel_admins .members').click(function (event) {
        event.stopPropagation();
    });
    $(window).click(function () {
        $('#result_users').addClass('hidden')
    });
    $('#result_users').click(function (event) {
        event.stopPropagation();
    });

    $("#aside_search").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#aside_list li").filter(function () {
            $(this).toggle($(this).children('a').text().toLowerCase().indexOf(value) > -1)
        });
    });
    $('input[name=confirm]').keyup(function(){
        var password_val=$('input[name=password]').val()
        if(password_val==$('input[name=confirm]').val()){
            $('input[name=confirm]').css('background-color','white')
            $('.submit_user').removeClass('false')
        }else{
            $('input[name=confirm]').css('background-color','#ffc8c8')
            $('.submit_user').addClass('false')
        }
    })
    $('.submit_user.false').click(function (event) {
        event.preventDefault();
        alert('رمز با تکرار برابر نیست ');
        // return false;
    })
    $('.btn.fa.fa-remove').click(function(){
        a=confirm("آیا از حذف این مورد اطمینان دارید؟");
        if (a){return true}else{return a }
    });
});
$('.leapyear-algorithmic').persianDatepicker({
    inline: true,

});
$('.observer-example').persianDatepicker({
    observer: true,
    format: 'YYYY-MM-DD',
    altField: '.observer-example-alt'
});
$('.clockpicker').clockpicker({
    placement: 'left',
    align: 'left',
    autoclose: true,
    'default': 'now',
    donetext: 'ساعت حرکت'
});
function priceLocalise(){
        var numberWithCommas = (x) => {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        };
        $('.amount').each(function(x){
            var amount = $(this).text()
             $(this).text(numberWithCommas(amount))
        })
    }
    priceLocalise()


$(':input').on('input change',function(){
    var val = $(this).val();
    val = val.replace('۰', '0')
    val = val.replace('۱', '1')
    val = val.replace('۲', '2')
    val = val.replace('۳', '3')
    val = val.replace('۴', '4')
    val = val.replace('۵', '5')
    val = val.replace('۶', '6')
    val = val.replace('۷', '7')
    val = val.replace('۸', '8')
    val = val.replace('۹', '9')
    $(this).val(val);
});


// notifications 

var compareJSON = function(obj1, obj2) { // difrant json
  var ret = {};
  for(var i in obj2) {
    if(!obj1.hasOwnProperty(i) || obj2[i] !== obj1[i]) {
      ret[i] = obj2[i];
    }
  }
  return ret;
};

function notifyMe(massege) {
  if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    var notification = new Notification(massege);
  }

  // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== "denied") {
    Notification.requestPermission().then(function (permission) {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        var notification = new Notification(massege);
      }
    });
  }

  // At last, if the user has denied notifications, and you 
  // want to be respectful there is no need to bother them any more.
}


function haveNotif(){

    var baseUrl = window.location.origin;
    var csrf = $('meta[name=csrf-token]').attr("content")
    var data = {'_token': csrf, 'json': 'true'}

    var orders = $.ajax({
        type: 'get',
        // async: false,
        url: baseUrl+"/admin/notification",
        data: data
    }).responseJSON;
    return orders;
}
var isfirst = 0;
var lastOrders = haveNotif()


// function findUpTag(el, tag) {
//     while (el.parentNode) {
//         el = el.parentNode;
//         if (el.tagName === tag)
//             return el;
//     }
//     return null;
// }
//
//
// var tabelTds = document.querySelectorAll('.nobaar-table td');
// for (i=0; i< tabelTds.length ; i++){
//     tabelTds[i].addEventListener('mouseover',function (event) {
//         var index = event.target.cellIndex;
//         var table = findUpTag(event.target,'TABLE');
//        var rows = table.rows;
//         for(const f in rows){
//             var cells = table.rows[f].cells;
//             for (const j in cells){
//                 if(cells[j].style  && cells[j].cellIndex == index){
//                         cells[j].style.backgroundColor="#464A52";
//                 }
//             }
//         }
//     })
//     tabelTds[i].addEventListener('mouseout',function (event) {
//         var index = event.target.cellIndex;
//         var table = findUpTag(event.target,'TABLE');
//        var rows = table.rows;
//         for(const f in rows){
//             var cells = table.rows[f].cells;
//             for (const j in cells){
//                 if(cells[j].style && cells[j].cellIndex == index){
//                         cells[j].style.backgroundColor="transparent";
//                 }
//             }
//         }
//     })
// }

setInterval(function(){
    var currentOrders = haveNotif();
    if(currentOrders) {
        var sessionKey = 'order_notif' + currentOrders[0].id;
        var getSession = localStorage.getItem(sessionKey);
        // console.log(getSession )
        if (isfirst && lastOrders[0].id != currentOrders[0].id && !getSession) {
            localStorage.setItem(sessionKey, lastOrders[0].id);
            document.getElementById('notification').play();
            orderNotif(currentOrders[0].id)
            notifyMe('سفارش جدید ' + currentOrders[0].id);
            lastOrders = currentOrders;
        }
        isfirst = 1
    }
 }, 15000);

function orderNotif(orderId){
    $("#notifModal").modal();
    var alert = '<div class="alert alert-success"> سفارش جدید <strong> '+ orderId +' </strong>.</div>';
    var el = $('#orderNotif')
    el.append(alert)
}


// remove aletrs from modal

    $('#notifModal .close').click(function() {
        $(this).parent().parent().find('#orderNotif').html(' ')
    });

$('.priceNum').on('input',function(){

    var re = /,/g;
    var oldval = $(this).val();
    var pure = oldval.replace(re, '');
    $(this).val((pure.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")))
 /*   let last = oldval[oldval.length -1];
    if(pure.length%3 == 0 ){
        }*/
});
$("#discount_check_all").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
});
