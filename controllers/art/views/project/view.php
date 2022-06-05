<?php
defined('CMSPATH') or die; // prevent unauthorized access
?>

<link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
<script src="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>

<div class='container contain'>
    <a href='/art/timeedit' class='is-pulled-right pull-right button is-primary'>New Time Entry</a>
    <h1 class='title is-1 '>My Time</h1>
    <p><strong>Last 7 days</strong></p>
    <p class='note'>Time below shown in minutes</p>
    <div class="ct-chart ct-double-octave"></div>
</div>

<div class="container contain">
    
    
    <div class="table-container">
        <table class='table is-fullwidth'>
            <thead>
                <tr>
                    <th>Art/Project</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Activity</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($time_entries as $entry):?>
                    <tr>
                        <td><?php echo $entry->title;?></td>
                        <td><?php echo date("F j, Y",strtotime($entry->entrytime));?></td>
                        <td><?php echo $entry->minutes;?></td>
                        <td><?php echo $entry->timeactivity;?></td>
                        <td><?php echo $entry->note;?></td>
                        <td>
                            <a href='/art/timeedit/<?php echo $entry->id;?>' class='button is-info is-small'>Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<script>
    var week_chart_data = <?php echo json_encode($week_chart_data);?> ;
    new Chartist.Line('.ct-chart',week_chart_data);
</script>

