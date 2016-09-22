var barSales;
var appSales;
var smsSales;
var salesChart;
var salesChartToday;

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
        return [moment.utc(el.date).valueOf(), el.sales]
    });
}

function getBarSales(start) {
    return $.getJSON(urls.cards + '?start=' + start, function(data) {
        barSales = data.memberships;
        salesChart.addSeries({name: 'Bar-salg', data: toHighchartsSeries(barSales)});
    });
}

function getSmsSales(start) {
    return $.getJSON(urls.tekstmelding + '?start=' + start, function(data) {
        smsSales = data.memberships;
        salesChart.addSeries({name: 'SMS-salg', data: toHighchartsSeries(smsSales)});
    });
}

function getAppSales(start) {
    return $.getJSON(urls.snapporder + '?start=' + start, function (data) {
        appSales = data.memberships;
        salesChart.addSeries({name: 'App-salg', data: toHighchartsSeries(appSales)});
    });
}

function recalc(start) {
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

    $.when(getSmsSales(start), getAppSales(start), getBarSales(start)).done(function() {
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

        salesChartToday = new Highcharts.Chart({
            chart: {
                renderTo: 'sales-chart-today',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: false
            },
            series: [{
                name: 'Salg', data: [
                    {name: 'Bar-salg', y: todayBarSales.sales},
                    {name: 'SMS-salg', y: todaySmsSales.sales},
                    {name: 'App-salg', y: todayAppSales.sales}
                ]}
            ]
        });
    });
}

$(document).ready(function() {
    Highcharts.setOptions({
        credits: {enabled: false}
    });

    var $startInput = $('#start');
    var start = $startInput.val();
    recalc(start);

    $startInput.on('input', function(e) {
        start = $(e.target).val();
        recalc(start);
    })
});
