<?php

namespace Sync\Parser;

/**
 * Parse character data
 * Class Search
 * @package Sync\Parser
 */
class Lodestone extends ParserHelper
{
    private $xivdb;

    function __construct()
    {
        $this->xivdb = new \Sync\Modules\XIVDBApi();
    }

    /**
     * @param $html
     * @return array|bool
     */
    public function parseBanners($html)
    {
        $this->setInitialDocument($html);

        $entries = $this->getDocument()->find('#slider_bnr_area li');
        $results = [];

        foreach($entries as $entry) {
            $results[] = [
                'url' => $entry->find('a',0)->href,
                'banner' => explode('?', $entry->find('img', 0)->getAttribute('src'))[0],
            ];
        }

        $this->add('entries', $results);
        return $this->data;
    }

    /**
     * @param $html
     * @return array|bool
     */
	public function parseTopics($html)
	{
		$html = $this->trim($html, 'class="ldst__main"', 'class="ldst__side"');
		$this->setInitialDocument($html);

        $entries = $this->getDocumentFromClassname('.news__content')->find('li.news__list--topics');
        $results = [];

        foreach($entries as $entry) {
            // fix up lodestone urls
            $html = $entry->find('.news__list--banner p')->innerHtml();
            $html = str_ireplace('/lodestone/', LODESTONE_URL.'/lodestone/', $html);

            $results[] = [
                'time' => $this->getTimestamp($entry->find('.news__list--time', 0)),
                'title' => $entry->find('.news__list--title')->plaintext,
                'url' => LODESTONE_URL .  $entry->find('.news__list--title a', 0)->getAttribute('href'),
                'banner' => explode('?', $entry->find('.news__list--img img', 0)->getAttribute('src'))[0],
                'html' => $html,
            ];
        }

        $this->add('entries', $results);
		return $this->data;
	}

    /**
     * @param $html
     * @return array
     */
    public function parseNotices($html)
    {
        $html = $this->trim($html, 'class="ldst__main"', 'class="ldst__side"');
        $this->setInitialDocument($html);

        $entries = $this->getDocumentFromClassname('.news__content')->find('li.news__list');
        $results = [];

        foreach($entries as $entry) {
            $results[] = [
                'time' => $this->getTimestamp($entry->find('.news__list--time', 0)),
                'title' => $entry->find('.news__list--title')->plaintext,
                'url' => LODESTONE_URL . $entry->find('.news__list--link', 0)->getAttribute('href'),
            ];
        }

        $this->add('entries', $results);
        return $this->data;
    }

    /**
     * @param $html
     * @return array
     */
    public function parseMaintenance($html)
    {
        $html = $this->trim($html, 'class="ldst__main"', 'class="ldst__side"');
        $this->setInitialDocument($html);

        $entries = $this->getDocumentFromClassname('.news__content')->find('li.news__list');
        $results = [];

        foreach($entries as $entry) {
            $tag = $entry->find('.news__list--tag')->plaintext;
            $title = $entry->find('.news__list--title')->plaintext;
            $title = str_ireplace($tag, null, $title);

            $results[] = [
                'time' => $this->getTimestamp($entry->find('.news__list--time', 0)),
                'title' => $title,
                'url' => LODESTONE_URL . $entry->find('.news__list--link', 0)->getAttribute('href'),
                'tag' => $tag,
            ];
        }

        $this->add('entries', $results);
        return $this->data;
    }

    /**
     * @param $html
     * @return array
     */
    public function parseUpdates($html)
    {
        $html = $this->trim($html, 'class="ldst__main"', 'class="ldst__side"');
        $this->setInitialDocument($html);

        $entries = $this->getDocumentFromClassname('.news__content')->find('li.news__list');
        $results = [];

        foreach($entries as $entry) {
            $results[] = [
                'time' => $this->getTimestamp($entry->find('.news__list--time', 0)),
                'title' => $entry->find('.news__list--title')->plaintext,
                'url' => LODESTONE_URL . $entry->find('.news__list--link', 0)->getAttribute('href'),
            ];
        }

        $this->add('entries', $results);
        return $this->data;
    }

