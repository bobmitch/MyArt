<?php
defined('CMSPATH') or die; // prevent unauthorized access

// get data for view/page combination
//CMS::pprint_r (CMS::Instance()->page);

$content_id = CMS::Instance()->uri_segments[1] ?? null;
$time_entry = DB::fetch("select * from time_entries where id=?",$content_id);

if (!$content_id || !is_numeric($content_id)) {
	// NEW ITEM!
	$new_content = true;
}
else {

	$new_content = false;
	//CMS::pprint_r ($content_location);
}


// prep forms
$content_form = new Form(CMSPATH . '/controllers/art/views/timeedit/timeform.json');


// check if submitted or show defaults/data from db
if ($content_form->is_submitted()) {

	//echo "<h1>Submitted Content!</h1>";

	// update forms with submitted values
	$content_form->set_from_submit();
	
	// validate
	if ($content_form->validate()) {
		//CMS::pprint_r ($content_form);
		$art_id = $content_form->fields['timeart']->default;
		$note = $content_form->fields['timeentrynote']->default;
		$minutes = $content_form->fields['timeentrytime']->default;
		// check minutes is good
		if (!is_numeric($minutes)) {
			// gotta convert xx:yy to minutes
			$time_arr = explode(":",$minutes);
			$hours_minutes = $time_arr[0] * 60;
			$minutes = $hours_minutes + $time_arr[1];
		}
		$entrytimestamp = $content_form->fields['timeentrytimestamp']->default;
		$timeactivity = $content_form->fields['timeactivity']->default;
		if (!$entrytimestamp) {
			$entrytimestamp = date('Y-m-d H:i:s',time());
		}
		//DB::exec('insert into time_entries (user_id, art_id, minutes, entrytime, note) values(?,?,?,?,?)', array(CMS::Instance()->user->id, $art_id, $minutes, $entrytimestamp,$note));
		DB::exec('insert into time_entries (id, user_id, art_id, minutes, entrytime, timeactivity, note) values(?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE art_id=?, minutes=?, entrytime=?, timeactivity=?, note=? ', array($content_id, CMS::Instance()->user->id, $art_id, $minutes, $entrytimestamp,$timeactivity, $note, $art_id, $minutes, $entrytimestamp, $timeactivity, $note));
		//$content->save($required_details_form, $content_form, $url);
		CMS::Instance()->queue_message('Time added','success','/time');	
	}
	else {
		CMS::Instance()->queue_message('Invalid form','danger',$_SERVER['REQUEST_URI']);	
	}
}
else {
	//CMS::pprint_r ($content_form);
	// set form values correctly from db
	$content_form->get_field_by_name('timeart')->default = $time_entry->art_id;
	$content_form->get_field_by_name('timeentrytime')->default = $time_entry->minutes;
	$content_form->get_field_by_name('timeentrynote')->default = $time_entry->note;
	$content_form->get_field_by_name('timeentrytimestamp')->default = $time_entry->entrytime;
	$content_form->get_field_by_name('timeactivity')->default = $time_entry->timeactivity;

}
