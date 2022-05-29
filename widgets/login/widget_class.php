<?php
defined('CMSPATH') or die; // prevent unauthorized access

// if user signs in with google and user already registered, login
// if not, register/create blank new user
// if user signs in with username/password, check and login if ok

class Widget_login extends Widget {
	public function render() {
		// check for goto and redirect if needed
		$goto = $_SESSION['smart_redirect'] ?? Config::$uripath . '/';
		
		// check if need to logout
		$logout = Input::getvar('logout','STRING',null);
		if (CMS::Instance()->user->username!=='guest' && $logout) {
			session_start();
			session_destroy();
			$_SESSION = array();
			session_start();
			CMS::Instance()->queue_message('Logged out','success','/');
			//header('Location: ' . "/");
		}
		// reach here, signin stuff
		$email = Input::getvar('email','EMAIL',null);
		$id_token = Input::getvar('id_token');
        
		if ($email || $id_token ) {
			$a_user = new User();
			if ($id_token) {
				// google login attempted
				$a_user = $this->authenticate_token($a_user);
				/* CMS::pprint_r ($a_user);
				exit(0); */
				if ($a_user->email) {
					$_SESSION['user_id'] = $a_user->id;
					Hook::execute_hook_actions('user_logged_in'); 
					CMS::Instance()->queue_message('Signed in','success', $goto);
				}
				else {
					CMS::Instance()->queue_message('Error signing in with Google','danger',Config::$uripath . "/");
				}
			}
			else {
				$a_user->load_from_email($email);
				if ($a_user->email) {
					$password = Input::getvar('password','RAW');
					if ($password) {
						if ($a_user->check_password($password)) {
							// logged in!
							$_SESSION['user_id'] = $a_user->id;
							Hook::execute_hook_actions('user_logged_in'); 
							CMS::Instance()->queue_message('Welcome ' . $login_user->username, 'success', $goto);
						}
					}
				}
			}
			// reach here, problem
			CMS::Instance()->queue_message('Unknown user','danger',Config::$uripath . "/sign-in");
		}
		else {
		?>
		<div class='contain container'>
			<form id='register' method="POST">
				<h1 class='title is-1'><?php echo Config::$sitename . " Login";?></h1>

					<div class='field'>
						<label class="label" for='email'>Email</label>
						<div class="control has-icons-left">
							<input class='input' autocapitalize="none" type="email" name="email" required>
							<span class="icon is-small is-left">
								<i class="fas fa-envelope"></i>
							</span>
						</div>
						<p class="help">Required</p>
					</div>
					
					<div class='field'>
						<label class="label" for='password'>Password</label>
						<div class="control has-icons-left">
							<input class='input' type="password" name="password" required>
							<span class="icon is-small is-left">
								<i class="fas fa-unlock"></i>
							</span>
						</div>
						<p class="help">Required</p>
					</div>
				<br>
				<button class='cta btn button is-primary' type='submit'>Sign Me In</button>
				<br>
				<?php Hook::execute_hook_actions('additional_login_options'); ?>
				
			</form>
		</div>
		<?php
		}
	}

	public function authenticate_token($user_object) {
        if ($user_object->id!==false) {
            // already logged in 
            return $user_object;
        }
        $id_token = Input::getvar('id_token');
        if (!$id_token) {
            // google login not attempted
            return $user_object;
        }

        $a_user = $user_object;

        $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=" . $id_token;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        if (!curl_errno($curl)) {
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($http_code==200) {
				$response_object = json_decode($result);
                $google_email = $response_object->email;
                $query = 'select * from users where email=?';
                $stmt = CMS::Instance()->pdo->prepare($query);
                $stmt->execute(array($google_email));
                $result = $stmt->fetch();
                if ($result) {
                    // google user email matched user in system - return user details class
                    $a_user->load_from_id($result->id);
                    return $a_user;
                }
                else {
					// REGISTER USER!!!
					// make random password for google user
					/* $bytes = openssl_random_pseudo_bytes(8);
					$random_password = bin2hex($bytes);
					$new_user_id = User::create_new ($response_object->name, $random_password, $response_object->email, [3], 1); // [] no groups, 1 published	
					$a_user->load_from_id ($new_user_id);
					//CMS::pprint_r ($a_user); exit(0);
                    return $a_user; */
                    CMS::Instance()->queue_message('Google knows you, but we do not','danger',Config::$uripath . "/login");
                }
            }
            else {
                CMS::Instance()->queue_message('Google server failed to respond','danger',Config::$uripath . "/login");
                // google server call failed
            }
        }
        else {
            CMS::Instance()->queue_message('Error sending data to Google','danger',Config::$uripath . "/login");
            // curl failed
        }
        return $a_user;
    }
}