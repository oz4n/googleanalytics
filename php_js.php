<?php
require_once  'googleAnalytics.php';
$ad         = new googleAnalytics();
$startTime  = mktime(0, 0, 0, date("m") - 1, date("d") - 1, date("Y") -2);
$endEnd     = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
$records    = $ad->getAnalyticRecords(date('Y-m-d', $startTime), date('Y-m-d', $endEnd), 'ga:date', 'ga:visitors,ga:newVisits,ga:visits,ga:pageviews,ga:timeOnPage,ga:bounces,ga:entrances,ga:exits');            
?>

google.load('visualization', '1', {packages:['annotatedtimeline','geomap','table']});
google.setOnLoadCallback(gaChartTimeline);
function gaChartTimeline() {
    var gaData = new google.visualization.DataTable();
    gaData.addColumn('date', 'Date');
    gaData.addColumn('number', 'Visits');
    gaData.addColumn('number', 'Pageviews');
    gaData.addColumn('number', 'Visitors');
    gaData.addColumn('number', 'New Visits');
    gaData.addRows(<?php echo count($records ['entry']); ?>);
    <?php
    if (!empty($records ['entry'])) {
        $row = 0;
        $script = '';
        foreach ($records ['entry'] as $record) {
            $date = date('Y,m-1,d', strtotime($record ['dimension'] ['ga:date']));
            $script .= "gaData.setValue({$row}, 0, new Date({$date}));gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});gaData.setValue({$row}, 2, {$record['metric']['ga:pageviews']});gaData.setValue({$row}, 3, {$record['metric']['ga:visitors']});gaData.setValue({$row}, 4, {$record['metric']['ga:newVisits']});";
            $row++;
        }
    }
    echo $script;
    ?>	  
    var gaVisitsPageviewsChart = new google.visualization.AnnotatedTimeLine(document.getElementById('chartTimeline'));
    gaVisitsPageviewsChart.draw(gaData, {
        wmode: 'transparent',
        displayZoomButtons: false,
        displayAnnotations: true
    });	
}
<?php
$mapRecords = $ad->getAnalyticRecords(date('Y-m-d', $startTime), date('Y-m-d', $endEnd), 'ga:country', 'ga:visits');
?>
google.setOnLoadCallback(gaChartMapOverlay);
function gaChartMapOverlay(){
    var gaData = new google.visualization.DataTable();
    gaData.addColumn('string', 'Country');
    gaData.addColumn('number', 'Visits');
    gaData.addRows(<?php echo count($mapRecords ['entry']); ?>);
    <?php
    if (!empty($mapRecords ['entry'])) {
        $row = 0;
        $script = '';
        foreach ($mapRecords ['entry'] as $record) {
            $script .= "gaData.setValue({$row}, 0, \"" . $record['dimension']['ga:country'] . "\" );gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});";
            $row++;
        }
    }
    echo $script;
    ?>	
    var chartOptions = {};
    chartOptions['dataMode'] = 'regions';
    chartOptions['region'] = 'world';
    var chartMap = new google.visualization.GeoMap(document.getElementById('chartWorldMap'));
    chartMap.draw(gaData,chartOptions);
}
<?php
$keywordsRecords = $ad->getAnalyticRecords(date('Y-m-d', $startTime), date('Y-m-d', $endEnd), 'ga:keyword', 'ga:visits', '-ga:visits', '50');
?>
google.setOnLoadCallback(gaTableKeywords);
function gaTableKeywords(){
    var gaData = new google.visualization.DataTable();
    gaData.addColumn('string', 'Keywords');
    gaData.addColumn('number', 'Visits');
    gaData.addRows(<?php echo count($keywordsRecords ['entry']); ?>);
    <?php
    if (!empty($keywordsRecords ['entry'])) {
        $row = 0;
        $script = '';
        foreach ($keywordsRecords ['entry'] as $record) {
            $script .= "gaData.setValue({$row}, 0, \"" . $record['dimension']['ga:keyword'] . "\" );gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});";
            $row++;
        }
    }
    echo $script;
    ?>	
    var table = new google.visualization.Table(document.getElementById('tableKeywords'));
    table.draw(gaData, {pageSize:10,page:'enable',showRowNumber: true});
}
<?php
$sourceRecords = $ad->getAnalyticRecords(date('Y-m-d', $startTime), date('Y-m-d', $endEnd), 'ga:source', 'ga:visits', '-ga:visits', '50');
?>
google.setOnLoadCallback(gaTableSource);
function gaTableSource(){
    var gaData = new google.visualization.DataTable();
    gaData.addColumn('string', 'Source');
    gaData.addColumn('number', 'Visits');
    gaData.addRows(<?php echo count($keywordsRecords ['entry']); ?>);
    <?php
    if (!empty($sourceRecords ['entry'])) {
        $row = 0;
        $script = '';
        foreach ($sourceRecords ['entry'] as $record) {
            $script .= "gaData.setValue({$row}, 0, \"" . $record['dimension']['ga:source'] . "\" );gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});";
            $row++;
        }
    }
    echo $script;
    ?>	
    var table = new google.visualization.Table(document.getElementById('tableSource'));
    table.draw(gaData, {pageSize:14,page:'enable',showRowNumber: true});
}
<?php
$browserRecords = $ad->getAnalyticRecords(date('Y-m-d', $startTime), date('Y-m-d', $endEnd), 'ga:browser', 'ga:visits', '-ga:visits', '50');
?>
google.setOnLoadCallback(gaTableBrowser);
function gaTableBrowser(){
    var gaData = new google.visualization.DataTable();
    gaData.addColumn('string', 'Browser');
    gaData.addColumn('number', 'Visits');
    gaData.addRows(<?php echo count($keywordsRecords ['entry']); ?>);
        <?php
        if (!empty($browserRecords ['entry'])) {
            $row = 0;
            $script = '';
            foreach ($browserRecords ['entry'] as $record) {
                $script .= "gaData.setValue({$row}, 0, \"" . $record['dimension']['ga:browser'] . "\" );gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});";
                $row++;
            }
        }
        echo $script;
        ?>	
    var table = new google.visualization.Table(document.getElementById('tableBrowser'));
    table.draw(gaData, {pageSize:10,page:'enable',showRowNumber: true});
}	