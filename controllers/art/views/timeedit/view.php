<?php
defined('CMSPATH') or die; // prevent unauthorized access

?>
<div class='box container'>
    
<?php if ($new_content):?>
    <h1 class='title is-1'>New Time Entry</h1>
<?php else:?>
    <h1 class='title is-1'>Editing Time</h1>
<?php endif; ?>


    <form method="POST" action="">

    
    <div >
       
            <?php $content_form->display_front_end(); ?>
        
    </div>

 

    <style>
    .field_id_alias, .field_id_category, .field_id_tags,  .field_id_note, .field_id_state, .field_id_start, .field_id_end, .form_field_wrap.hidden {
        display:none;
    }
    div.flex {display:flex; flex-wrap:wrap;}
    div.flex > * {padding-left:2rem; padding-bottom:2rem;}
    /* div.flex > div:first-child {padding-left:0;} */
    div.flex > * {min-width:2rem;}
    </style>


    <div class="fixed-control-bar">
        <button class="button is-primary" type="submit">Save</button>
        <button class="button is-warning" type="button" onclick="window.history.back();">Cancel</button>
    </div>


    </form>
</div>