    /**
     * @param $html
     * @return array
     */
    public function parseStatus($html)
    {
        $html = $this->trim($html, 'class="ldst__main"', 'class="ldst__side"');
        $this->setInitialDocument($html);

        $entries = $this->getDocumentFromClassname('.news__content')->find('li.news__list');
        $results = [];

        foreach($entries as $entry) {
            $tag = $entry->find('.news__list--tag')->plaintext;
            $title = $entry->find('.news__list--title')->plaintext;
            $title = str_ireplace($tag, null, $title);

            $results[] = [
                'time' => $this->getTimestamp($entry->find('.news__list--time', 0)),
                'title' => $title,
                'url' => LODESTONE_URL . $entry->find('.news__list--link', 0)->getAttribute('href'),
                'tag' => $tag,
            ];
        }

        $this->add('entries', $results);
        return $this->data;
    }

    /**
     * @param $html
     * @return array
     */
    public function parseWorldStatus($html)
    {
        $html = $this->trim($html, 'class="ldst__main"', 'class="ldst__side"');
        $this->setInitialDocument($html);

        $entries = $this->getDocumentFromClassname('.parts__space--pb16')->find('div.item-list__worldstatus');
        $results = [];

        foreach($entries as $entry) {
            $results[] = [
                'title' => trim($entry->find('h3')->plaintext),
                'status' => trim($entry->find('p')->plaintext),
            ];
        }

        $this->add('entries', $results);
        return $this->data;
    }

    /**
     * @param $html
     * @return array
     */
    public function parseFeast($html)
    {
        $this->setInitialDocument($html);

        $entries = $this->getDocument()->find('.wolvesden__ranking__table tr');
        $results = [];

        foreach($entries as $node) {
            $results[] = [
                'character' => [
                    'id' => explode('/', $node->getAttribute('data-href'))[3],
                    'name' => trim($node->find('.wolvesden__ranking__result__name h3', 0)->plaintext),
                    'server' =>trim( $node->find('.wolvesden__ranking__result__world', 0)->plaintext),
                    'avatar' => explode('?', $node->find('.wolvesden__ranking__result__face img', 0)->src)[0],
                ],
                'leaderboard' => [
                    'rank' => $node->find('.wolvesden__ranking__result__order', 0)->plaintext,
                    'rank_previous' => trim($node->find('.wolvesden__ranking__td__prev_order', 0)->plaintext),
                    'win_count' => trim($node->find('.wolvesden__ranking__result__win_count', 0)->plaintext),
                    'win_rate' => str_ireplace('%', null, trim($node->find('.wolvesden__ranking__result__winning_rate', 0)->plaintext)),
                    'matches' => trim($node->find('.wolvesden__ranking__result__match_count', 0)->plaintext),
                    'rating' => trim($node->find('.wolvesden__ranking__result__match_rate', 0)->plaintext),
                    'rank_image' => @trim($node->find('.wolvesden__ranking__td__rank img', 0)->src)
                ],
            ];
        }

        $this->add('results', $results);
        return $this->data;
    }

    /**
     * @param $html
     * @return array
     */
    public function parseDeepDungeon($html)
    {
        $this->setInitialDocument($html);

        $entries = $this->getDocument()->find('.deepdungeon__ranking__wrapper__inner li');
        $results = [];

        foreach($entries as $node) {
            if ($node->find('.deepdungeon__ranking__job', 0)) {
                $classjob = $node->find('.deepdungeon__ranking__job img', 0)->getAttribute('title');
            } else {
                $classjob = $this->getDocument()->find('.deepdungeon__ranking__select_job', 0)->find('a.selected', 0)->find('img', 0)->getAttribute('title');
            }

            $results[] = [
                'character' => [
                    'id' => explode('/', $node->getAttribute('data-href'))[3],
                    'name' => trim($node->find('.deepdungeon__ranking__result__name h3', 0)->plaintext),
                    'server' =>trim( $node->find('.deepdungeon__ranking__result__world', 0)->plaintext),
                    'avatar' => explode('?', $node->find('.deepdungeon__ranking__face__inner img', 0)->src)[0],
                ],
                'classjob' => [
                    'name' => $classjob,
                    'id' => $this->xivdb->getRoleId($classjob),
                ],
                'leaderboard' => [
                    'rank' => $node->find('.deepdungeon__ranking__result__order', 0)->plaintext,
                    'score' => trim($node->find('.deepdungeon__ranking__data--score', 0)->plaintext),
                    'time' => $this->getTimestamp($node->find('.deepdungeon__ranking__data--time')),
                    'floor' => filter_var($node->find('.deepdungeon__ranking__data--reaching', 0)->plaintext, FILTER_SANITIZE_NUMBER_INT),
                ],
            ];
        }

        $this->add('results', $results);
        return $this->data;
    }

