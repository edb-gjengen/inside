<?php
    $semester_start = date_create('first day of august');
    if($semester_start > date_create()) {
        $semester_start = date_modify($semester_start, '-1 year');
    }
    $start = isset($_GET['start']) ? date_create($_GET['start']) : $semester_start;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medlemsstatistikk - Det Norske Studentersamfund</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="stats.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-6"></div>
            <h2>Salg per dag</h2>
            <label for="start">Fra</label> <input id="start" name="start" type="date" value="<?php echo date_format($start, 'Y-m-d'); ?>" />
            <div id="sales-chart"></div>
            <h2>Salg totalt</h2>
            <div id="sums">
                <div>SMS: <span class="sms big-number"></span></div>
                <div>App: <span class="app big-number"></span></div>
                <div>Bar: <span class="bar big-number"></span></div>
                <div>Totalt: <span class="sum big-number"></span></div>
            </div>
            <h2>Salg idag</h2>
            <em>Dato: <span class="today-date-wrap"></span></em>
            <div id="sales-chart-today" style="min-width: 310px; height: 400px; max-width: 600px;"></div>
            <div id="sums">
                <div>SMS: <span class="sms-today big-number"></span></div>
                <div>App: <span class="app-today big-number"></span></div>
                <div>Bar: <span class="bar-today big-number"></span></div>
                <div>Totalt: <span class="sum-today big-number"></span></div>
            </div>
            <div class="disclaimer"><br><br><br><em>Disse tallene kan avvike fra faktiske salgtall.</em></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-2.1.4.js"></script>
<script src="https://code.highcharts.com/highcharts.src.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="stats.js"></script>

</body>
</html>
