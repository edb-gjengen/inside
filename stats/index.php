<?php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medlemsstatistikk - Det Norske Studentersamfund</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="all" href="../snapporder/css/style.css" />

    <script src="https://code.jquery.com/jquery-2.1.4.js"></script>
    <script src="https://code.highcharts.com/highcharts.src.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.js"></script>
    <script>
        $(document).ready(function() {
            var urls = {
                tekstmelding: 'https://tekstmelding.neuf.no/stats/memberships/'
            };
            Highcharts.setOptions({
                credits:{
                    enabled: false
                },
                global: {
                    useUTC: false
                }
            });

            $.getJSON(urls.tekstmelding, function(data) {
                var example_data = [
                    {"data": [[1398279855000, 1]], "name": "Pale Ale"},
                    {"data": [[1397067745000, 1]], "name": "Fat\u00f8l"},
                    {"data": [[1427314222000, 1]], "name": "Guiness"}
                ];
                console.log(data);

                var sms_sales = _.map(data.memberships, function(el) {
                    var ts = moment(el.date);
                    console.log(ts);
                    return [ts.valueOf(), el.sales]
                });

                console.log(sms_sales);
                console.log(sms_sales[0][0]);
                var sales = new Highcharts.Chart({
                    chart: {
                        renderTo: 'sales',
                        type: 'spline'
                    },
                    title: {
                        text: false
                    },
                    xAxis: {
                        type: "datetime"
                    },
                    yAxis: {
                        title: {
                            text: "Kjøp"
                        },
                        min: 0
                    },

                    plotOptions: {
                        series: {
                            marker: {
                                radius: 4
                            }
                        }
                    },
                    series: [{
                        name: 'SMS-salg',
                        data: sms_sales,
                        //pointStart: sms_sales[0][0],
                        //pointInterval: 24 * 3600 * 1000
                    }]
                });
                //sales.addSeries({name:'Testings', data: sms_sales });
            });
        });
    </script>
</head>
<body>
<div>
    <h3>Salg totalt</h3>
    <div id="sales"></div>
    <!--<div class="credits-wrap">
        <div class="credits">Laget med <span class="love" title="kærlighed">♥</span> av <a href="http://kak.studentersamfundet.no/" title="Kommunikasjonsavdelingen">KAK</a></div>
    </div>-->
</div>

</body>
</html>
