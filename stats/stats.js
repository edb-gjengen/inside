var barSales;
var appSales;
var smsSales;
var salesData;
var salesChart;
var salesChartToday;
var saleTypes = ['bar', 'sms', 'app'];

var urls = {
    cards: 'https://kassa.neuf.no/stats/card-sales/',
    tekstmelding: 'https://tekstmelding.neuf.no/stats/memberships/',
    snapporder: '/stats/snapporder.php'
};

function sumSales(memo, num) {
    return memo + num.sales;
}

function toHighchartsSeries(memberships) {
    memberships = _.sortBy(memberships, 'date');
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

function totals() {
    var barTotal = _.reduce(barSales, sumSales, 0);
    $('.bar').html(barTotal);
    var smsTotal = _.reduce(smsSales, sumSales, 0);
    $('.sms').html(smsTotal);
    var appTotal = _.reduce(appSales, sumSales, 0);
    $('.app').html(appTotal);

    $('.sum').html(barTotal + smsTotal + appTotal);
}

function today() {
    var today = moment.utc().format('YYYY-MM-DD');
    $('.today-date-wrap').text(today);
    var todayBarSales = _.findWhere(barSales, {date: today}) || {sales: 0};
    $('.bar-today').html(todayBarSales.sales);
    var todaySmsSales = _.findWhere(smsSales, {date: today}) || {sales: 0};
    $('.sms-today').html(todaySmsSales.sales);
    var todayAppSales = _.findWhere(appSales, {date: today}) || {sales: 0};
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
            ]
        }
        ]
    });
}

function groupSalesByDate() {
    var types = {
        'bar': barSales,
        'sms': smsSales,
        'app': appSales
    };
    var salesByDate = {};
    _.each(types, function (source, type) {
        _.each(source, function (el) {
            var d = {};
            d[el.date] = {
                'bar': 0,
                'sms': 0,
                'app': 0
            };
            d[el.date][type]= el.sales;

            $.extend(true, salesByDate, d);
        });
    });

    var table_friendly = [];
    _.each(salesByDate, function (sales, date) {
        sales.date = date;
        table_friendly.push(sales);
    });
    table_friendly = _.sortBy(table_friendly, 'date').reverse();

    return table_friendly;
}

function salesTable() {
    var html = '<thead><tr><th>Dato</th><th>Bar</th><th>SMS</th><th>App</th></tr></thead>';
    html += '<tbody>';
    salesData = groupSalesByDate();

    _.each(salesData, function(el) {
        html += '<tr><td>' + el.date + '</td>';
        _.each(saleTypes, function(type) {
            html += '<td>' + el[type] + '</td>';
        });
        html += '</tr>';
    });
    html += '</tbody>';
    $('#sales-table').html(html);
}

function toCSV(data) {
    var csvLines = data.map(function(d){
        return d.date + ',' + saleTypes.map(function(t) { return d[t]; }).join(',');
    });
    var csvHeader = 'date,' +saleTypes.join(',') + '\n';
    return csvHeader + csvLines.join('\n')
}

function downloadCSVFile(csvData, fileName) {
    csvData = 'data:text/csv;charset=utf-8,' + csvData;
    var encodedUri = encodeURI(csvData);

    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", fileName);
    document.body.appendChild(link); // Required for FF

    link.click();
}

function recalc(start, cb) {
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
        totals();
        salesTable();

        if(cb) {
            cb();
        }
    });
}

$(document).ready(function() {
    Highcharts.setOptions({
        credits: {enabled: false}
    });

    var $exportBtn = $('.export-data-btn');
    var $startInput = $('#start');

    var start = $startInput.val();
    recalc(start, function() {
        today();
    });

    /* Dynamic date change */
    $startInput.on('input', function(e) {
        start = $(e.target).val();
        history.replaceState(null, null, '?start=' + start);
        recalc(start);
    });

    /* Export to CSV */
    $exportBtn.on('click', function (e) {
        e.preventDefault();
        var fileName = 'medlemskapsstats-' + $startInput.val() + '.csv';
        var csvData = toCSV(salesData);
        downloadCSVFile(csvData, fileName);
    });

});
