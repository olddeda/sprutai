<?php

namespace common\modules\base\components\youtube;

use Exception;
use InvalidArgumentException;
use StdClass;
use yii\base\Component;

class Youtube extends Component
{
    /**
     * Order in search api
     */
    const ORDER_DATE = 'date';
    const ORDER_RATING = 'rating';
    const ORDER_RELEVANCE = 'relevance';
    const ORDER_TITLE = 'title';
    const ORDER_VIDEOCOUNT = 'videoCount';
    const ORDER_VIEWCOUNT = 'viewCount';

    /**
     * Event type
     */
    const EVENT_TYPE_LIVE = 'live';
    const EVENT_TYPE_COMPLETED = 'completed';
    const EVENT_TYPE_UPCOMING = 'upcoming';

    /**
     * Type in search api
     */
    const SEARCH_TYPE_CHANNEL = 'channel';
    const SEARCH_TYPE_PLAYLIST = 'playlist';
    const SEARCH_TYPE_VIDEO = 'video';

    /**
     * The API Key
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $referer;

    /**
     * @var string
     */
    public $sslPath;

    /**
     * @var array
     */
    public $APIs = [
        'videos.list' => 'https://www.googleapis.com/youtube/v3/videos',
        'search.list' => 'https://www.googleapis.com/youtube/v3/search',
        'channels.list' => 'https://www.googleapis.com/youtube/v3/channels',
        'playlists.list' => 'https://www.googleapis.com/youtube/v3/playlists',
        'playlistItems.list' => 'https://www.googleapis.com/youtube/v3/playlistItems',
        'activities' => 'https://www.googleapis.com/youtube/v3/activities',
    ];

    /**
     * @var array
     */
    public $page_info = [];

    /**
     * Override the API urls, so you can set them from a config
     *
     * @param array $APIs
     */
    public function setAPIs(array $APIs) {
        $this->APIs = $APIs;
    }

    /**
     * @param $vId
     *
     * @return StdClass
     * @throws Exception
     */
    public function getVideoInfo($vId) {
        $API_URL = $this->getApi('videos.list');
        $params = [
            'id' => $vId,
            'part' => 'id, snippet, contentDetails, player, statistics, status'
        ];

        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeSingle($apiData);
    }

    /**
     * @param string $url
     *
     * @return StdClass|null
     * @throws Exception
     */
    public function getVideoInfoUrl($url) {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
        if (count($matches)) {
            return $this->getVideoInfo($matches[0]);
        }
        return null;
    }


    /**
     * @param $vIds
     *
     * @return array
     * @throws Exception
     */
    public function getVideosInfo($vIds) {
        $ids = is_array($vIds) ? implode(',', $vIds) : $vIds;
        $API_URL = $this->getApi('videos.list');
        $params = [
            'id' => $ids,
            'part' => 'id, snippet, contentDetails, player, statistics, status'
        ];

        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeList($apiData);
    }


    /**
     * Simple search interface, this search all stuffs
     * and order by relevance
     *
     * @param $q
     * @param int $maxResults
     *
     * @return array
     * @throws Exception
     */
    public function search($q, $maxResults = 10) {
        $params = [
            'q' => $q,
            'part' => 'id, snippet',
            'maxResults' => $maxResults
        ];
        return $this->searchAdvanced($params);
    }

