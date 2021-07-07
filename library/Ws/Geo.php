<?php

/**
 * Class Ws_Updater
 *
 * Atualiza em tempo real o CloudSearch
 *
 */

class Ws_Geo {


    /**
     * Retorna uma instância única de uma classe.
     *
     * @staticvar Singleton $instance A instância única dessa classe.
     *
     * @return Singleton A Instância única.
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Construtor do tipo protegido previne que uma nova instância da
     * Classe seja criada através do operador `new` de fora dessa classe.
     */
    protected function __construct()
    {
    }

    public function updater($id, $street, $city, $country)
    {

        $city = $this->get_city($city);
        $country = $this->get_country($country);
        $street = $this->street_clean($street);


        $result = Ws_Gmaps::getLatLon($street, $city, $country);

        if(isset($result['lat']) && isset($result['lon'])) {
            $model = new GeoEventosModel();
            $model->insertGeo($id, $result['lat'], $result['lon']);
            return $result;
        }
    }

    public function get_city($id_city)
    {
        $id_city = (int)$id_city;
        if(empty($id_city)) {
            return 'São Paulo';
        }

        $model = new CidadeModel();
        try {
        $result = $model->getByID($id_city);
        } catch(Exception $e) {}

        if(!empty($result)) {
            return $result->ds_cidade;
        }

    }

    public function get_country($country)
    {
        $country = (int)$country;
        if(empty($country)) {
            return 'Brasil';
        }

        $model = new PaisModel();
        try {
        $result = $model->getByID($country);
        } catch(Exception $e) {}

        if(!empty($result)) {
            return $result->ds_pais;
        }
    }

    public function street_clean($street)
    {
        if(strpos($street, ' -') !== false) {
            $street = substr($street, 0, strpos($street, ' -'));
        }
        return $street;
    }
}