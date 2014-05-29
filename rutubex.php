<?php
/**
 * Created by PhpStorm.
 * User: rkoshkarov
 * Date: 27.05.14
 * Time: 15:28
 */

error_reporting(E_ALL);


class rutubex {
    private $apiUrl = 'http://rutube.ru/api';
    private static $token;

    protected function __clone() {
    }

    /**
     * mixed $auth
     */
    public function __construct($auth = '') {
        if (!empty($auth)) {
            if (is_array($auth)) {
                $t = $this->send('POST', '/accounts/token_auth/', $auth);
                if (!empty($t)) {
                    self::$token = $t->token;
                }
            } elseif(is_string($auth)) {
                self::$token = $auth;
            }
        }
    }

    private function _toArray($obj){
        $rc = (array)$obj;
        foreach($rc as &$field){
            if(is_object($field))$field = $this->_toArray($field);
        }
        return $rc;
    }

    /**
     * @param string $url
     * @param string $callback_url
     * @param string $errback_url
     * @param string $query_fields
     * @param string $extra
     * @param string $title
     * @param string $description
     * @param bool $is_hidden
     * @param int $category_id
     * @param int $type
     * @return array
     */
    private function _loadVideo($url='',$callback_url='',$errback_url='',$query_fields='',$extra='',$title='',$description='',$is_hidden=true,$category_id=13,$type=1) {
        $snd = array(
            'url' => $url,
            'callback_url' => $callback_url,
            'errback_url' => $errback_url,
            'query_fields' => $query_fields,
            'extra' => $extra,
            'title' => $title,
            'description' => $description,
            'is_hidden' => $is_hidden,
            'category_id' => $category_id,
            'type' => $type,
        );
        return $this->send('POST', '/video/', $snd);
    }

    /**
     * @param string $id
     * @return bool|mixed
     */
    private function _getVideoByTvShow($id = '') {
        return $this->send('GET', '/metainfo/contenttvs/'.$id, array('format' => 'json'));
    }

    /**
     * @param string $tvId
     * @param string $year
     * @param string $video_id
     * @param string $season
     * @param string $episode
     * @param string $type
     * @param string $ext_id
     * @internal param string $fragment
     * @internal param string $episode_global
     * @return bool|mixed
     */
    private function _addVideoToTvShow($tvId, $year='', $video_id='', $season='', $episode='', $type='',$ext_id='') {
        $snd = array(
            'tv' => $tvId,
            'year' => $year,
            'video_id' => $video_id,
            'season' => $season,
            'episode' => $episode,
            'type' => $type,
            'ext_id' => $ext_id,
        );
        return $this->send('POST', '/metainfo/contenttvs/', $snd, 'json');
    }

    /**
     * @param string $id
     * @param string $title
     * @param string $description
     * @param bool $is_hidden
     * @param int $category
     * @return bool|mixed
     */
    private function _editVideo($id='', $title='', $description='', $is_hidden=true, $category=13) {
        $snd = array(
            'title' => $title,
            'description' => $description,
            'is_hidden' => $is_hidden,
            'category' => $category,
        );
        return $this->send('PUT', '/video/'.$id, $snd);
    }

    /**
     * @param string $tvId
     * @param string $year
     * @param string $video_id
     * @param string $season
     * @param string $episode
     * @param string $fragment
     * @param string $episode_global
     * @param int|string $type
     * @param int|string $ext_id
     * @return bool|mixed
     */
    private function _editVideoToTvShow($tvId, $year='', $video_id='', $season='', $episode='', $fragment='', $episode_global='', $type=2, $ext_id=0) {
        $snd = array(
            'tv' => $tvId,
            'year' => $year,
            'video_id' => $video_id,
            'season' => $season,
            'episode' => $episode,
            'fragment' => $fragment,
            'episode_global' => $episode_global,
            'type' => $type,
            'ext_id' => $ext_id,
        );
        return $this->send('PUT', '/metainfo/contenttvs/'.$video_id, $snd);
    }