    /**
     * Search only videos
     *
     * @param string $q Query
     * @param integer $maxResults number of results to return
     * @param string $order Order by
     *
     * @return array API results
     * @throws Exception
     */
    public function searchVideos($q, $maxResults = 10, $order = null) {
        $params = [
            'q' => $q,
            'type' => 'video',
            'part' => 'id, snippet',
            'maxResults' => $maxResults
        ];
        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params);
    }

    /**
     * Search only videos in the channel
     *
     * @param string $q
     * @param string $channelId
     * @param integer $maxResults
     * @param string $order
     *
     * @return array
     * @throws Exception
     */
    public function searchChannelVideos($q, $channelId, $maxResults = 10, $order = null) {
        $params = [
            'q' => $q,
            'type' => 'video',
            'channelId' => $channelId,
            'part' => 'id, snippet',
            'maxResults' => $maxResults
        ];
        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params);
    }

    /**
     * @param $q
     * @param $channelId
     * @param int $maxResults
     * @param null $order
     *
     * @return array
     * @throws Exception
     */
    public function searchChannelLiveStream($q, $channelId, $maxResults = 10, $order = null) {
        $params = [
            'q' => $q,
            'type' => 'video',
            'eventType' => 'live',
            'channelId' => $channelId,
            'part' => 'id, snippet',
            'maxResults' => $maxResults
        ];

        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params);
    }
    
    /**
     * Generic Search interface, use any parameters specified in
     * the API reference
     *
     * @param $params
     * @param $pageInfo
     *
     * @return array
     * @throws Exception
     */
    public function searchAdvanced($params, $pageInfo = false) {
        $API_URL = $this->getApi('search.list');

        if (empty($params) || !isset($params['q'])) {
            throw new InvalidArgumentException('at least the Search query must be supplied');
        }

        $apiData = $this->api_get($API_URL, $params);
        if ($pageInfo) {
            return [
                'results' => $this->decodeList($apiData),
                'info' => $this->page_info
            ];
        } else {
            return $this->decodeList($apiData);
        }
    }

    /**
     * Generic Search Paginator, use any parameters specified in
     * the API reference and pass through nextPageToken as $token if set.
     *
     * @param $params
     * @param $token
     *
     * @return array
     * @throws Exception
     */
    public function paginateResults($params, $token = null) {
        if (!is_null($token)) {
            $params['pageToken'] = $token;
        }
        return $this->searchAdvanced($params, true);
    }

    /**
     * @param $username
     *
     * @return StdClass
     * @throws Exception
     */
    public function getChannelByName($username, $optionalParams = false) {
        $API_URL = $this->getApi('channels.list');
        $params = [
            'forUsername' => $username,
            'part' => 'id,snippet,contentDetails,statistics,invideoPromotion'
        ];
        if ($optionalParams) {
            $params = array_merge($params, $optionalParams);
        }
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeSingle($apiData);
    }

    /**
     * @param $id
     *
     * @return StdClass
     * @throws Exception
     */
    public function getChannelById($id, $optionalParams = false) {
        $API_URL = $this->getApi('channels.list');
        $params = [
            'id' => $id,
            'part' => 'id,snippet,contentDetails,statistics,invideoPromotion'
        ];
        if ($optionalParams) {
            $params = array_merge($params, $optionalParams);
        }
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeSingle($apiData);
    }

    /**
     * @param array $ids
     *
     * @param bool $optionalParams
     *
     * @return array
     * @throws Exception
     */
    public function getChannelsById($ids = [], $optionalParams = false) {
        $API_URL = $this->getApi('channels.list');
        $params = [
            'id' => implode(',', $ids),
            'part' => 'id,snippet,contentDetails,statistics,invideoPromotion'
        ];
        if ($optionalParams) {
            $params = array_merge($params, $optionalParams);
        }
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeList($apiData);
    }

    /**
     * @param $channelId
     * @param array $optionalParams
     *
     * @return array
     * @throws Exception
     */
    public function getPlaylistsByChannelId($channelId, $optionalParams = []) {
        $API_URL = $this->getApi('playlists.list');
        $params = [
            'channelId' => $channelId,
            'part' => 'id, snippet, status'
        ];
        if ($optionalParams) {
            $params = array_merge($params, $optionalParams);
        }
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeList($apiData);
    }


    /**
     * @param $id
     *
     * @return StdClass
     * @throws Exception
     */
    public function getPlaylistById($id) {
        $API_URL = $this->getApi('playlists.list');
        $params = [
            'id' => $id,
            'part' => 'id, snippet, status'
        ];
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeSingle($apiData);
    }

    /**
     * @param $playlistId
     *
     * @return array
     * @throws Exception
     */
    public function getPlaylistItemsByPlaylistId($playlistId, $maxResults = 50) {
        $params = [
            'playlistId' => $playlistId,
            'part' => 'id, snippet, contentDetails, status',
            'maxResults' => $maxResults
        ];
        return $this->getPlaylistItemsByPlaylistIdAdvanced($params);
    }

    /**
     * @param $params
     * @param bool|false $pageInfo
     *
     * @return array
     * @throws Exception
     */
    public function getPlaylistItemsByPlaylistIdAdvanced($params, $pageInfo = false) {
        $API_URL = $this->getApi('playlistItems.list');

        if (empty($params) || !isset($params['playlistId'])) {
            throw new InvalidArgumentException('at least the playlist id must be supplied');
        }

        $apiData = $this->api_get($API_URL, $params);
        if ($pageInfo) {
            return [
                'results' => $this->decodeList($apiData),
                'info' => $this->page_info
            ];
        } else {
            return $this->decodeList($apiData);
        }
    }

    /**
     * @param $channelId
     *
     * @param bool $optionalParams
     *
     * @return array
     * @throws Exception
     */
    public function getActivitiesByChannelId($channelId, $optionalParams = false) {
        if (empty($channelId)) {
            throw new InvalidArgumentException('ChannelId must be supplied');
        }
        $API_URL = $this->getApi('activities');
        $params = [
            'channelId' => $channelId,
            'part' => 'id, snippet, contentDetails'
        ];
        if ($optionalParams) {
            $params = array_merge($params, $optionalParams);
        }
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeList($apiData);
    }

    /**
     * Parse a youtube URL to get the youtube Vid.
     * Support both full URL (www.youtube.com) and short URL (youtu.be)
     *
     * @param string $youtube_url
     *
     * @return string Video Id
     * @throws Exception
     */
    public static function parseVIdFromURL($youtube_url) {
        $videoId = null;
        if (strpos($youtube_url, 'youtube.com')) {
            if (strpos($youtube_url, 'embed')) {
                $path = static::_parse_url_path($youtube_url);
                $videoId = substr($path, 7);
            }
            if ($params = static::_parse_url_query($youtube_url)) {
                $videoId = isset($params['v']) ? $params['v'] : null;
            }
        } else if (strpos($youtube_url, 'youtu.be')) {
            $path = static::_parse_url_path($youtube_url);
            $videoId = substr($path, 1);
        }

        if (empty($videoId)) {
            throw new Exception('The supplied URL does not look like a Youtube URL');
        }

        return $videoId;
    }

    /**
     * Get the channel object by supplying the URL of the channel page
     *
     * @param string $youtube_url
     *
     * @return object Channel object
     * @throws Exception
     */
    public function getChannelFromURL($youtube_url) {
        if (strpos($youtube_url, 'youtube.com') === false) {
            throw new Exception('The supplied URL does not look like a Youtube URL');
        }

        $path = static::_parse_url_path($youtube_url);
        if (strpos($path, '/channel') === 0) {
            $segments = explode('/', $path);
            $channelId = $segments[count($segments) - 1];
            $channel = $this->getChannelById($channelId);
        } else if (strpos($path, '/user') === 0) {
            $segments = explode('/', $path);
            $username = $segments[count($segments) - 1];
            $channel = $this->getChannelByName($username);
        } else {
            throw new Exception('The supplied URL does not look like a Youtube Channel URL');
        }

        return $channel;
    }
    
    /**
     * @param $name
     *
     * @return mixed
     */
    public function getApi($name) {
        return $this->APIs[$name];
    }
    
    /**
     * Decode the response from youtube, extract the single resource object.
     * (Don't use this to decode the response containing list of objects)
     *
     * @param string $apiData the api response from youtube
     *
     * @return StdClass  an Youtube resource object
     * @throws Exception
     */
    public function decodeSingle(&$apiData) {
        $resObj = json_decode($apiData);
        if (isset($resObj->error)) {
            $msg = "Error ".$resObj->error->code." ".$resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : ".$resObj->error->errors[0]->reason;
            }
            throw new Exception($msg, $resObj->error->code);
        } else {
            $itemsArray = $resObj->items;
            if (!is_array($itemsArray) || count($itemsArray) == 0) {
                return false;
            } else {
                return $itemsArray[0];
            }
        }
    }
    
    /**
     * Decode the response from youtube, extract the list of resource objects
     *
     * @param string $apiData response string from youtube
     *
     * @return array Array of StdClass objects
     * @throws Exception
     */
    public function decodeList(&$apiData) {
        $resObj = json_decode($apiData);
        if (isset($resObj->error)) {
            $msg = "Error ".$resObj->error->code." ".$resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : ".$resObj->error->errors[0]->reason;
            }
            throw new Exception($msg, $resObj->error->code);
        } else {
            $this->page_info = [
                'resultsPerPage' => $resObj->pageInfo->resultsPerPage,
                'totalResults' => $resObj->pageInfo->totalResults,
                'kind' => $resObj->kind,
                'etag' => $resObj->etag,
                'prevPageToken' => null,
                'nextPageToken' => null
            ];

            if (isset($resObj->prevPageToken)) {
                $this->page_info['prevPageToken'] = $resObj->prevPageToken;
            }
            if (isset($resObj->nextPageToken)) {
                $this->page_info['nextPageToken'] = $resObj->nextPageToken;
            }

            $itemsArray = $resObj->items;
            if (!is_array($itemsArray) || count($itemsArray) == 0) {
                return false;
            } else {
                return $itemsArray;
            }
        }
    }


    /**
     * Using CURL to issue a GET request
     *
     * @param $url
     * @param $params
     *
     * @return mixed
     * @throws Exception
     */
    public function api_get($url, $params) {
        $params['key'] = $this->apiKey;
        
        $tuCurl = curl_init();
        if ($this->sslPath !== null) {
            curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($tuCurl, CURLOPT_CAINFO, __DIR__.'/cert/cacert.pem');
            curl_setopt($tuCurl, CURLOPT_CAPATH, __DIR__.'/cert/cacert.pem');
        }
        curl_setopt($tuCurl, CURLOPT_URL, $url.(strpos($url, '?') === false ? '?' : '').http_build_query($params));
        if ($this->referer !== null) {
            curl_setopt($tuCurl, CURLOPT_REFERER, $this->referer);
        }
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        $tuData = curl_exec($tuCurl);
        if (curl_errno($tuCurl)) {
            throw new Exception('Curl Error : '.curl_error($tuCurl), curl_errno($tuCurl));
        }
        return $tuData;
    }


    /**
     * Parse the input url string and return just the path part
     *
     * @param string $url the URL
     *
     * @return string      the path string
     */
    public static function _parse_url_path($url) {
        return parse_url($url, PHP_URL_PATH);
    }


    /**
     * Parse the input url string and return an array of query params
     *
     * @param string $url the URL
     *
     * @return array      array of query params
     */
    public static function _parse_url_query($url) {
        $queryString = parse_url($url, PHP_URL_QUERY);

        $params = [];

        parse_str($queryString, $params);

        if (count($params) === 0) {
            return $params;
        }

        return array_filter($params);
    }
}