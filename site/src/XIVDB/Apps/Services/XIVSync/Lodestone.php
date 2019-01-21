<?php

namespace XIVDB\Apps\Services\XIVSync;

trait Lodestone
{
	public function getBanners()
	{
		return $this->api('/lodestone/banners', true);
	}

	public function getTopics()
	{
		return $this->api('/lodestone/topics', true);
	}

	public function getNotices()
	{
		return $this->api('/lodestone/notices', true);
	}

	public function getMaintenance()
	{
		return $this->api('/lodestone/maintenance', true);
	}

	public function getStatus()
	{
		return $this->api('/lodestone/status', true);
	}

	public function getCommunity()
	{
		return [];
	}

	public function getEvents()
	{
		return [];
	}

    public function getDevPosts()
    {
        return $this->api('/lodestone/devtracker');
    }

    public function getPopularPosts()
    {
        return $this->api('/forums/popularposts');
    }
}
