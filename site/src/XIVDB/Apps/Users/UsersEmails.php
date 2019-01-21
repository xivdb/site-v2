<?php

namespace XIVDB\Apps\Users;

//
// User emails operations
//
Trait UsersEmails
{
	//
    // Send a welcome email
    //
    public function sendWelcomeEmail($email, $username)
    {
		$mail = $this->getModule('mail');
        return $mail->send(
            $email,
            'Welcome to XIVDB',
            'welcome',
            'User/email/welcome.twig',
            [
                'username' => $username,
            ]
        );
    }

    //
    // Send a password recover email
    //
    public function sendPasswordRecoveryEmail($account, $code)
    {
		$mail = $this->getModule('mail');
        return $mail->send(
            $account['email'],
            'XIVDB Password Recovery',
            'password recovery',
            'User/email/password_recovery.twig',
            [
                'user' => $account,
                'code' => base64_encode($code),
            ]
        );
    }
}
