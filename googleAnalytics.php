<?php

class googleAnalytics {

    public $username = "google email";     
    public $password = "email password";     
    public $profile = "ga Google analytics profile id";     
    public $authToken = NULL; 

   public function getRequirements() {
        if (!defined('XML_ERROR_NONE')) {
            return FALSE;
        }
        if (!function_exists('curl_init')) {
            return FALSE;
        }
        return TRUE;
    }

   public function getAuthToken() {
        if (empty($this->username) || empty($this->password)) {
            return FALSE;
        }
        if (!empty($this->authToken)) {
            return $this->authToken;
        }
        $data = array(
            'accountType' => 'GOOGLE',
            'Email' => $this->username,
            'Passwd' => $this->password,
            'source' => 'wp-analytics 1.0',
            'service' => 'analytics'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/accounts/ClientLogin");
        //curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        if ($info ['http_code'] == 200) {
            preg_match('/Auth=(.*)/', $output, $matches);
            if (isset($matches [1])) {
                $this->authToken = $matches [1];
            }
        }
        curl_close($ch);
        return $this->authToken;
    }

   public function fetchFeed($url) {

        if (empty($this->authToken)) {
            $this->getAuthToken();
            if (empty($this->authToken)) {
                return FALSE;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Authorization: GoogleLogin auth={$this->authToken}"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        if ($info ['http_code'] == 200) {
            return $output;
        } else {
            return FALSE;
        }
    }

   public function getProfiles() {
        $feedUrl = "https://www.google.com/analytics/feeds/accounts/default";
        if (($feedData = $this->fetchFeed($feedUrl)) === FALSE) {
            return array();
        }
        $doc = new DOMDocument ( );
        $doc->loadXML($feedData);
        $entries = $doc->getElementsByTagName('entry');
        $profiles = array();
        foreach ($entries as $entry) {
            $tableId = $entry->getElementsByTagName('tableId')->item(0)->nodeValue;
            $profiles [$tableId] = array();
            $profiles [$tableId] ["tableId"] = $tableId;
            $profiles [$tableId] ["title"] = $entry->getElementsByTagName('title')->item(0)->nodeValue;
            $profiles [$tableId] ["entryid"] = $entry->getElementsByTagName('id')->item(0)->nodeValue;
            $properties = $entry->getElementsByTagName('property');
            foreach ($properties as $property) {
                $profiles [$tableId] ['property'] [$property->getAttribute('name')] = $property->getAttribute('value');
            }
        }
        return $profiles;
    }

   public function getAnalyticRecords($startDate, $endDate, $dimensions, $metrics, $sort = '', $maxResults = '') {

        $url = 'https://www.google.com/analytics/feeds/data';
        $url .= "?ids=" . $this->profile;
        $url .= "&start-date=" . $startDate;
        $url .= "&end-date=" . $endDate;
        $url .= "&dimensions=" . $dimensions;
        $url .= "&metrics=" . $metrics;
        if (!empty($sort)) {
            $url .= "&sort=" . $sort;
        }
        if (!empty($maxResults)) {
            $url .= "&max-results=" . $maxResults;
        }
        if (($feedData = $this->fetchFeed($url)) === FALSE) {
            return array();
        }
        $doc = new DOMDocument ( );
        $doc->loadXML($feedData);
        $results = array();

        $aggregates = $doc->getElementsByTagName('aggregates');
        foreach ($aggregates as $aggregate) {
            $metrics = $aggregate->getElementsByTagName('metric');
            foreach ($metrics as $metric) {
                $results ['aggregates'] ['metric'] [$metric->getAttribute('name')] = $metric->getAttribute('value');
            }
        }

        $entries = $doc->getElementsByTagName('entry');
        foreach ($entries as $entry) {
            $record = array();
            $record ["title"] = $entry->getElementsByTagName('title')->item(0)->nodeValue;
            $dimensions = $entry->getElementsByTagName('dimension');
            foreach ($dimensions as $dimension) {
                $record ['dimension'] [$dimension->getAttribute('name')] = $dimension->getAttribute('value');
            }
            $metrics = $entry->getElementsByTagName('metric');
            foreach ($metrics as $metric) {
                $record ['metric'] [$metric->getAttribute('name')] = $metric->getAttribute('value');
            }
            $results ['entry'] [] = $record;
        }
        return $results;
    }

}