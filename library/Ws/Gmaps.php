<?php

class Ws_Gmaps {
    const GMAPS_URL = "https://maps.google.com/maps/api/geocode/";
    const GMAPS_KEYS = "AIzaSyDgq5qcekCQmQZtCjw0E4ZcvWffgEWa4lY";

    static function getLatLon($endereco, $city, $country)
    {

        $query = self::makeQuery($endereco, $city, $country);
        $query = urlencode($query);
        $geocode = file_get_contents(self::GMAPS_URL . 'json?address='.$query.'&sensor=false&key_=' . self::GMAPS_KEYS);

        $output= json_decode($geocode);

        if($output->status == "OVER_QUERY_LIMIT"){
            return self::over_limit($output);
        }

        try {
            $result = array(
                'lat' => $output->results[0]->geometry->location->lat,
                'lon' => $output->results[0]->geometry->location->lng
            );

        }catch (\Exception $e) {
            return $e->getMessage();
        }

        if(!empty($result)) {
            return $result;
        }

        return false;

    }

    static function makeQuery($endereco, $city, $country)
    {

        $list = compact('endereco', 'city', 'country');
        $list = array_filter($list);
        return implode(', ', $list);
    }

    static function over_limit($output)
    {
        return $output->error_message;
    }
}
