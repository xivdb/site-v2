<?php

namespace XIVDB\Routes\AppSecure;

use Symfony\Component\HttpFoundation\Request;

//
// AppAccount
//
trait AppAccount
{
    protected function _account()
    {
        //
        // Account page
        //
        $this->route('/account', 'GET|POST|OPTIONS', function(Request $request)
        {
            // permissions
            $this->noApi($request);
            $this->mustBeOnline();

            return $this->respond('User/profile/index.twig', [
                'type' => 'profile',
                '_token' => $this->getModule('csrf')->getNewToken(),
            ]);
        });

        //
        // Account save
        //
        $this->route('/account/save', 'POST', function(Request $request)
        {
            // verify token
            $this->getModule('csrf')
                 ->isRequestTokenValid($request->get('_token'))
                 ->refreshToken();

            // permissions
            $this->noApi($request)->mustBeOnline();
            $users = $this->getModule('users');

            // if post request
            if ($request->isMethod('POST'))
            {
                $username = trim($request->get('username'));
                $email = trim($request->get('email'));
                $password = trim($request->get('password'));

                // sanitize user data
                list($email, $username, $password) = $users->sanitizeUserCrud($email, $username, $password);

                // check if invalid
                if ($invalidResponse = $users->invalidUserCrudCheck($email, $username, $password)) {
                    $this->flashbag('notice', $invalidResponse);
                    return $this->redirect('/account');
                }

                $user = $this->getUser();

                // if username changed, check if username exists
                if (strtolower($username) != strtolower(trim($user->username)) && $users->checkUsernameExists($user->id, $username)) {
                    $this->flashbag('notice', 'Username has been taken. If you own this username, please contact: kupo@xivdb.com');
                    return $this->redirect('/account');
                }

                // if email changed, check if email exists
                if (strtolower($email) != strtolower(trim($user->email)) && $users->checkEmailExists($user->id, $email)) {
                    $this->flashbag('notice', 'Email has been taken. If you own this email, please contact: kupo@xivdb.com');
                    return $this->redirect('/account');
                }

                // update account
                $user->email = $email;
                $user->username = $username;
                $user->data['site_background'] = trim($request->get('site_background'));
                $user->save();

                $this->flashbag('notice', 'Account has been updated.');

                // if password updated
                if ($request->get('password1') && $request->get('password2') && ($request->get('password1') === $request->get('password2'))) {
                    $passwordResponse = $users->updateUserPassword($this->getUser()->id, $request->get('password1'));
                    $this->flashbag('notice', $passwordResponse);
                }
            }

            return $this->redirect('/account');
        });

        //
        // Characters Page
        //
        $this->route('/account/characters', 'GET|POST', function(Request $request)
        {
            // permissions
            $this->noApi($request);
            $this->mustBeOnline();

            // show characters page
            return $this->respond('User/characters/index.twig', [
                'type' => 'character',
            ]);
        });

        //
        // Link a character
        //
        $this->route('/account/characters/verify/{id}', 'GET|POST', function(Request $request, $id)
        {
            // permissions
            $this->noApi($request);
            $this->mustBeOnline();

            // get users module
            $users = $this->getModule('users');

            // Does the user already have the character?
            if ($users->hasCharacter($this->getUser(), $id)) {
                return $this->json([false, 'You already own this character!']);
            }

            // get xivsync and attempt to verify character
            $sync = $this->getModule('xivsync');
            $response = $sync->verifyCharacter($this->getUser()->characterCode, $id);

            // response is an array
            if (is_array($response)) {
                // add the character to the user
                $users->addCharacter($this->getUser(), $response);
                return $this->json([true]);
            }

            // return verficiation response
            return $this->json([false, $response]);
        });

        //
        // Switch a main character
        //
        $this->route('/account/characters/switch/{id}', 'GET|POST', function(Request $request, $id)
        {
            // permissions
            $this->noApi($request);
            $this->mustBeOnline();

            // Switch to another character
            $users = $this->getModule('users');
            $response = $users->switchCharacter($this->getUser(), $id);
            $this->flashbag('notice', $response);

            // redirect back
            return $this->redirect('/account/characters');
        });

        //
        // Delete a character
        //
        $this->route('/account/characters/delete/{id}', 'GET|POST', function(Request $request, $id)
        {
            // permissions
            $this->noApi($request);
            $this->mustBeOnline();

            // redirect back
            // show characters page
            return $this->respond('User/characters/delete.twig', [
                'type' => 'character',
                'delete_id' => $id,
            ]);
        });

        //
        // Switch a main character
        //
        $this->route('/account/characters/delete/{id}/confirm', 'GET|POST', function(Request $request, $id)
        {
            // permissions
            $this->noApi($request);
            $this->mustBeOnline();

            // Switch to another character
            $users = $this->getModule('users');
            $response = $users->deleteCharacter($this->getUser(), $id);
            $this->flashbag('notice', $response);

            // redirect back
            return $this->redirect('/account/characters');
        });

        //
        // Tools
        //
        $this->route('/account/tools', 'GET|POST', function(Request $request)
        {
            // permissions
            $this->noApi($request);
            $this->mustBeOnline();

            return $this->respond('User/tools/index.twig', [
                'type' => 'tools',
            ]);
        });
    }
}