    /**
     * @param string $id
     * @return bool|mixed
     */
    public function getVideo($id = '') {
        $tv = $this->_getVideoByTvShow($id);
        $video = $this->send('GET', '/video/'.$id);
        if (!empty($tv)) {
            $video = array_merge((array)$tv, (array)$video);
        }

        return $video;
    }

    /**
     * @param string $videoUrl
     * @param string $name
     * @param string $descr
     * @param bool $isHidden
     * @param int $category
     * @param string $tvName
     * @param string $year
     * @param int $season
     * @param int $episode
     * @param int $type
     * @param int $extId
     * @return bool|mixed
     */
    public function addVideo($videoUrl='', $name='', $descr='', $isHidden=true, $category=13, $tvName='', $year='', $season=0, $episode=1, $type=2,$extId=0) {
        $year = (!empty($year)) ? $year : date('Y');
        if ($resV = $this->_loadVideo($videoUrl,'','','','',$name,$descr,$isHidden,$category)) {
            sleep(1);
            $resT = $this->_addVideoToTvShow(
                array('name' => $tvName),
                $year,
                $resV['video_id'],
                $season,
                $episode,
                $type,
                $extId
            );
            return array_merge($resV, $resT);
        }
    }

    public function editVideo($videoId, $name, $descr, $isHidden, $category, $tvName, $year, $season, $episode, $type,$extId) {
        $year = (!empty($year)) ? $year : date('Y');
        if ($resV = $this->_editVideo($videoId,$name,$descr,$isHidden,$category)) {
            sleep(1);
            $resT = $this->_editVideoToTvShow(
                $tvName,
                $year,
                $videoId,
                $season,
                $episode,
                $type,
                $extId
            );
            return array_merge($resV, $resT);
        }
    }

    /**
     * @param string $id
     * @return bool|mixed
     */
    public function deleteVideo($id = '') {
        return $this->send('DELETE', '/video/'.$id);
    }


    /**
     * @return bool|mixed
     */
    public function getVideoTypes() {
        return $this->send('GET', '/metainfo/contenttvstype/', array('format' => 'json'));
    }

    /**
     * @param string $id
     * @return bool|mixed
     */
    public function getTvShow($id = '') {
        return $this->send('GET', '/metainfo/tv/'.$id, array('format' => 'json'));
    }


    /**
     * @param string $method
     * @param string $apiMethod
     * @param array $data
     * @param string $type
     * @return bool|mixed
     */
    private function send($method = 'GET', $apiMethod = '', $data = array(), $type='query') {
        $head = array();
        $ch = curl_init($this->apiUrl.$apiMethod);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($type == 'query') {
            $data = http_build_query($data);
        } elseif($type == 'json') {
            $data = json_encode($data);
            array_push($head, 'Accept: application/json');
            array_push($head, 'Content-type: application/json');
        }
        if (!empty(self::$token)) {
            array_push($head, 'Authorization: Token '.self::$token);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        $resInfo = curl_getinfo($ch);
        /*var_dump($head);
        var_dump($data);
        var_dump($response);
        var_dump($resInfo);*/
        if (!$response && $resInfo['http_code'] > 300) {
            switch ($resInfo['http_code']) {
                case 400:
                    $this->errLog('Content error');
                    break;
                case 401:
                    $this->errLog('Auth error');
                    break;
                case 403:
                    $this->errLog('Not have permissions');
                    break;
                case 404:
                    $this->errLog('Not found');
                    break;
                case 500:
                    $this->errLog('API error');
                    break;
                default:
                    $this->errLog('Request error');
                    break;
            }
        } else {
            if (!empty($response)) {
                return $this->_toArray(json_decode($response));
            } else {
                return true;
            }
        }
    }

    /**
     * @param $code
     */
    private function errLog($code) {
        var_dump($code);
    }



}