<?php
defined('CMSPATH') or die; // prevent unauthorized access
?>

<div class="container contain">
    <a href='/art/timeedit' class='is-pulled-right pull-right button is-primary'>New Time Entry</a>
    <h1 class='title is-1 '>My Time</h1>
    <p><strong>Last 7 days</strong></p>
    <table class='table is-fullwidth'>
        <thead>
            <tr>
                <th>Art/Project</th>
                <th>Date</th>
                <th>Time</th>
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
                    <td><?php echo $entry->note;?></td>
                    <td>
                        <a href='/art/timeedit/<?php echo $entry->id;?>' class='button is-info is-small'>Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>