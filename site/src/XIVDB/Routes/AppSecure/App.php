<?php

namespace XIVDB\Routes\AppSecure;

//
// Frontend Application
//
class App extends \XIVDB\Routes\RouteHandler
{
    use AppAccount;
    use AppUser;
    use AppTooltips;
}
