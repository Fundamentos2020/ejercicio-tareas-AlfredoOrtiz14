<?php
    //Llamada al modelo.
    require_once("../Models/DB.php");
    require_once("../Models/Categoria.php");
    require_once("../Models/Response.php");
    try{
        $connection = Conexion::conecta();
    }
    catch(PDOException $e){
        error_get_last("Error de conexión" . $e);

        $response = new Response();
        $response->setHttpStatusCode(500); 
        $response->setSuccess(false);
        $response->addMessage("Error en conexión a Base de datoa");
        $response->send();
        exit();
    }

    //GET host/categorias
    if(empty($_GET))//Ruta sin parametros
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $query = $connection->prepare("SELECT * FROM categorias");
            $query->execute();
    
            $categorias = array();
    
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $categoria = new Categoria($row['id'], $row['nombre']);
                $categorias[] = $categoria->getCategorias();
            }
    
            //echo json_encode($categorias);
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($categorias);
            $response->send();
            exit();
        }
        else{
            $response = new Response();
            $response->setHttpStatusCode(405);//Metodo no permitido, solo GET. 
            $response->setSuccess(false);
            $response->addMessage("Método no permitido");
            $response->send();
        }
    }
    else{
        $response = new Response();
        $response->setHttpStatusCode(404);//Cuando no existe la ruta. 
        $response->setSuccess(false);
        $response->addMessage("No existe la ruta o no se encontro");
        $response->send();
    }

?>