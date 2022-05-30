<?php
defined('CMSPATH') or die; // prevent unauthorized access

// not really single-art, just too lazy to change to my-art
$time_entries = DB::fetchAll('select t.*, a.title as title from time_entries t, content a WHERE user_id=? and t.art_id=a.id AND t.entrytime > NOW() - INTERVAL 7 DAY ORDER BY t.entrytime DESC',array(CMS::Instance()->user->id));

//CMS::pprint_r ($my_art);