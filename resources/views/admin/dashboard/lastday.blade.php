
	<canvas id="doneAndnew" ></canvas>



<script>
	


var ctx = document.getElementById("doneAndnew");
ctx.height = 130;
var dynamicColors = function() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return "rgba(" + r + "," + g + "," + b + ", " + "0.7)";
         };
var lastWeek = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            'تمام شده',
            'جدید',
            'لغو شده'
        ],
        datasets: [{
            label: 'سفارش',
            data: [
                {{$doneInToDay}},
                {{$newInToDay}},
                {{$canceledInToDay}},
            ],
            backgroundColor: [
                'rgba(54, 162, 90, 0.8)',
                'rgba(255,99,132,0.8)',
                'rgba(54, 162, 235, 0.8)',
            ],
            borderColor: [
                'rgba(54, 162, 90, 0.8)',
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
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
        }
    }
});
</script>