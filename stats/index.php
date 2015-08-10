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
        function sumSales(memo, num) {
            return memo + num[1];
        }

        $(document).ready(function() {
            var card_sales;
            var snapporder_sales;
            var sms_sales;

            var urls = {
                tekstmelding: 'https://tekstmelding.neuf.no/stats/memberships/',
                snapporder: '/stats/snapporder.php',
                cards: 'https://kassa.neuf.no/stats/card-sales/'
            };
            Highcharts.setOptions({
                credits:{
                    enabled: false
                }
            });
            var sales = new Highcharts.Chart({
                chart: {
                    renderTo: 'sales'
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
            $.getJSON(urls.tekstmelding, function(data) {
                sms_sales = _.map(data.memberships, function(el) {
                    return [moment(el.date).valueOf(), el.sales]
                });
                sales.addSeries({name: 'SMS-salg', data: sms_sales});

                $.getJSON(urls.snapporder, function(data) {
                    snapporder_sales = _.map(data.memberships, function (el) {
                        return [moment(el.date).valueOf(), parseInt(el.sales, 10)]
                    });
                    sales.addSeries({name: 'App-salg', data: snapporder_sales});

                    $.getJSON(urls.cards, function(data) {
                        card_sales = _.map(data.memberships, function (el) {
                            return [moment(el.date).valueOf(), parseInt(el.sales, 10)]
                        });
                        sales.addSeries({name: 'Bar-salg', data: card_sales});

                        // THEN!

                        /* Totals */
                        var card_totals = _.reduce(card_sales, sumSales, 0);
                        $('.bar').html(card_totals);
                        var sms_totals = _.reduce(sms_sales, function(memo, num) { return memo + num[1]}, 0);
                        $('.sms').html(sms_totals);
                        var snapporder_totals = _.reduce(snapporder_sales, function(memo, num) { return memo + num[1]}, 0);
                        $('.app').html(snapporder_totals);

                        $('.sum').html(snapporder_totals + sms_totals + card_totals)
                    });

                });
            });
        });
    </script>
    <style>
        .sms,
        .bar,
        .sum,
        .app {
            font-weight: bold;
            font-size: 1.5em;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Salg per dag</h2>
    <em>Fra 1. august 2015</em>
    <div id="sales"></div>
    <h2>Salg totalt</h2>
    <div id="sums">
        <div>SMS: <span class="sms"></span></div>
        <div>App: <span class="app"></span></div>
        <div>Bar: <span class="bar"></span></div>
        <div>Totalt: <span class="sum"></span></div>
    </div>
    <!--<div class="credits-wrap">
        <div class="credits">Laget med <span class="love" title="kærlighed">♥</span> av <a href="http://kak.studentersamfundet.no/" title="Kommunikasjonsavdelingen">KAK</a></div>
    </div>-->
    <div class="disclaimer"><br><br><br><em>Disse tallene kan avvike fra faktiske salgtall.</em></div>
</div>

</body>
</html>
