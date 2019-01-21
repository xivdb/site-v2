<?php

namespace Sync\App;

//
// Search class
//
class Search
{
	private $http;
	private $parser;

	function __construct()
	{
		$this->http = new \Sync\Modules\HttpRequest();
		$this->parser = new \Sync\Parser\Search();
	}

    /**
     * Parse character search
     * @param $url
     * @return array|bool
     */
	public function parseCharacterSearch($url)
	{
		$html = $this->http->get($url);
		if (!$html) {
			return false;
		}

		$data = $this->parser->parseCharacterSearch($html);
		if (!$data) {
			return false;
		}

		return $data;
	}

    /**
     * Parse free company search
     * @param $url
     * @return array|bool
     */
    public function parseFreeCompanySearch($url)
    {
        $html = $this->http->get($url);
        if (!$html) {
            return false;
        }

        $data = $this->parser->parseFreeCompanySearch($html);
        if (!$data) {
            return false;
        }

        return $data;
    }

    /**
     * Parse linkshell search
     * @param $url
     * @return array|bool
     */
    public function parseLinkshellSearch($url)
    {
        $html = $this->http->get($url);
        if (!$html) {
            return false;
        }

        $data = $this->parser->parseLinkshellSearch($html);
        if (!$data) {
            return false;
        }

        return $data;
    }
}