    /**
     * @param $html
     * @param $lang
     * @return mixed
     */
    public function parseDevTrackingUrl($html, $lang)
    {
        $trackerNumber = [
            'ja' => 0,
            'en' => 1,
            'fr' => 2,
            'de' => 3,
        ][$lang];

        $this->setInitialDocument($html);

        $link = $this->getDocument()->find('.devtrack_btn', $trackerNumber)->getAttribute('href');
        return $link;
    }

    /**
     * @param $html
     * @return array
     */
    public function parseDevPostLinks($html)
    {
        $this->setInitialDocument($html);
        $posts = $this->getDocument()->find('.blockbody li');

        $links = [];
        foreach($posts as $node) {
            $links[] = $node->find('.posttitle a', 0)->getAttribute('href');
        }

        return $links;
    }

    /**
     * @param $html
     * @param $postId
     * @return array
     */
    public function parseDevPost($html, $postId)
    {
        $this->setInitialDocument($html);

        $post = $this->getDocument();

        // get postcount
        $postcount = $post->find('#postpagestats_above', 0)->plaintext;
        $postcount = explode(' of ', $postcount)[1];
        $postcount = filter_var($postcount, FILTER_SANITIZE_NUMBER_INT);

        $data = [
            'title' => $post->find('.threadtitle a', 0)->plaintext,
            'url' => LODESTONE_FORUMS . $post->find('.threadtitle a', 0)->getAttribute('href') . sprintf('?p=%s#post%s', $postId, $postId),
            'post_count' => $postcount,
        ];

        // get post
        $post = $post->find('#post_'. $postId);

        // todo : translate ...
        $timestamp = $post->find('.posthead .date', 0)->plaintext;

        // remove invisible characters
        $timestamp = preg_replace('/[[:^print:]]/', ' ', $timestamp);
        $timestamp = str_ireplace('-', '/', $timestamp);

        // fix time from Tokyo to Europe
        $date = new \DateTime($timestamp, new \DateTimeZone('Asia/Tokyo'));
        $date->setTimezone(new \DateTimeZone('UTC'));
        $timestamp = $date->format('U');

        // get colour
        $color = str_ireplace(['color: ', ';'], null, $post->find('.username span', 0)->getAttribute('style'));

        // fix some post stuff
        $message = trim($post->find('.postcontent', 0)->innerHtml());

        // get signature
        $signature = false;
        if ($post->find('.signaturecontainer', 0)) {
            $signature = trim($post->find('.signaturecontainer', 0)->plaintext);
        }

        // create data
        $data['user'] = [
            'username' => trim($post->find('.username span', 0)->plaintext),
            'color' => $color,
            'title' => trim($post->find('.usertitle', 0)->plaintext),
            'avatar' => LODESTONE_FORUMS . $post->find('.postuseravatar img', 0)->src,
            'signature' => $signature,
        ];

        // clean up the message
        $replace = [
            "\t" => null,
            "\n" => null,
            '&#13;' => null,
            'images/' => LODESTONE_FORUMS .'images/',
            'members/' => LODESTONE_FORUMS .'members/',
            'showthread.php' => LODESTONE_FORUMS .'showthread.php',
        ];

        $message = str_ireplace(array_keys($replace), $replace, $message);
        $message = trim($message);

        $dom = new \DOMDocument;
        $dom->loadHTML($message);
        $message = $dom->saveXML();

        $remove = [
            '<?xml version="1.0" standalone="yes"?>',
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">',
            '<html>', '</html>', '<head>', '</head>',
        ];

        $message = str_ireplace($remove, null, $message);
        $message = str_ireplace(['<body>', '</body>'], ['<article>', '</article>'], $message);

        $data['time'] = $timestamp;
        $data['message'] = trim($message);

        return $data;
    }
}
