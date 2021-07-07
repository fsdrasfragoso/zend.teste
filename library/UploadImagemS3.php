<?php
require "upload-s3/autoload.php";

use Aws\S3\S3Client;

class UploadImagemS3 extends Zend_Controller_Action{
	
	
	const ACCESS_KEY = 'AKIAJ6MJF5756BVMEN5Q';
	const SECRET_KEY = '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';
	
	public static function uploadS3($file, $caminho, $tipo, $id_evento, $id_categoria=0, $indice=0)
	{
		$s3Path = Zend_Registry::get('config')->s3;
		
		if(is_array($file['name'])){ 
			$novo=$file['name'][$indice];		
			$source=$file['tmp_name'][$indice];		

		}else{
			$novo=$file['name'];		
			$source=$file['tmp_name'];
		}
		
		$arquivo=explode(".", $novo);
		
		#Se for save de combos salva diferente o nome da imagem
		if(strstr($caminho, 'combo'))
			$ds_imagem=$id_evento.'.'.strtolower($arquivo[1]);
		else
			$ds_imagem=$tipo.'.'.strtolower($arquivo[1]);
		
		if($id_categoria==0){
			if(strstr($caminho, 'combo'))
				$camimhofull=$caminho;
			else
				$camimhofull=$caminho.'/'.$id_evento;
		}else {
			if(strstr($caminho, 'combo'))
				$camimhofull=$caminho;
			else
				$camimhofull=$caminho.'/'.$id_categoria;
		}
		
		try {       
	 
			// dispara exce��o caso n�o tenha dados enviados
			if (!isset($file)) {
				throw new Exception("File not uploaded", 1);
			}

			// cria o objeto do cliente, necessita passar as credenciais da AWS
			$clientS3 = S3Client::factory(array(
				'key'    => self::ACCESS_KEY,
				'secret' => self::SECRET_KEY
			));

			// m�todo putObject envia os dados pro bucket selecionado (no caso, teste-marcelo
			$response = $clientS3->putObject(array(
                'Bucket' => "$s3Path->bucket/$camimhofull",
                'Key'    => $ds_imagem,
				'SourceFile' => $source,
				'ContentType' => 'image'
			));

			if( $tipo === "skip_bd" )
				return false;
	 
			if(strstr($caminho, 'combo'))
				echo self::salvaImagemCombo($id_evento, $id_categoria, $tipo, $camimhofull, $ds_imagem);
			else
				echo self::salvaImagem2($id_evento, $id_categoria, $tipo, $camimhofull, $ds_imagem);
	 
		} catch(Exception $e) {
			echo "Erro > {$e->getMessage()}";
			exit;
		}
	}
	
	public static function saveOnlyS3( $file, $path, $file_name )
	{
		$s3Path = Zend_Registry::get('config')->s3;

		// cria o objeto do cliente, necessita passar as credenciais da AWS
		$clientS3 = S3Client::factory(array(
			'key'    => self::ACCESS_KEY,
			'secret' => self::SECRET_KEY
		));

		try{

			$extension  = explode( ".", $file_name );
			$getType 	= end( $extension );

			switch ($getType){
				case "pdf":
					$content_type = "application/pdf";
					break;
				case "doc":
					$content_type = "application/msword";
					break;
				case "docx":
					$content_type = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
					break;
				case "jpg":
					$content_type = "image/jpeg";
					break;
				case "png":
					$content_type = "image/png";
					break;
				case "txt":
					$content_type = "text/plain";
					break;
				case "RET":
					$content_type = "application/octet-stream";
					break;
			}

			$response = $clientS3->putObject(array(
				'Bucket' => "media.ativo.com/$path",    
				'Key'    => $file_name,    
				'SourceFile' => $file,    
				'ContentType' => $content_type
			));

		}catch(Exception $e ){
			var_dump($file);
			var_dump($path);
			//var_dump($s3Path->bucket);
			var_dump($file_name);
			var_dump($e->getMessage());
			exit;
		}
	}		

