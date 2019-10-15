
 <canvas id="lastWeek" ></canvas>
<script>
var ctx = document.getElementById("lastWeek");
ctx.height = 430;
var dynamicColors = function() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return "rgba(" + r + "," + g + "," + b + ", " + "0.7)";
         }
var lastWeek = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
			@foreach ($lastMonth as $key => $value)
        		"{{dateTojal($value[1])}}",
        	@endforeach
        ],
        datasets: [{
            label: 'سفارش',
            data: [
            	@foreach ($lastMonth as $key => $value)
            		{{ $value[0].',' }}
            	@endforeach
            ],
            backgroundColor: [
            @foreach ($lastMonth as $key => $value)
                    
                    @if ($value[0] <=1)
                    '#F44336',

                    @elseif ($value[0] >=1 && $value[0] <=2 )
                    '#E91E63',

                    @elseif ($value[0] >=3 && $value[0] <=4 )
                    '#FF5722',

                    @elseif ($value[0] >=5 && $value[0] <=6 )
                    '#FF9800',

                    @elseif ($value[0] >=7 && $value[0] <=8 )
                    '#2196F3',

                    @elseif ($value[0] >=9 && $value[0] <=10 )
                    '#CDDC39',

                    @elseif ($value[0] >=11 )
                    '#4CAF50',
                    @endif
                @endforeach
            ],
            borderColor: [
                'rgb(0,0,0)',
            ],
            borderWidth: 1
        }]
    },
    options: {
        legend: {
            labels: {
                FontFamily : 'irsans',
                fontColor: 'blue'
            }
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>