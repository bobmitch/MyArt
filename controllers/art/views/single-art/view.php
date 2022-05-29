<?php
defined('CMSPATH') or die; // prevent unauthorized access
?>

<div class="container contain">
    <a href='/art/edit' class='is-pulled-right pull-right button is-primary'>New Art</a>
    <h1 class='title is-1 '>My Art</h1>
    <table class='table'>
        <thead>
            <tr>
                <th>Title</th>
                <th>Thumbnail</th>
                <th>Dimensions</th>
                <th>Media</th>
                <th>Started</th>
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
                    $thumb = new Image($art->f_artthumbnail);
                    $thumb->render(200,'artthumb');
                    ?></td>
                    <td><?php echo $art->f_artdimensions;?></td>
                    <td><?php echo $art->f_artmedia;?></td>
                    <td><?php echo $art->start;?></td>
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