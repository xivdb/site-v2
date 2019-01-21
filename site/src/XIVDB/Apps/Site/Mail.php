<?php

namespace XIVDB\Apps\Site;

use Postmark\PostmarkClient;

//
// Wrapper to send mail using postmark
//
class Mail extends \XIVDB\Apps\AppHandler
{
	//
	// Send some mail
	//
	public function send($to, $subject, $tags, $template, $data = [])
	{
		// if we're on dev, only send to me
		$to = DEV ? MAIL_DEV : $to;

		$twig = $this->getModule('twig');

		// generate html
		$html = $twig->render($template, $data);
		$text = $this->text($html);

		// auto add email/subject to data params
		$data['to'] = $to;
		$data['subject'] = $subject;

		// create message
		$message = [
			'To' => $to,
			'Subject' => $subject,
			'From' => MAIL_FROM,
			'TrackOpens' => MAIL_TRACK_OPENS,
			'TextBody' => $text,
			'HtmlBody' => $html,
			'Tag' => $tags,
		];

		// connect to postmark client
        $client = new PostmarkClient(MAIL_PRIVATE_KEY);
        return $client->sendEmailBatch([$message]);
	}

    //
    // Email the dev
    //
    public function dev($subject, $template, $data)
    {
        return $this->send(MAIL_DEV, $subject, 'devmail', sprintf('Email/%s.twig', $template), $data);
    }

    //
    // Error email
    //
    public function error($data)
    {
        return $this->send(MAIL_DEV, 'Website Error', 'error', 'Email/error.twig', $data);
    }

	//
    // Get the text version of html emails
    //
    private function text($html)
    {
        // closing tags to add nl after
        $tags = ['</p>','<br />','<br>','<hr />','<hr>','</h1>','</h2>','</h3>','</h4>','</h5>','</h6>'];
        $html = str_replace($tags,"\n",$html);
        $html = strip_tags($html);

        // fix any html entity encoded characters
        $html = html_entity_decode($html, ENT_QUOTES);

        // Trim a lot of blank lines
        $html = trim(implode("\n", array_map('trim', explode("\n", $html))));
        return $html;
    }
}
