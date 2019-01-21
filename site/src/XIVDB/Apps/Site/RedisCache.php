<?php

namespace XIVDB\Apps\Site;

use Redis;

/**
 * Class RedisCache
 *
 * @package XIVDB\Apps\Site
 */
class RedisCache extends \XIVDB\Apps\AppHandler
{
    use RedisCacheCharacters;

    /** @var Redis */
    public $redis;

    /** Redis timeout */
    const TIMEOUT = 8;

    /**
     * Get stats
     * @return bool|mixed
     */
    public function getStats()
    {
        $this->connect();
        return json_decode($this->redis->get('__stats'), true);
    }

    /**
     * Connect to Redis
     *
     * @return bool
     */
    private function connect()
    {
        if (!CACHE || $this->redis) {
            return false;
        }

        $config = require FILE_CONFIG;

        // basic config
        $host = $config['redis']['host'];
        $port = $config['redis']['port'];
        $timeout = self::TIMEOUT;

        // Init Redis
        $this->redis = new Redis();
        $this->redis->pconnect($host, $port, $timeout);
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);

        return true;
    }

    /**
     * Store an item in redis
     *
     * @param $key
     * @param $data
     * @param int $ttl
     */
    public function set($key, $data, $ttl = DEFAULT_CACHE_TIME, $ignoreLang = false)
    {
        $key = $ignoreLang ? $key : LANGUAGE .'_'. $key;
        if (!CACHE) {
            return;
        }

		// store in redis
        $this->connect();

        $data = json_encode($data);

        try {
            $this->redis->set($key, $data, $ttl);
        } catch (\Exception $e) {
            $this->connect();
            $this->redis->set($key, $data, $ttl);
        }
    }
    
    public function increment($key)
    {
        if (!CACHE) {
            return;
        }
        
        try {
            $this->connect();
            $this->redis->incr($key);
        } catch (\Exception $e) {
        }
    }

    /**
     * Get from block storage
     *
     * @param $key
     * @return bool|mixed
     */
    public function get($key, $ignoreLang = false)
    {
    	$key = $ignoreLang ? $key : LANGUAGE .'_'. $key;
        if (!CACHE) {
            return false;
        }

        $this->connect();

        try {
            $data = $this->redis->get($key);
        } catch (\Exception $e) {
            $this->connect();
            $data = $this->redis->get($key);
        }

        if (!$data) {
            return false;
        }

        return json_decode($data, true);
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
    	$key = LANGUAGE .'_'. $key;
        if (!CACHE) return false;

        // delete
        $this->connect();
        return $this->redis->delete($key);
    }

    /**
     * @param $data
     * @return string
     */
    public function hash($data)
    {
        ksort($data);
        $data = json_encode($data);
        return md5($data);
    }

    /**
     * @param $key
     * @return string
     */
    public function getBlockStorageFolder($key)
    {
        list($a, $b) = str_split(md5($key), 2);
        return sprintf('%s/%s/%s/', self::STORAGE_FOLDER, $a, $b);
    }
}
