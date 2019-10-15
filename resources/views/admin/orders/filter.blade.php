<form class="form-inline float-right" action="{{ route('order.search') }}" method="get">
        @csrf
        <input autofocus tabindex="1" autocomplete="off" id="order_search" type="text" class="form-control" placeholder="جستجوی">
        <div id="order_users" class="hidden"></div>
    </form>

    <form class="form-inline float-left" action="{{ route('order.search') }}" method="get">
    @csrf
    <label for="start_date">از</label>
    
    <input id="start_date" tabindex="2"autocomplete="off" class="form-control" placeholder="از ">
    <input type="hidden" name="start_date" id="observer-start">

    <label for="end_date">تا</label>

    <input  id="end_date" tabindex="3"autocomplete="off" class="form-control" placeholder="تا">
    <input type="hidden" id="observer-end" name="end_date">

    <input type="submit" class="form-control btn btn-info" value="فیلتر">
</form>