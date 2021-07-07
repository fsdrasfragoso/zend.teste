<?php

/**
 * Class Ws_Updater
 *
 * Atualiza em tempo real o CloudSearch
 *
 */

class Ws_Updater {

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


    /*
     *
     * Função envia um ponteiro de execução para a api do cloudsearch
     *
     * Ws_Updater::getInstance()->updater($id_evento, 'delete', 'events');
     * Ws_Updater::getInstance()->updater($id_evento, 'update', 'events');
     *
     * Ws_Updater::getInstance()->updater($id_evento, 'update', 'photos');
     * Ws_Updater::getInstance()->updater($id_evento, 'delete', 'photos');
     */

    public function updater($id=null, $exec="update", $source="events")
    {
        $id = (int)$id;

        if(!empty($id)) {
            $curl = new Ws_Zebra();

            $class='Ws_Url'.ucfirst($source);

            if($exec=="update") {
               $this->makeUpdate($id, $class, $curl);
            }

            if($exec=="delete") {
                $this->makeDelete($id, $class, $curl);
            }

        }
    }


    public function makeUpdate($id, $class, Ws_Zebra $curl)
    {

        $url =  call_user_func($class.'::makeUrl', $id);
        $params =  call_user_func($class.'::makeParams', $id);


        //if(Zend_Registry::get('config')->ambiente != 'develop') {
            $curl->get(array($url), array($this, 'callbackUpdater'));
        //}

    }

    public function makeDelete($id, $class, Ws_Zebra $curl)
    {
        $url =  call_user_func($class.'::makeUrl', $id);
        $params =  call_user_func($class.'::makeParams', $id);


        //if(Zend_Registry::get('config')->ambiente != 'develop') {
            $curl->delete(array($url=>$params), array($this, 'callbackUpdater'));
        //}
    }

    public function callbackUpdater($e)
    {
    }


}