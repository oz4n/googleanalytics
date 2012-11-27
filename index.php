<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="icon" type="image/jpg" href="/assets/70f624e1/images/google-h.jpg" "="">
         <link rel="stylesheet" type="text/css" href="http://web-demo.host.org/themes/css/bootstrap.css">
         <link rel="stylesheet" type="text/css" href="http://web-demo.host.org/themes/css/main.css">
         <link rel="stylesheet" type="text/css" href="http://web-demo.host.org/googleanalytics/main.css">        
            <script type='text/javascript' src='http://www.google.com/jsapi'></script>
            <script type='text/javascript' src='http://web-demo.host.org/googleanalytics/php_js.php'></script>
            <?php    
            require_once  'googleAnalytics.php';
            $ad         = new googleAnalytics();
            $startTime  = mktime(0, 0, 0, date("m") - 1, date("d") - 1, date("Y")-2);
            $endEnd     = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
            $records    = $ad->getAnalyticRecords(date('Y-m-d', $startTime), date('Y-m-d', $endEnd), 'ga:date', 'ga:visitors,ga:newVisits,ga:visits,ga:pageviews,ga:timeOnPage,ga:bounces,ga:entrances,ga:exits');            
       
            ?>
            <title>Google Analistik</title>
    </head>
    <body>
   	<div id="wrapper" style="margin-top: 10px; padding-bottom: 30px;">
            <div class="container">	
                <div class="row">
                    <div class="span12">
                        <div id="timeline">
                            <div class="title"><h3>Timeline</h3></div>  
                        </div>                        
                        <div style="height: 300px;" class="thumbnail" style="text-align: center;" >
                            <div id="chartTimeline"  class="chartholder" style="margin-left: 9px;"></div>
                        </div>
                    </div>
                </div>	
            </div>
            <div class="container">	
                <div class="row">
                    <div class="span12">
                        <div id="map">
                            <div class="title"><h3>Map Overlay & Traffic Source</h3></div>  
                        </div>
                        <div class="row-fluid">
                            <div id="chartWorldMap" class="span8 thumbnail" style=" padding-bottom: 14px; text-align: center;"></div>
                            <div class="span4 thumbnail"><p id="tableSource"></p></div>
                        </div>                        
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <div id="keywords-and-browser">                        
                            <div class="title"><h3>Top Search Keywords & Browser</h3></div>               
                        </div>  
                        <div class="row-fluid">
                            <div class="span6 thumbnail"><div id="tableKeywords"></div></div>
                            <div class="span6 thumbnail"><div id="tableBrowser"></div></div> 
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>