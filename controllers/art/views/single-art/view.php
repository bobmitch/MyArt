<?php
defined('CMSPATH') or die; // prevent unauthorized access

function convertToHoursMins($time, $format = '%02d:%02d') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

?>

<div class="contain container">
    <form action="" method="GET" class="form">
        <div class="control">
            <input name="searchtext" class="input" type="text" placeholder="Search">
        </div>
    </form>
</div>
<br>
<?php if ($searchtext):?>
    <div class='container contain'>
        <p>Searching for &ldquo;<?php echo $searchtext;?>&rdquo;</p>
    </div>
<?php endif; ?>
<br>
<div class="container contain">
    <a href='/art/edit' class='is-pulled-right pull-right button is-primary'>New Art</a>
    <h1 class='title is-1 '>My Art</h1>
    <div class="table-container">
        <table class='table is-fullwidth'>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Thumbnail</th>
                    <th>Dimensions</th>
                    <th>Media</th>
                    <th>Started</th>
                    <th>Est. End</th>
                    <th>Time Spent</th>
                    <th>Progress</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($my_art as $art):?>
                    <tr>
                        <td><?php echo $art->title;?></td>
                        <td><?php 
                        if ($art->f_artthumbnail) {
                            $thumb = new Image($art->f_artthumbnail);
                            $thumb->render(200,'artthumb');
                        }
                        else {
                            echo "<p>No image yet</p>";
                        }
                        ?></td>
                        <td><?php echo $art->f_artdimensions;?></td>
                        <td><?php echo $art->f_artmedia;?></td>
                        <td><?php echo date("F j, Y",strtotime($art->start));?></td>
                        <td><?php if ($art->f_projend) { echo date("F j, Y",strtotime($art->f_projend)); }?></td>
                        <td>
                            <?php 
                            $total_time = DB::fetch('select sum(minutes) as totaltime from time_entries where art_id=?',$art->id)->totaltime;
                            if ($total_time) {
                                echo convertToHoursMins($total_time);
                            }
                            else {
                                echo "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($total_time && $art->f_projest) {
                                $percent_total_done = 100.0 * ($total_time / ($art->f_projest * 60));
                                echo round($percent_total_done,2) . "%";
                            }
                            else {
                                if ($total_time) {
                                    echo "?";
                                }
                                else {
                                    echo "0%";
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $art->f_artlocation;?></td>
                        <td><?php echo $art->f_artstatus;?></td>
                        <td>
                            <a href='/art/edit/<?php echo $art->id;?>' class='button is-info is-small'>Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>