	public function readDirS3($path, $file_name)
	{
		$s3Path = Zend_Registry::get('config')->s3;
		
		// cria o objeto do cliente, necessita passar as credenciais da AWS
		$clientS3 = S3Client::factory(array(
			'key'    => self::ACCESS_KEY,
			'secret' => self::SECRET_KEY
		));
		
		$bucket="s3://$s3Path->bucket/";
		
		try{								
				$clientS3->registerStreamWrapper();
			  return $stream = fopen($bucket.$path.'/'.$file_name, 'r');

		}catch(Exception $e ){
			Zend_debug::dump( $e->getMessage() );
			exit;
		}
	}
	
	public function verificaSeExiste($path, $file_name)
	{
		$s3Path = Zend_Registry::get('config')->s3;
		
		// cria o objeto do cliente, necessita passar as credenciais da AWS
		$clientS3 = S3Client::factory(array(
			'key'    => self::ACCESS_KEY,
			'secret' => self::SECRET_KEY
		));
		
		$bucket="s3://$s3Path->bucket/";
		
		try{								
				$clientS3->registerStreamWrapper();
				if (file_exists($bucket.$path.'/'.$file_name) and is_file($bucket.$path.'/'.$file_name)) {
					return true;
				}else{
					return false;
				}				

		}catch(Exception $e ){
			Zend_debug::dump( $e->getMessage() );
			exit;
		}
	}
	
	public function assinaturasCreateCsvFileS3($path, $file_name, $dados)
	{
		$s3Path = Zend_Registry::get('config')->s3;
		
		// cria o objeto do cliente, necessita passar as credenciais da AWS
		$clientS3 = S3Client::factory(array(
			'key'    => self::ACCESS_KEY,
			'secret' => self::SECRET_KEY
		));
		
		$bucket="s3://$s3Path->bucket/";
		try{								
				$clientS3->registerStreamWrapper();
				
				
				$stream = fopen($bucket.$path.$file_name, 'w');

				foreach ($dados as $key => $row)
				{
					$row = array_map("utf8_decode", $row);
					array_shift($row);
					
					if($key==0){
						fputcsv($stream, array_keys($row), ';'); 
					}
					fputcsv($stream, $row, ';'); 
				}
				fclose($stream);

		}catch(Exception $e ){
			Zend_debug::dump( $e->getMessage() );
			exit;
		}
	}

	public static function salvaImagem2($id_evento, $id_categoria, $ds_tipo_imagem, $ds_url_imagem, $ds_imagem){
		$imagem= new EventoImagemS3Model();
		$sql="INSERT INTO sa_evento_imagem_s3 (id_evento, id_categoria, ds_tipo_imagem, ds_url_imagem, ds_imagem) 
				VALUES (".$id_evento.", ".$id_categoria.", '".$ds_tipo_imagem."', '".$ds_url_imagem."', '".$ds_imagem."')
				ON DUPLICATE KEY UPDATE id_evento=$id_evento, id_categoria=$id_categoria, ds_tipo_imagem='$ds_tipo_imagem', ds_url_imagem='$ds_url_imagem', ds_imagem='$ds_imagem';";
		$imagem->getAdapter()->query($sql)->rowCount();
	}
	
	public function salvaImagemCombo($id_evento, $id_categoria, $ds_tipo_imagem, $ds_url_imagem, $ds_imagem){
		$imagem= new EventoImagemS3Model();
		$sql="INSERT INTO sa_evento_imagem_s3 (id_combo, id_categoria, ds_tipo_imagem, ds_url_imagem, ds_imagem) 
				VALUES (".$id_evento.", ".$id_categoria.", '".$ds_tipo_imagem."', '".$ds_url_imagem."', '".$ds_imagem."')
				ON DUPLICATE KEY UPDATE id_combo=$id_evento, id_categoria=$id_categoria, ds_tipo_imagem='$ds_tipo_imagem', ds_url_imagem='$ds_url_imagem', ds_imagem='$ds_imagem';";
		$imagem->getAdapter()->query($sql)->rowCount();
	}
}
	
?>