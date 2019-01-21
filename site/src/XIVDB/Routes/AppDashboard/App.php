<?php

namespace XIVDB\Routes\AppDashboard;

//
// Manager Application
//
class App extends \XIVDB\Routes\RouteHandler
{
    use AppHome;
    use AppDevBlog;
    use AppTranslations;
    use AppGameData;
    use AppLibraData;
    use AppDevTasks;
    use AppMapper;

    //
    // check permissions
    //
    protected function authenticate()
    {
        // Must be logged in for any manager stuff
		$this->mustBeOnline();
    }
}
