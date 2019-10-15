<script>

    /* driver search */
    $('#driver_search').on("change paste keyup", function() {
        var input = $('#driver_search').val();
        $.ajax({
            type: 'get',
            url: '{{ route('driver.search') }}',
            data: {'_token': '{{csrf_token()}}', 'search':input, 'json': 'true'},
        }).done(function (response) {
            var reps='';
            var data= response
            for (i =0;i<data.length; i++){
                var id = data[i].id
                reps +='<p> <a href="/admin/carrier/'+data[i].id+'">'+data[i].id+ ' ' +data[i].user.first_name+' '+data[i].user.last_name+' '+ data[i].user.phone +'</a></p>';
                $('#driver_result').html(reps)
                // console.log(data[i].first_name)
            }
        });
        $('#driver_result').removeClass('hidden');
        if (input == ''){
            $('#driver_result').addClass('hidden');
        }


    });

    /*start suser search */
    $('#user_search').on("change paste keyup", function() {
        var input = $('#user_search').val();
        $.ajax({
            type: 'get',
            url: '{{ route('user.search') }}',
            data: {'_token': '{{csrf_token()}}', 'search':input},
        }).done(function (response) {
            var reps='';
            var data= response.msg.data
            for (i =0;i<data.length; i++){
                var id = data[i].id
                reps +='<p> <a href="/admin/users/'+data[i].id+'">'+data[i].first_name+' '+data[i].last_name+' '+ data[i].phone +'</a></p>';
                $('#result_users').html(reps)
            }
        });
        if (input == ''){
            $('#result_users').addClass('hidden');
        }else {
            $('#result_users').removeClass('hidden');
        }
    });

    /* order search */
    $('#order_search').on("change paste keyup", function() {
        var input = $('#order_search').val();
        var field = $("#field").val();
        $.ajax({
            type: 'get',
            url:'/admin/order_search?search='+ input +'&field='+ field +'&json=ok'
        }).done(function (response) {
            console.log(response)
            var reps='';
            var data= response
            for (i =0;i<data.length; i++){
                var id = data[i].id
                reps +='<p> <a href="/admin/orders/'+data[i].id+'">'+data[i].id+ ' ' +data[i].user.first_name+' '+data[i].user.last_name+' '+ data[i].user.phone +' '+data[i].receiver_name +'</a></p>';
                $('#order_users').html(reps)
            }
        });
        $('#order_users').removeClass('hidden');
        if (input == ''){
            $('#order_users').addClass('hidden');
        }


    });
    var clickOnTodayTab = {{ isset($_GET['today']) ? 'true': "false" }}
    /* ./order search */

    /*active last order tab clicked*/

    $('.nobaar_tab > li > a').click(function(){
        sessionStorage.setItem('tabToActive',$(this).attr('class'))
    });

    if(sessionStorage.getItem('tabToActive') && !clickOnTodayTab){
        var activeTab='.'+sessionStorage.getItem('tabToActive');
        $(activeTab).click()
    }
    /* ./active last order tab clicked*/
</script>
