<div class="col-lg-12">
    <h2 class="chart_caption">بر حسب تاریخ</h2>

    <form class="form-inline" action="{{ route('dashboard') }}" method="get">
        @csrf
        <label for="start_time">از</label>

        <input  id="end_time" tabindex="3"autocomplete="off" class="form-control" placeholder="از">
        <input type="hidden" id="observer-end-time" name="end_time">

        <label for="end_time">تا</label>
        
        <input id="start_time" tabindex="2"autocomplete="off" class="form-control" placeholder="تا">
        <input type="hidden" name="start_time" id="observer-start-time">

        <input type="submit" class="form-control btn btn-info" value="فیلتر">
    </form>
    <canvas id="filtered" ></canvas>
</div>
<script>
var ctx = document.getElementById("filtered");
ctx.height = 120;
var options = {
    scaleFontColor: "#FFFFFF" ,
  type: 'line',
  data: {
    labels: [
    @foreach ($newOnfilteredDate as $val)
    "{{dateTojal($val[1])}}",
    @endforeach
    ],
    datasets: [
            {
              label: 'جدید',
              data: [
                @foreach ($newOnfilteredDate as $val)
                    "{{$val[0]}}",
                @endforeach
              ],
            borderWidth: 3,
            backgroundColor:'transparent',
            borderColor:'rgba(132,99,255,1)'
            },  
            {
                label: 'انجام شده',
                data: [
                    @foreach ($doneOnfilteredDate as $val)
                        "{{$val[0]}}",
                    @endforeach
                  ],
                borderWidth: 3,
                backgroundColor:'transparent',
                borderColor:'rgba(80, 150, 30,1)'
            },  
            {
                label: 'لغو شده',
                data: [
                    @foreach ($canceleOnfilteredDate as $val)
                        "{{$val[0]}}",
                    @endforeach
                  ],
                borderWidth: 3,
                backgroundColor:'transparent',
                borderColor:'rgba(255,99,132,1)'
            }
        ]
  },
  options: {
    scales: {
        yAxes: [{
        ticks: {
                    reverse: false
        }
      }]
    }
  }
}
var myLineChart = new Chart(ctx, options);
</script>