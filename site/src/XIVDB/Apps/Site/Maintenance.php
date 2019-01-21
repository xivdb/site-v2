<?php

namespace XIVDB\Apps\Site;

//
// Maintenance class
//
class Maintenance extends \XIVDB\Apps\AppHandler
{
    private $enabled = false;

    function __construct($API = false)
    {
        if (isset($_GET['x1vdb'])) {
            $expire = TIME_60MINUTES * 24;

            @setcookie('nomaint', 'disable', time()+$expire, '/', COOKIE_DOMAIN);
            header('Location: /');
            exit;
        }

        $dbs = $this->getModule('database');

        // check if maintenance is enabled
        $dbs->QueryBuilder
            ->select('*', false)
            ->from('site_settings')
            ->where('id = 1')
            ->limit(0,1);

        $settings = $dbs->get()->one();

        $this->enabled = strlen($settings['maintenance']) > 2 ? true : false;

        // set define for further use
        define('SITE_UNDER_MAINTENANCE', $this->enabled);

        // if on maint
        if (SITE_UNDER_MAINTENANCE && !isset($_COOKIE['nomaint']) && !isset($_GET['_']))
        {
            $maintmessage = nl2br($settings['maintenance']);

            // if on API, show a json error
            if ($API) {
                header('Content-Type: application/json');
                header('Access-Control-Allow-Origin: *');
                exit(json_encode([
                    'error' => 'XIVDB down for maintenance: '. strip_tags($maintmessage),
                ]));
            }

            // show maint page
            $html = file_get_contents(ROOT_VIEWS .'/Default/maintenance.twig');
            $html = str_ireplace('{{ message }}',  $maintmessage, $html);
            exit($html);
        }
    }
}
