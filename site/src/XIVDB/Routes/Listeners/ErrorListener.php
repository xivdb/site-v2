<?php

namespace XIVDB\Routes\Listeners;

//
// ErrorListener
//
trait ErrorListener
{
    protected function registerErrorListener()
    {
        // if we're on dev environment, we don't neeed these
        if (DEV) {
            return;
        }

        // attach error listener
        $this->SilexApplication->error(function (\Exception $e, $code)
        {
            switch ($code)
            {
                default:
                    error_log($e->getMessage(), 0);
                    /*
                     * Send an email when error is reported, can mass spam
                     *
                    $this->getModule('mail')->error([
                        'url' => $this->SilexApplication['request']->getUri(),
                        'message' => $e->getMessage(),
                        'server_data' => json_encode($_SERVER, JSON_PRETTY_PRINT),
                        'backtrace' => json_encode(debug_backtrace(), JSON_PRETTY_PRINT),
                    ]);
                    */
                    return $this->respond('Errors/exception.twig');

                   break;
                case 404:
                    return $this->show404($this->SilexApplication['request']);
                    break;
            }
        });
    }

    //
    // Show a 404 error page
    //
    public function show404($request = false)
    {
        if ($request && $this->isApiRequest($request)) {
            return $this->error('The content you are looking for could not be found.', 404);
        }

        return $this->respond('Errors/404.twig');
    }

    /**
     * @param bool $request
     * @return mixed
     */
    public function show404Characters($request = false)
    {
        if ($request && $this->isApiRequest($request)) {
            return $this->error('The character you are looking for could not be found. The character may still be being processed or the XIVSync service has gone down.', 404);
        }

        return $this->respond('Errors/404.twig');
    }
}
