<?php

/**
 * GoogleAnalytics
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Services\Google;

class Analytics extends \XIVDB\Apps\AppHandler
{
    const URL = 'https://www.google-analytics.com/collect';

    private $data;

    function __construct()
    {
        $config = require FILE_CONFIG;
        $config = $config['analytics'];

        $this->data = [
            'v' => 1,
            'tid' => $config['id'],
            'cid' => $this->getModule('uuid4'),
        ];
    }

    /**
     * Send a page hit
     *
     * @param $dp = page
     * @param $dt = title
     * @param $dh = host
     */
    public function hit($data)
    {
        $data['t'] = 'pageview';
        $this->send($data);
    }

    /**
     * Send an event
     *
     * @param $data - data to merge
     */
    public function event($data)
    {
        $data['t'] = 'event';
        $this->send($data);
    }

    /**
     * Send to Google analytics
     *
     * @param $data - preformed post data
     */
    private function send($data)
    {
        // cache bust
        $data['z'] = mt_rand(0,999999);

        // merge
        $data = array_merge($this->data, $data);

        // Options
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded\r\n',
                'content' => http_build_query($data),
            ],
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents(self::URL, false, $context);
    }
}
