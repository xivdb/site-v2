<?php

namespace Sync\App;

/**
 * Class Lodestone
 * @package Sync\App
 */
class Lodestone
{
    private $http;
    private $parser;

    function __construct()
    {
        $this->http = new \Sync\Modules\HttpRequest();
        $this->parser = new \Sync\Parser\Lodestone();
    }

    //
    // Request character data from lodestone
    //
    public function parse($type, $url)
    {
        $html = $this->http->get($url);
        if (!$html) {
            return false;
        }

        switch($type) {
            case 'banners':
                $data = $this->parser->parseBanners($html);
                break;
                
            case 'topics':
                $data = $this->parser->parseTopics($html);
                break;

            case 'notices':
                $data = $this->parser->parseNotices($html);
                break;

            case 'maintenance':
                $data = $this->parser->parseMaintenance($html);
                break;

            case 'updates':
                $data = $this->parser->parseUpdates($html);
                break;

            case 'status':
                $data = $this->parser->parseStatus($html);
                break;

            case 'worldstatus':
                $data = $this->parser->parseWorldStatus($html);
                break;

            case 'feast':
                $data = $this->parser->parseFeast($html);
                break;

            case 'deepdungeon':
                $data = $this->parser->parseDeepDungeon($html);
                break;
        }

        if (!$data) {
            return false;
        }

        return $data;
    }

    /**
     * Parse SE Dev blog, special, because its XML!
     * @param $url
     */
    public function parseDevBlog($url)
    {
        $html = $this->http->get($url);
        if (!$html) {
            return false;
        }

        $xml = simplexml_load_string($html, null, LIBXML_NOCDATA);
        $json = json_decode(json_encode($xml), true);

        return $json;
    }

    /**
     * Another special one as it has to first go to
     * the forums, get the dev tracking url, then
     * parse each individual dev tracking post
     *
     * For now, defaults to English
     *
     * @param $url
     */
    public function parseDevTracker($url, $lang = 'en')
    {
        $html = $this->http->get($url);
        if (!$html) {
            return false;
        }

        // get dev tracking url
        $devTrackerUrl = $this->parser->parseDevTrackingUrl($html, $lang);
        if (!$devTrackerUrl) {
            return false;
        }

        // get dev tracking search results
        $html = $this->http->get($devTrackerUrl);
        if (!$html) {
            return false;
        }

        // get dev link posts
        $devlinks = $this->parser->parseDevPostLinks($html);
        if (!$devlinks) {
            return false;
        }

        $data = [];
        foreach($devlinks as $url) {
            $html = $this->http->get(LODESTONE_FORUMS . $url);
            $postId = str_ireplace('post', null, explode('#', $url)[1]);
            $data[] = [
                'id' => $postId,
                'thread' => $this->parser->parseDevPost($html, $postId),
            ];
        }

        return $data;
    }
}
