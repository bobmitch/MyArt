<?php
defined('CMSPATH') or die; // prevent unauthorized access

// all entries for week
$time_entries = DB::fetchAll('select t.*, a.title as title from time_entries t, content a WHERE user_id=? and t.art_id=a.id AND t.entrytime > NOW() - INTERVAL 7 DAY ORDER BY t.entrytime DESC',array(CMS::Instance()->user->id));

// summed totals for last week
$time_by_day = DB::fetchAll('
select SUBSTR(t.entrytime,1,10) as myday, sum(t.minutes) as total 
from time_entries t 
WHERE t.user_id=? 
AND t.entrytime > NOW() - INTERVAL 7 DAY 
GROUP BY myday ORDER BY myday ASC',
array(CMS::Instance()->user->id));
// get keyed array entries - key is iso date yyyy-mm-dd
$week_arr = [];
foreach ($time_by_day as $tot) {
    $week_arr[$tot->myday] = $tot->total;
}
// loop through last 7 dates and fill gaps if we don't have time entries
for($i=0; $i<7; $i++){
    $date_key = date('Y-m-d',strtotime("-$i day"));
    if (!array_key_exists($date_key, $week_arr)) {
        // add 0 for date to arr
        $week_arr[$date_key] = '0';
    }
}
ksort($week_arr); // sort by key in ASC order
// now have 7 day arr

// chart data
$labels = [];
$series = [];
$week_chart_data = new stdClass();
foreach ($week_arr as $key => $value) {
    $labels[] = $key;
    $series[] = $value;
}
$week_chart_data->labels = $labels;
$week_chart_data->series = [$series]; // has to be an array of arrays - see https://github.com/gionkunz/chartist-js/issues/393