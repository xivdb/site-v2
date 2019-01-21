<?php

namespace XIVDB\Routes\AppSecure;

use Symfony\Component\HttpFoundation\Request;
use XIVDB\Apps\Database\Database;

//
// AppAccount
//
trait AppUser
{
    protected function _user()
    {
        $this->route('/', 'GET|OPTIONS', function(Request $request) {
            return $this->redirect('/account');
        });

        //
        // Allow a login
        //
        $this->route('/login', 'GET|POST|OPTIONS', function(Request $request)
        {
            $this->noApi($request);

            // if user is logged in, redirect them to their account
            if ($this->getUser()) {
                return $this->redirect('/account');
            }

            // if login
            if ($request->isMethod('POST')) {
                // verify token
                $this->getModule('csrf')
                    ->isRequestTokenValid($request->get('_token'))
                    ->refreshToken();

                $users = $this->getModule('users');
                $login = $users->authorizeLogin(
                    $request->get('login'),
                    $request->get('password')
                );

                // if login is true, redirect
                if ($login === true) {
                    $lasturl = $this->getLastUrl();
                    return $this->redirect($lasturl ? $lasturl : '/');
                }

                // show login error
                $this->flashbag('error', $login);
            }

            return $this->respond('User/user/login.twig', [
                '_token' => $this->getModule('csrf')->getNewToken(),
            ]);
        });

        //
        // Logout request
        //
        $this->route('/logout', 'GET', function(Request $request)
        {
            $this->noApi($request);
            $this->getModule('users')->authorizeLogout();

            return $this->redirect(URL);
        });

        //
        // Register request
        //
        $this->route('/register', 'GET|POST|OPTIONS', function(Request $request)
        {
            $this->noApi($request);

            // if user is logged in, redirect them to their account
            if ($this->getUser()) {
                return $this->redirect('/account');
            }

            $languages = $this->getModule('language');

            // Register post method
            if ($request->isMethod('POST')) {
                // verify token
                $this->getModule('csrf')
                    ->isRequestTokenValid($request->get('_token'))
                    ->refreshToken();

                $users = $this->getModule('users');

                // if captcha failed
                if (!$users->recaptcha($request->get('g-recaptcha-response'))) {
                    $this->flashbag('error', $languages->custom(1011));
                } else {
                    $email = trim($request->get('email'));
                    $username = preg_replace('/[^a-zA-Z0-9]/', '', trim($request->get('username')));
                    $password = trim($request->get('password'));

                    // try authorize a registration attempt
                    $registered = $users->authorizeRegistration($email, $username, $password);

                    // if registered
                    if ($registered === 'ok') {
                        $users->sendWelcomeEmail($email, $username);
                        $this->flashbag('success', $languages->custom(1010) . "\n\nUsername: ". $username);
                    } else {
                        $this->flashbag('error', $registered);
                    };
                }
            }

            return $this->respond('User/user/register.twig', [
                '_token' => $this->getModule('csrf')->getNewToken(),
            ]);
        });

        //
        // Forgot password request
        //
        $this->route('/forgot-password', 'GET|POST', function(Request $request)
        {
            $this->noApi($request);

            // if user is logged in, redirect them to their account
            if ($this->getUser()) {
                return $this->redirect('/account');
            }

            // on post method
            if ($request->isMethod('POST')) {
                // verify token
                $this->getModule('csrf')
                    ->isRequestTokenValid($request->get('_token'))
                    ->refreshToken();

                $users = $this->getModule('users');
                $reset = $users->authorizeForgotPassword(trim($request->get('login_id')));

                if ($reset === true) {
                    $languages = $this->getModule('language');
                    $this->flashbag('success', $languages->custom(1008));
                } else {
                    $this->flashbag('error', $reset);
                }
            }

            return $this->respond('User/user/forgot_password.twig', [
                '_token' => $this->getModule('csrf')->getNewToken(),
            ]);
        });

        //
        // Forgot password request
        //
        $this->route('/reset-password', 'GET|POST', function(Request $request)
        {
            $languages = $this->getModule('language');
            $showForm = true;

            // check for token
            if ($token = $request->get('token')) {
                // decode it
                $token = base64_decode($token);
                $users = $this->getModule('users');

                // try get user
                if ($user = $users->getUserViaUnique($token)) {
                    // if post
                    if ($request->isMethod('POST')) {
                        // verify token
                        $this->getModule('csrf')
                            ->isRequestTokenValid($request->get('_token'))
                            ->refreshToken();

                        // get passwords
                        $newPassword = $request->get('new_password');
                        $confirmPassword = $request->get('confirm_password');

                        // check it matches confirmed password
                        if ($newPassword === $confirmPassword) {
                            // update users password
                            $reset = $users->updateUserPassword($user['id'], $newPassword);
                            $this->flashbag('notice', $reset);
                            $showForm = false;
                        } else {
                            // passwords did not match
                            $this->flashbag('error', $languages->custom(1006));
                        }
                    }
                } else {
                    // User could not be found
                    $this->flashbag('error', '[1] '. $languages->custom(1007));
                    $showForm = false;
                }
            } else {
                // invalid token
                $this->flashbag('error', '[2] '. $languages->custom(1016));
                $showForm = false;
            }

            return $this->respond('User/user/reset_password.twig', [
                'showForm' => $showForm,
                '_token' => $this->getModule('csrf')->getNewToken(),
            ]);
        });

        //
		// Delete a gearset
		//
		$this->route('/gearsets/delete/{id}', 'GET', function(Request $request, $id)
		{
			$gearsets = $this->getModule('gearsets')->load();

            $exists = false;
            foreach($gearsets as $set) {
                if ($set['id'] == $id) {
                    $exists = true;
                    break;
                }
            }

            if ($exists) {
                $dbs = $this->getModule('database');
                $dbs->QueryBuilder
                    ->delete('content_gearsets')
                    ->where(['id = :id', 'user = :uid'])
                    ->bind('id', $id)
                    ->bind('uid', $this->getUser()->id);

                $dbs->execute();
                $this->flashbag('notice', 'Gearset was deleted.');
            }

            return $this->redirect('/account/tools');
		});
		
		//
        // Delete account
        //
        $this->route('/account/delete', 'GET', function() {
            $user = $this->getUser();
    
            if (!$user) {
                die('Invalid Request');
            }
            
            /** @var Database $dbs */
            $dbs = $this->getModule('database');
            
            // delete members_sessions
            $dbs->QueryBuilder->delete('members_sessions')->where(['member = :id'])->bind('id', $user->id);
            $dbs->execute();
    
            // delete members_characters
            $dbs->QueryBuilder->delete('members_characters')->where(['member = :id'])->bind('id', $user->id);
            $dbs->execute();
    
            // delete members
            $dbs->QueryBuilder->delete('members')->where(['id = :id'])->bind('id', $user->id);
            $dbs->execute();
    
            // delete content_screenshots
            $dbs->QueryBuilder->delete('content_screenshots')->where(['member = :id'])->bind('id', $user->id);
            $dbs->execute();
    
            // delete content_comments
            $dbs->QueryBuilder->delete('content_comments')->where(['member = :id'])->bind('id', $user->id);
            $dbs->execute();
            
            return $this->redirect('/');
        });
        
        //
        // Download account info
        //
        $this->route('/account/download', 'GET', function() {
            $user = $this->getUser();
            
            if (!$user) {
                die('Invalid Request');
            }
    
            /** @var Database $dbs */
            $dbs = $this->getModule('database');
            
            $strings = [];
    
            // Member Information
            $dbs->QueryBuilder->select('*')->from('members')->where(['id = :id'])->bind('id', $user->id);
            $strings[] = 'Member:';
            $member = $dbs->get()->one();
            unset($member['password']);
            $member['data'] = json_decode($member['data'], true);
            $member['history'] = json_decode($member['history'], true);
            $strings[] = json_encode($member, JSON_PRETTY_PRINT);
            $strings[] = '';$strings[] = '';$strings[] = '';
            
            // login sessions
            $dbs->QueryBuilder->select('*')->from('members_sessions')->where(['member = :id'])->bind('id', $user->id);
            $strings[] = 'Login Sessions:';
            foreach($dbs->get()->all() as $row) {
                $strings[] = json_encode($row, JSON_PRETTY_PRINT);
            }
            $strings[] = '';$strings[] = '';$strings[] = '';
            
            // characters
            $dbs->QueryBuilder->select('*')->from('members_characters')->where(['member = :id'])->bind('id', $user->id);
            $strings[] = 'Characters:';
            foreach($dbs->get()->all() as $row) {
                $strings[] = json_encode($row, JSON_PRETTY_PRINT);
            }
            $strings[] = '';$strings[] = '';$strings[] = '';
            
            // content screenshots
            $dbs->QueryBuilder->select('*')->from('content_screenshots')->where(['member = :id'])->bind('id', $user->id);
            $strings[] = 'Content Screenshots:';
            foreach($dbs->get()->all() as $row) {
                $strings[] = json_encode($row, JSON_PRETTY_PRINT);
            }
            $strings[] = '';$strings[] = '';$strings[] = '';
    
            // content comments
            $dbs->QueryBuilder->select('*')->from('content_comments')->where(['member = :id'])->bind('id', $user->id);
            $strings[] = 'Content Comments:';
            foreach($dbs->get()->all() as $row) {
                $strings[] = json_encode($row, JSON_PRETTY_PRINT);
            }
            $strings[] = '';$strings[] = '';$strings[] = '';
            $strings = implode("\n", $strings);
            
            // create file
            $filename = __DIR__.'/'. $user->username .'.txt';
            file_put_contents($filename, $strings);
    
            // download file
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='. basename($filename));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            readfile($filename);
            
            // delete file
            unlink($filename);
            exit;
        });
    }
}
