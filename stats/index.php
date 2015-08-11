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
        var barSales;
        var appSales;
        var smsSales;
        var salesChart;

        var urls = {
            cards: 'https://kassa.neuf.no/stats/card-sales/',
            tekstmelding: 'https://tekstmelding.neuf.no/stats/memberships/',
            snapporder: '/stats/snapporder.php'
        };

        function sumSales(memo, num) {
            return memo + num.sales;
        }

        function toHighchartsSeries(memberships) {
            return _.map(memberships, function (el) {
                return [moment.utc(el.date).valueOf(), parseInt(el.sales, 10)]
            });
        }

        function getBarSales() {
            return $.getJSON(urls.cards, function(data) {
                barSales = data.memberships;
                salesChart.addSeries({name: 'Bar-salg', data: toHighchartsSeries(barSales)});
            });
        }

        function getSmsSales() {
            return $.getJSON(urls.tekstmelding, function(data) {
                smsSales = data.memberships;
                salesChart.addSeries({name: 'SMS-salg', data: toHighchartsSeries(smsSales)});
            });
        }

        function getAppSales() {
            return $.getJSON(urls.snapporder, function (data) {
                appSales = data.memberships;
                salesChart.addSeries({name: 'App-salg', data: toHighchartsSeries(appSales)});
            });
        }

        $(document).ready(function() {
            Highcharts.setOptions({
                credits: {enabled: false}
            });
            salesChart = new Highcharts.Chart({
                chart: {
                    renderTo: 'sales-chart'
                },
                title: {
                    text: false
                },
                xAxis: {
                    type: "datetime"
                },
                yAxis: {
                    title: {
                        text: "Salg"
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
                series: []
            });
            $.when(getSmsSales(), getAppSales(), getBarSales()).done(function() {
                /* Totals */
                var barTotal = _.reduce(barSales, sumSales, 0);
                $('.bar').html(barTotal);
                var smsTotal = _.reduce(smsSales, sumSales, 0);
                $('.sms').html(smsTotal);
                var appTotal = _.reduce(appSales, sumSales, 0);
                $('.app').html(appTotal);

                $('.sum').html(barTotal + smsTotal + appTotal);

                /* Today */
                var today = moment.utc().format('YYYY-MM-DD');
                $('.today-date-wrap').text(today);
                var todayBarSales = _.findWhere(barSales, {date:today}) || {sales: 0};
                $('.bar-today').html(todayBarSales.sales);
                var todaySmsSales = _.findWhere(smsSales, {date:today}) || {sales: 0};
                $('.sms-today').html(todaySmsSales.sales);
                var todayAppSales = _.findWhere(appSales, {date:today}) || {sales: 0};
                $('.app-today').html(todayAppSales.sales);

                $('.sum-today').html(todayBarSales.sales + todaySmsSales.sales + todayAppSales.sales);
            });
        });
    </script>
    <style>
        .big-number {
            font-weight: bold;
            font-size: 1.5em;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Salg per dag</h2>
    <em>Fra 1. august 2015</em>
    <div id="sales-chart"></div>
    <h2>Salg totalt</h2>
    <div id="sums">
        <div>SMS: <span class="sms big-number"></span></div>
        <div>App: <span class="app big-number"></span></div>
        <div>Bar: <span class="bar big-number"></span></div>
        <div>Totalt: <span class="sum big-number"></span></div>
    </div>
    <h2>Salg idag</h2>
    <div id="sums">
        <div>SMS: <span class="sms-today big-number"></span></div>
        <div>App: <span class="app-today big-number"></span></div>
        <div>Bar: <span class="bar-today big-number"></span></div>
        <div>Totalt: <span class="sum-today big-number"></span></div>
    </div>
    <!--<div class="credits-wrap">
        <div class="credits">Laget med <span class="love" title="kærlighed">♥</span> av <a href="http://kak.studentersamfundet.no/" title="Kommunikasjonsavdelingen">KAK</a></div>
    </div>-->
    <div class="disclaimer"><br><br><br><em>Disse tallene kan avvike fra faktiske salgtall.</em></div>
</div>

</body>
</html>
