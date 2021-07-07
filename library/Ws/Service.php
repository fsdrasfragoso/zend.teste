<?php

/**
 * Class Ws_Updater
 *
 * Atualiza em tempo real o CloudSearch
 *
 */

class Ws_Service {

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

    public function batchGeo($idsByComma)
    {
        $result = array();
        if(!empty($idsByComma)) {
            $idsByComma = str_replace(' ', '', $idsByComma);
            $data = explode(',', $idsByComma);

            if(sizeof($data) > 0) {
                foreach($data as $id) {
                    $model = new EventoModel();
                    $evento = $model->getByID($id);

                    $result[] = Ws_Geo::getInstance()->updater($id, $evento->ds_endereco, $evento->id_cidade, $evento->id_pais);
                }
            }
        }

        return $result;
    }
}