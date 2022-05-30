<?php
defined('CMSPATH') or die; // prevent unauthorized access

// get data for view/page combination
//CMS::pprint_r (CMS::Instance()->page);

$content_id = CMS::Instance()->uri_segments[1] ?? null;
$client_id = Input::getvar('client_id','INT');

if (!$content_id || !is_numeric($content_id)) {
	// NEW ITEM!
	$content = new content(2); // art
	$new_content = true;
}
else {
	$content_item = Content::get_all_content_for_id($content_id);
	$content = new Content();
	$content->load($content_id);
	$new_content = false;
	//CMS::pprint_r ($content_location);
}


// prep forms
$required_details_form = new Form(CMSPATH . '/admin/controllers/content/views/edit/required_fields_form.json');
$content_form = new Form (CMSPATH . '/controllers/art/custom_fields.json');


// check if submitted or show defaults/data from db
if ($required_details_form->is_submitted()) {

	//echo "<h1>Submitted Content!</h1>";

	// update forms with submitted values
	$required_details_form->set_from_submit();
	$content_form->set_from_submit(); 

	// validate
	if ($required_details_form->validate() && $content_form->validate()) {
		// forms are valid, save info
		if ($new_content) {
			$client_alias = DB::fetch('select alias from content where id=?',$client_id)->alias;
			$url = "/art";
		}
		else {
			$url = "/art";
		}
		$content->save($required_details_form, $content_form, $url);
	}
	else {
		CMS::Instance()->queue_message('Invalid form','danger',$_SERVER['REQUEST_URI']);	
	}
}
else {

	$required_details_form->get_field_by_name('state')->default = $content->state;
	$required_details_form->get_field_by_name('title')->default = $content->title;
	$required_details_form->get_field_by_name('title')->label = "Art Title (You change later if just starting!)";
	$required_details_form->get_field_by_name('alias')->default = $content->alias;
	$required_details_form->get_field_by_name('note')->default = $content->note;
	$required_details_form->get_field_by_name('start')->default = $content->start;
	$required_details_form->get_field_by_name('end')->default = $content->end;
	$required_details_form->get_field_by_name('category')->content_type = $content->content_type;
	$required_details_form->get_field_by_name('category')->default = $content->category;

	foreach ($content_form->fields as $content_field) {
		$value = $content->get_field($content_field->name);
		if ($value) {
			$content_field->default = $value;
		}
	}
	//CMS::pprint_r ($content_form);
}
