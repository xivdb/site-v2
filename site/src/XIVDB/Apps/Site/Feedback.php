<?php

/**
 * Feedback
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Site;

use Symfony\Component\HttpFoundation\Request;

class Feedback extends \XIVDB\Apps\AppHandler
{
    const TABLE = 'site_feedback';
    const TABLE_RESPONSE = 'site_feedback_responses';

    //
    // List all feedback
    //
    public function all($folder = null)
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*', false)
            ->from(self::TABLE);

        // if a folder has been passed
        // - else show where null
        if ($folder) {
            $dbs->QueryBuilder
                ->where('folder = :folder')
                ->bind('folder', $folder);
        } else {
            $dbs->QueryBuilder
                ->where(["folder = ''", 'folder IS NULL'], 'OR');
        }

        $dbs->QueryBuilder
            ->where('deleted = 0')
            ->order('updated', 'DESC')
            ->order('added', 'DESC');


        return $dbs->get()->all();
    }

    //
    // Get a list of folders
    //
    public function folders()
    {
        $dbs = $this->getModule('database');
        return $dbs->sql("SELECT distinct(folder) as name FROM site_feedback WHERE folder != '' and deleted = 0 GROUP BY folder");
    }

    //
    // Get feedback for a specific ID
    //
    public function get($id)
    {
        $dbs = $this->getModule('database');
        $users = $this->getModule('users');

        // get feedback
        $dbs->QueryBuilder
            ->select('*', false)
            ->from(self::TABLE)
            ->where('id = :id')
            ->bind('id', $id)
            ->limit(0,1);

        $feedback = $dbs->get()->one();
        if (!$feedback) {
            return false;
        }

        // fix some data
        $feedback['data'] = json_decode($feedback['data'], true);
        $feedback['message'] = $this->markdown($feedback['message']);

        // get user
        if ($feedback['user']) {
            $feedback['user'] = $users->get($feedback['user']);
        }

        // get feedback responses
        $dbs->QueryBuilder
            ->select('*', false)
            ->from(self::TABLE_RESPONSE)
            ->where('feedback = :id')
            ->bind('id', $id)
            ->order('added', 'DESC');

        $responses = $dbs->get()->all();
        foreach($responses as $i => $res) {
            $res['user'] = $users->get($res['user']);
            $res['message'] = $this->markdown($res['message']);
            $responses[$i] = $res;
        }

        $feedback['responses'] = $responses;
        return $feedback;
    }

    //
    // Delete some feedback
    //
    public function delete($id)
    {
        if (!$id) {
            return false;
        }

        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->update(self::TABLE)
            ->set([ 'deleted' => 1 ])
            ->where('id = :id')
            ->bind('id', $id);

        $dbs->execute();
        return true;
    }

    //
    // Add feedback to tag (technically a folder)
    //
    public function tag($id, $folder)
    {
        if (!$id || !$folder) {
            return false;
        }

        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->update(self::TABLE)
            ->set([ 'folder' => ':folder' ])
            ->bind('folder', $folder)
            ->where('id = :id')
            ->bind('id', $id);

        $dbs->execute();
        return true;
    }

    //
    // Submit some feedback
    //
    public function submit($request)
    {
        // you need to be logged in to submit feedback
        if (!$this->getUser()) {
            return;
        }

        $dbs = $this->getModule('database');

        // insert new feedback
        $dbs->QueryBuilder
            ->insert(self::TABLE)
            ->schema([
                'user', 'category', 'section', 'message',
                'title', 'url', 'data', 'added'
            ])
            ->values([
                $this->getUser()->id,
                ':category', ':section', ':message',
                ':title', ':url', ':info', ':date'
            ])
            ->bind('category', $request->get('category'))
            ->bind('section', $request->get('section'))
            ->bind('message', $request->get('message'))
            ->bind('title', $request->get('title'))
            ->bind('url', $request->get('url'))
            ->bind('info', json_encode($request->get('info')))
            ->bind('date', date(DATE_MYSQL));

        // get submission id
        $submitId = $dbs->execute()->id();

        // send mail
        $mail = $this->getModule('mail');
        $mail->dev('(XIVDB Feedback) '. $request->get('title'), 'feedback_new', [
            'url' => URL .'/feedback/view/'. $submitId,
            'message' => $request->get('message'),
            'title' => $request->get('title'),
        ]);

        return $submitId;
    }

    //
    // Reply to some feedback
    //
    public function reply($request)
    {
        // you need to be logged in to submit feedback
        if (!$this->getUser()) {
            return;
        }

        $dbs = $this->getModule('database');

        // insert new feedback
        $dbs->QueryBuilder
            ->insert(self::TABLE_RESPONSE)
            ->schema([
                'user', 'feedback', 'message'
            ])
            ->values([ $this->getUser()->id, ':fid', ':message' ])
            ->bind('fid', $request->get('fid'))
            ->bind('message', $request->get('message'));

        // get submission id
        $dbs->execute();

        // List of people to email
        $emails = [];

        // get author
        $feedback = $this->get($request->get('fid'));
        if (isset($feedback['user']->email)) {
            $emails[$feedback['user']->email] = 1;
        }

        // Get everyone who has responded
        foreach($feedback['responses'] as $response) {
            $emails[$response['user']->email] = 1;
        }

        // Set email title
        if ($emails > 0) {
            $mail = $this->getModule('mail');
            $title = '(XIVDB Feedback Response) '. $feedback['title'];

            // send mail to all involved
            foreach($emails as $email => $i) {
                $mail->send($email, $title, 'feedback_response', 'Email/feedback_new.twig', [
                    'url' => URL .'/feedback/view/'. $request->get('fid'),
                    'message' => $request->get('message'),
                    'title' => $title,
                ]);
            }
        }

        return true;
    }
}
