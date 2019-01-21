<?php

namespace XIVDB\Routes\AppFrontend;

//
// Frontend Application
//
class App extends \XIVDB\Routes\RouteHandler
{
    use AppComments;
    use AppContent;
    use AppDefault;
    use AppDev;
    use AppFeedback;
    use AppScreenshots;
	use AppSearch;
    use AppToolMaps;
    use AppToolShoppingCart;
    use AppToolGearsets;
    use AppToolLoreFinder;
    use AppToolIcons;
    use AppCommunity;
    use AppProfiles;
    use AppToolMapper;

    //
    // v1 redirect of old urls
    //
    protected function checkV1Redirect($request)
    {
        $redirect = false;
        $params = explode('/', key($request->query->all()));

        // support old v1 routes
        if (isset($params[1])) {
            $type = $params[0];
            $id = $params[1];
            $string = isset($params[2]) ? trim(strip_tags($params[2])) : '-';

            // switch based on tyope
            switch($type) {
                case 'item'; $redirect = sprintf('/item/%s/%s', $id, $string); break;
                case 'recipe'; $redirect = sprintf('/recipe/%s/%s', $id, $string); break;
                case 'skill'; $redirect = sprintf('/action/%s/%s', $id, $string); break;
                case 'dungeon'; $redirect = sprintf('/instance/%s/%s', $id, $string); break;
                case 'achievement'; $redirect = sprintf('/achievement/%s/%s', $id, $string); break;
                case 'minion'; $redirect = sprintf('/minion/%s/%s', $id, $string); break;
                case 'mount'; $redirect = sprintf('/mount/%s/%s', $id, $string); break;
                case 'leve'; $redirect = sprintf('/leve/%s/%s', $id, $string); break;
                case 'status'; $redirect = sprintf('/status/%s/%s', $id, $string); break;
                case 'huntinglog'; $redirect = sprintf('/huntinglog/%s/%s', $id, $string); break;
                case 'npc'; $redirect = sprintf('/npc/%s/%s', $id, $string); break;
                case 'monster'; $redirect = sprintf('/enemy/%s/%s', $id, $string); break;
                case 'fate'; $redirect = sprintf('/fate/%s/%s', $id, $string); break;
                case 'quest'; $redirect = sprintf('/quest/%s/%s', $id, $string); break;
            }
        }

        // Old tooltips link
        if ($params[0] == 'tooltips') {
            $redirect = 'https://github.com/viion/XIVDB-Tooltips';
        }

        if ($redirect) {
            header('Location: '. $redirect);
            exit();
        }
    }
}
