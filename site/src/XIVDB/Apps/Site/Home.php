<?php

/**
 * Home
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Site;

class Home extends \XIVDB\Apps\AppHandler
{
    //
    // Get lodestone json
    //
    public function getLodestone()
    {
        $json = ROOT_WEB .'/assets/lodestone.json';
        if (file_exists($json)) {
            $json = file_get_contents($json);
            $json = json_decode($json, true);
            return $json;
        }

        return false;
    }

    //
    // Get dev tracker json
    //
    public function getDevTrackerJson()
    {
        $json = ROOT_WEB .'/assets/devtracker.json';
        if (file_exists($json)) {
            $json = file_get_contents($json);
            $json = json_decode($json, true);

            foreach($json as $i => $dev) {
                $tidy = new \Tidy();

                $tidy->parseString($dev['thread']['message'], [
                    'indent' => true,
                ], 'utf8');

                $tidy->cleanRepair();

                $res = $tidy->value;
                $res = str_ireplace([
                    'â—†', 'â€™', 'â€', 'œ'
                ], [
                    null, "'", null, null
                ], $res);

                // replace all http with https
                $res = str_ireplace('http://', 'https://', $res);
                $dev['thread']['user']['avatar'] = str_ireplace('http://', 'https://', $dev['thread']['user']['avatar']);
                $dev['thread']['url'] = str_ireplace('http://', 'https://', $dev['thread']['url']);

                $dev['thread']['message'] = $res;

                $json[$i] = $dev;
            }

            return $json;
        }

        return false;
    }

    //
    // Get screenshots
    //
    public function getScreenshots()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from('content_screenshots')
            ->where('deleted != 1')
            ->order('time', 'desc')
            ->limit(0,20);

        $data = $dbs->get()->all();

        $content = $this->getModule('content');
        foreach($data as $i => $ss) {
            $contentType = $content->getContentType($ss['content']);
            $data[$i]['content'] = $content->setCid($ss['content'])->getContentToId($ss['content'], $ss['uniq']);
            $data[$i]['time'] = $this->getModule('moment', $ss['time'])->fromNow()->getRelative();
            $data[$i]['image'] = sprintf('/screenshots/%s/%s/small_%s', $contentType, $ss['uniq'], $ss['filename']);
        }

        return $data;
    }

    //
    // Get comments
    //
    public function getComments()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from('content_comments')
            ->where('deleted != 1')
            ->order('time', 'desc')
            ->limit(0,50);

        $data = $dbs->get()->all();

        $content = $this->getModule('content');
        $users = $this->getModule('users');
        foreach($data as $i => $cmt) {
            $contentType = $content->getContentType($cmt['content']);
            $member = $users->get($cmt['member'], true);

            $data[$i]['text'] = htmlentities($data[$i]['text']);
            $data[$i]['text'] = $this->markdown($data[$i]['text']);
            $data[$i]['content'] = $content->setCid($cmt['content'])->getContentToId($cmt['content'], $cmt['uniq']);
            $data[$i]['time'] = $this->getModule('moment', $cmt['time'])->fromNow()->getRelative();
            $data[$i]['member'] = $member;
        }

        return $data;
    }

    //
    // Get latest xivdb dev blog post
    //
    public function getLatestDevBlog()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['id', 'subject', 'updated'], false)
            ->from('site_devblog')
            ->where('published = 1')
            ->order('id', 'desc')
            ->limit(0,1);

        return $dbs->get()->one();
    }

    //
    // Get stats on recently obtained characterz
    //
    public function getCharacterStats()
    {
        $stats = file_get_contents('https://xivsync.com/?json=1');
        $stats = json_decode($stats, true);
        $stats = $stats['stats'];

        return [
            'updatedPastHour' => $stats[1],
            'updatedPastDay' => $stats[2],
            'achievementsPastHour' => $stats[7],
            'achievementsPastDay' => $stats[8],
            'addedPastHour' => $stats[4],
            'addedPastDay' => $stats[5],
            'totalCharacter' => $stats[9],
        ];
    }

    public function getSyncTimes()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*', false)
            ->from('sync_times')
            ->order('time', 'desc')
            ->limit(0,5);

        return $dbs->get()->all();
    }
}
