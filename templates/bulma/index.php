<?php
	defined('CMSPATH') or die; // prevent unauthorized access
	
?>

<!DOCTYPE html>
<html lang="en" class='has-navbar-fixed-top'>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!--CMSHEAD-->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
	<link rel="stylesheet" href='/templates/bulma/style.css'>
	<!-- <link rel="stylesheet" href="https://unpkg.com/bulmaswatch/sandstone/bulmaswatch.min.css"> -->
	<!-- multiselect - Slim Select © 2020 Brian Voelker - Used under MIT license. -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.css" rel="stylesheet"></link>
	<!-- end multiselect -->

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" rel="stylesheet">

	<script src="https://kit.fontawesome.com/e73dd5d55b.js" crossorigin="anonymous"></script>
	<script>
		function postAjax(url, data, success) {
			var params = typeof data == 'string' ? data : Object.keys(data).map(
					function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
				).join('&');

			var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
			xhr.open('POST', url);
			xhr.onreadystatechange = function() {
				if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
			};
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.send(params);
			return xhr;
		}
	</script>
</head>
<body class='<?php echo CMS::Instance()->page->alias; ?>'>
	<nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
	<div class="navbar-brand">
		<a class="navbar-item" href="/">
		<?php 
		$logo_image_id = Configuration::get_configuration_value('general_options','admin_logo');
		if ($logo_image_id) {
			$logo_src = Config::$uripath . "/image/" . $logo_image_id;
		}
		else {
			$logo_src = "https://via.placeholder.com/200x60/000000/FFFFFF?text=SeamlessCMS";
		}
		?>
		<img src="<?php echo $logo_src;?>" >
		</a>

		<a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
		<span aria-hidden="true"></span>
		<span aria-hidden="true"></span>
		<span aria-hidden="true"></span>
		</a>
	</div>

	<div id="main_nav" class="navbar-menu">
	
		<div class="navbar-start">
		<?php CMS::Instance()->render_widgets('Top Nav');?>

		

			<div class="navbar-item">
				<a href='/art' class="navbar-item">
				My Art
				</a>
			</div>
		</div>

		<div class="navbar-end">
			
			<div class="navbar-item">
				<div class="buttons">
					<?php if (CMS::Instance()->user->is_member_of ('admin')):?>
						<a class='button is-info' target="_blank" href='/admin'>Admin</a>
					<?php endif; ?>
					
					<?php if (CMS::Instance()->user->username=='guest'):?>
					<a href='/login' class="button is-info">
						Log In
					</a>
					<?php else:?>
					<a href='/logout.php' class="button is-light">
						Log Out
					</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	</nav>
	<?php //CMS::pprint_r (CMS::Instance()->page);?>
	

	<div class='container' id='messages'>
	<?php CMS::Instance()->display_messages();?>
	</div>

	<?php CMS::Instance()->render_widgets('Header');?>

	
	
	<?php
	$sidebar_widgets = Widget::get_widgets_for_position(CMS::Instance()->page->id, "Sidebar");
	?>

	<?php if ($sidebar_widgets):?>
	<div class='columns'>
		<div class='column'>
	<?php endif; ?>
	
	<?php
	$bucket_widgets = Widget::get_widgets_for_position(CMS::Instance()->page->id, "Buckets");
	?>
	
	<?php if (CMS::Instance()->page->content_type==1): ?>
		<?php CMS::Instance()->render_widgets('Above Content');?>
		<div class='container'>
			<?php CMS::Instance()->render_controller(); ?>
		</div>
		<?php CMS::Instance()->render_widgets('After Content');?>
		<?php if ($bucket_widgets):?>
			<div id='buckets_container'  class='container'>
				<div id="buckets" class='columns'>
					<?php CMS::Instance()->render_widgets('Buckets');?>
				</div>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<?php CMS::Instance()->render_widgets('Above Content');?>
		<?php CMS::Instance()->render_controller(); ?>
		<?php CMS::Instance()->render_widgets('After Content');?>
		<?php if ($bucket_widgets):?>
			<div id='buckets_container' class='container'>
				<div id="buckets" class='columns'>
					<?php CMS::Instance()->render_widgets('Buckets');?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($sidebar_widgets):?>
		</div>
		<div class='column is-one-quarter'>
			<aside id="sidebar" class='block'>
				<?php CMS::Instance()->render_widgets('Sidebar');?>
			</aside>
		</div>
	<?php endif; ?>

	

	<?php if ($sidebar_widgets):?>
			</div> <!-- end column -->
		</div> <!-- end columns -->
	<?php endif; ?>
	
	<footer>
		<?php CMS::Instance()->render_widgets('Footer');?>
		<div class='container'>
        <p>116 State Street, St.Joseph, MI 490085 | 269-932-3623</p>
    </div>
	</footer>
	<?php if (Config::$debug) { CMS::Instance()->showinfo();} ?>

	<script>

		// smart search
		let searchtext_el = document.getElementById('searchtext');
		let smart_results = document.getElementById('smart_results');
		if (searchtext_el) {
			// give focus by default
			searchtext.focus();
			searchtext_el.addEventListener('keyup',function(e){
				let cur_search = e.target.value;
				cur_search = cur_search.toLowerCase();
				if (cur_search.length>1) {
					// do smart stuff
					let client_matches = [];
					let user_matches = [];
					smart_clients.forEach(client => {
						if (client.title.toLowerCase().includes(cur_search)) {
							client_matches.push(client);
						}
					});
					smart_users.forEach(user => {
						if (user.username.toLowerCase().includes(cur_search)) {
							user_matches.push(user);
						}
					});
					console.table(client_matches);
					console.table(user_matches);
					if (client_matches.length>0||user_matches.length>0) {
						// gen markup and display
						let markup = '';
						if (client_matches.length>0) {
							markup += `<h5>Clients</h5>`;
							markup += "<ul>";
							client_matches.forEach(client => {
								markup += `<li><a href="/clients/${client.alias}">${client.title}</a></li>`;
							});
							markup += "</ul>";
						}
						if (user_matches.length>0) {
							markup += `<h5>Users</h5>`;
							markup += "<ul>";
							user_matches.forEach(user => {
								markup += `<li><a href="/people/${user.id}">${user.username}</a></li>`;
							});
							markup += "</ul>";
						}
						smart_results.innerHTML = markup;
						smart_results.style.display = "block";
					}
				}
				else {
					// hide smart results
					smart_results.innerHTML = "";
					smart_results.style.display = "none";
				}
			});
			// hide smart results when mouse leaves
			smart_results.addEventListener('mouseleave',function(e){
				e.target.closest('#smart_results').style.display = "none";
			});
		}
		

		let msg_del_buttons = document.querySelectorAll('.notification .delete');
		msg_del_buttons.forEach(b => {
			b.addEventListener('click',function(e){
				e.target.closest('.notification').style.display="none";
			});
		});
		
		var copyable_pws = document.getElementsByClassName('copyable_pw');

	for(var i=0; i< copyable_pws.length; i++){
		copyable_pws[i].onclick = function(){ 
			var a = this.getAttribute('data-pw');
			var textArea = document.createElement("textarea");
			textArea.value = a;
			document.body.appendChild(textArea);
			textArea.select();
			try {
				var successful = document.execCommand('copy');
				var msg = successful ? 'successful' : 'unsuccessful';
				alert('Password copied - ready to paste');
			} catch (err) {
				alert('Error - unable to copy password');
				//console.log('Oops, unable to copy');
			}
		document.body.removeChild(textArea);
		}
		
	}
	</script>
</body>
</html>