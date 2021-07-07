<?php

class UsuarioController extends Zend_Controller_Action
{

    public function init()
    {
        header('Access-Control-Allow-Origin: *');
    }

    public function indexAction()
    {
        $this->_helper->layout->disableLayout(); 
        $usuario = new UsuarioModel();
        $logUsuarioModel = new LogUsuarioModel();
        $this->view->usuarios = $usuario->getAll(); 
        $this->view->log = $logUsuarioModel->getAll(); 

    }

    public function createAction()
    {
        $this->_helper->layout->disableLayout();     
    }

    public function storeAction()
    {
        
        $usuario = new UsuarioModel();
        $params = $this->_request->getParams(); 
        
        $caminho = $this->upload($_FILES["imagem"]);
       
       
        $array = $this->arrayUsuario($params,$caminho); 
        if($usuario->save($array))
        {
            http_response_code('200');
            echo json_encode(array("message" => "Usuario Salvo Com Sucesso!", "url"=>"/usuario"));
            die();
        }
        http_response_code('204');
        echo json_encode(array("message" => "Verifique os dados do usuarios", "url"=>"/usuario"));
        die(); 
    }

    public function editAction()
    {
        $this->_helper->layout->disableLayout();    
        
        $usuarioModel = new UsuarioModel();
        $params = $this->_request->getParams(); 
        
        $this->view->usuario = $usuarioModel->getById($params['id']);        
    }

    public function showAction()
    {
        
        $usuarioModel = new UsuarioModel();
        $logUsuarioModel = new LogUsuarioModel();
        $params = $this->_request->getParams(); 
        //print_r($usuarioModel->getById($params['cod'])['id']);exit;
        $this->view->usuario = $usuarioModel->getById($params['cod']);
        $this->view->log = $logUsuarioModel->getById($params['cod']);
    }

    public function updateAction()
    {
       
        
        $usuario = new UsuarioModel();
        $params = $this->_request->getParams(); 
        $caminho = $this->upload($_FILES["imagem"]);
        $array = $this->arrayUsuario($params,$caminho); 
        if($usuario->edit($array,$params['id']))
        {
            http_response_code('200');
            echo json_encode(array("message" => "Usuario Alterado Com Sucesso!", "url"=>"/usuario"));
            die();
        }
        http_response_code('204');
        echo json_encode(array("message" => "Verifique os dados do usuarios", "url"=>"/usuario"));
        die();        
    }



    public function deleteAction()
    {
       
       $usuario = new UsuarioModel();
       $params = $this->_request->getParams(); 
       if($usuario->remove($params['id']))
       {
            http_response_code('200');
            echo json_encode(array("message" => "Usuario Excluido Com Sucesso!", "url"=>"/usuario"));
            die();
       }
       http_response_code('204');
       echo json_encode(array("message" => "Problemas para excluir o Usuario", "url"=>"/usuario"));
       die(); 
    }

    public function arrayUsuario($params,$caminho)
    {
        $array = 
        [
            
            "nome" => $params['nome'],
            "sobrenome" => $params['sobrenome'],
            "email" => $params['email'],
            "imagem" => $caminho,
            "rua" => $params['rua'],
            "bairro" => $params['bairro'],
            "numero" => $params['numero'],
            "complemento" => $params['complemento'],
            "cidade" => $params['cidade'],
            "estado" => $params['estado'],
            "observacoes" => $params['observacoes'],
            "status" => $params['status']                
        ];

        return $array;
    }


    public function upload($foto)
    {
        
        if (!empty($foto["name"])) 
        {
             
            // Largura máxima em pixels
            $largura = 150;
            // Altura máxima em pixels
            $altura = 180;
            // Tamanho máximo do arquivo em bytes
            $tamanho = 1000;
            $error = array();
            // Verifica se o arquivo é uma imagem
            if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $foto["type"])){
                $error[1] = "Isso não é uma imagem.";
                } 
        
            // Pega as dimensões da imagem
            $dimensoes = getimagesize($foto["tmp_name"]);
        
            // Verifica se a largura da imagem é maior que a largura permitida
            if($dimensoes[0] > $largura) {
                $error[2] = "A largura da imagem não deve ultrapassar ".$largura." pixels";
            }
            // Verifica se a altura da imagem é maior que a altura permitida
            if($dimensoes[1] > $altura) {
                $error[3] = "Altura da imagem não deve ultrapassar ".$altura." pixels";
            }
            
            // Verifica se o tamanho da imagem é maior que o tamanho permitido
            if($foto["size"] > $tamanho) {
                    $error[4] = "A imagem deve ter no máximo ".$tamanho." bytes";
            }
            // Se não houver nenhum erro
            if (0 == 0) {
                echo 'entrou'; 
                // Pega extensão da imagem
                preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $foto["name"], $ext);
                // Gera um nome único para a imagem
                $nome_imagem = md5(uniqid(time())) . "." . $ext[1];
                // Caminho de onde ficará a imagem
                $caminho_imagem =  'img/'.$nome_imagem;
                // Faz o upload da imagem para seu respectivo caminho
                move_uploaded_file($foto["tmp_name"], $caminho_imagem); 

                return $caminho_imagem;
            }
        }

        return ''; 
       
    }

}