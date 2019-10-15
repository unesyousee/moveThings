<canvas id="conments" ></canvas>
<script>

var ctx = document.getElementById("conments");
ctx.height = 130;
var dynamicColors = function() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return "rgba(" + r + "," + g + "," + b + ", " + "0.7)";
         };
var lastWeek = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: [
            '1',
            '2',
            '3',
            '4',
            '5',
        ],
        datasets: [{
            label: 'نظر',
            data: [
                {{$Comments[0]}},
                {{$Comments[1]}},
                {{$Comments[2]}},
                {{$Comments[3]}},
                {{$Comments[4]}},
            ],
            backgroundColor: [
                '#FF3D67',
                '#FF9124',
                '#FFCD56',
                '#22CECE',
                '#36A2EB',
            ],
            borderWidth: 0
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