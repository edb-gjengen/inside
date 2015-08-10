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
                var sms_sales = _.map(data.memberships, function(el) {
                    return [moment(el.date).valueOf(), el.sales]
                });
                sales.addSeries({name: 'SMS-salg', data: sms_sales});

                $.getJSON(urls.snapporder, function(data) {
                    var snapporder_sales = _.map(data.memberships, function (el) {
                        return [moment(el.date).valueOf(), parseInt(el.sales, 10)]
                    });
                    sales.addSeries({name: 'App-salg', data: snapporder_sales});

                    $.getJSON(urls.cards, function(data) {
                        var card_sales = _.map(data.memberships, function (el) {
                            return [moment(el.date).valueOf(), parseInt(el.sales, 10)]
                        });
                        sales.addSeries({name: 'Bar-salg', data: card_sales});
                    });

                });
            });
        });
    </script>
</head>
<body>
<div class="container">
    <h3>Salg totalt</h3>
    <div id="sales"></div>
    <!--<div class="credits-wrap">
        <div class="credits">Laget med <span class="love" title="kærlighed">♥</span> av <a href="http://kak.studentersamfundet.no/" title="Kommunikasjonsavdelingen">KAK</a></div>
    </div>-->
    <div class="disclaimer">Disse tallene kan innehold avvik fra faktiske salgtall, men de er antagelig ikke så langt unna.</div>
</div>

</body>
</html>
