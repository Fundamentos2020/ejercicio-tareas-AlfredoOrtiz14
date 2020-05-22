<?php
    //Llamada al modelo.
    require_once("../Models/DB.php");
    require_once("../Models/Tarea.php");
    require_once("../Models/Response.php");


    try{//Conexion al principio
        $connection = Conexion::conecta();
    }
    catch(PDOException $e){
        error_log("Error de conexión" . $e);

        $response = new Response();
        $response->setHttpStatusCode(500); 
        $response->setSuccess(false);
        $response->addMessage("Error en conexión a Base de dato");
        $response->send();
        exit();
    }

    
    if(array_key_exists("categoria_id", $_GET)){
        //GET host/tareas/categoria_id={id}
        //Devolver por categoria
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $categoria_id = $_GET['categoria_id'];//Obtener el id que se especifico
            if($categoria_id == '' || !is_numeric($categoria_id)){
                $response = new Response();
                $response->setHttpStatusCode(400);//Error por parte del cliente
                $response->setSuccess(false);
                $response->addMessage("El id de categoría no puede ser vacío, tiene que ser númerico");
                $response->send();
                exit();
            }

            try{
                $query = $connection->prepare('SELECT id, titulo, descripcion, DATE_FORMAT(fecha_limite, "%Y-%m-%d %H:%i") fecha_limite, completada, categoria_id FROM tareas WHERE categoria_id = :_categoria_id');
                $query->bindParam(":_categoria_id", $categoria_id, PDO::PARAM_INT);
                $query->execute();
            
                $rowCount = $query->rowCount(); 
                $tareas = array();
            
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $tarea = new Tarea($row['id'], $row['titulo'], $row['descripcion'], $row['fecha_limite'],  $row['completada'],  $row['categoria_id']);
                    $tareas[] = $tarea->getTareas();
                }
                $returnData = array();
                $returnData['total registros'] = $rowCount;
                $returnData['tareas'] = $tareas;

                $response = new Response();
                $response->setHttpStatusCode(200);//Cuando se ejecuto correctamente 
                $response->setSuccess(true);
                $response->setToCache(true);
                $response->setData($returnData);
                $response->send();
                exit();
                echo json_encode($tareas);
            }
            catch(TareaException $e){//Error en Tarea
                $response = new Response();
                $response->setHttpStatusCode(500); 
                $response->setSuccess(false);
                $response->addMessage($e->getMessage());
                $response->send();
                exit();
            }
            catch(PDOException $e){//Error en la consulta
                error_log("Error en BD" . $e);
    
                $response = new Response();
                $response->setHttpStatusCode(500); 
                $response->setSuccess(false);
                $response->addMessage("Error en consulta de tareas");
                $response->send();
                exit();
            }
        }
        else{
            $response = new Response();
            $response->setHttpStatusCode(405);//Metodo no permitido, solo GET. 
            $response->setSuccess(false);
            $response->addMessage("Método no permitido");
            $response->send();
            exit();
        }
    }
    else{//Devolver todas
        //GET host/tareas
        if(empty($_GET))//Ruta sin parametros
        {
            if($_SERVER['REQUEST_METHOD'] === 'GET'){
                try{
                    $query = $connection->prepare('SELECT id, titulo, descripcion, DATE_FORMAT(fecha_limite, "%Y-%m-%d %H:%i") fecha_limite, completada, categoria_id FROM tareas');
                    $query->execute();
        
                    $rowCount = $query->rowCount();    
                    $tareas = array();
            
                    while($row = $query->fetch(PDO::FETCH_ASSOC)){
                        $tarea = new Tarea($row['id'], $row['titulo'], $row['descripcion'], $row['fecha_limite'],  $row['completada'],  $row['categoria_id']);
                        $tareas[] = $tarea->getTareas();
                    }
        
                    $returnData = array();
                    $returnData['total registros'] = $rowCount;
                    $returnData['tareas'] = $tareas;
        
                    $response = new Response();
                    $response->setHttpStatusCode(200);//Cuando se ejecuto correctamente 
                    $response->setSuccess(true);
                    $response->setToCache(true);//Cache es solo para listados
                    $response->setData($returnData);
                    $response->send();
                    exit();
                    echo json_encode($tareas);
                }
                catch(TareaException $e){//Error en Tarea
                    $response = new Response();
                    $response->setHttpStatusCode(500); 
                    $response->setSuccess(false);
                    $response->addMessage($e->getMessage());
                    $response->send();
                    exit();
                }
                catch(PDOException $e){//Error en la consulta
                    error_log("Error en BD" . $e);
        
                    $response = new Response();
                    $response->setHttpStatusCode(500); 
                    $response->setSuccess(false);
                    $response->addMessage("Error en consulta de tareas");
                    $response->send();
                    exit();
                }
            }
            //else{
                elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
                    try{
                        if($_SERVER['CONTENT_TYPE'] !== "application/json"){//Si nos envian el cuerpo en un formato incorrecto
                            $response = new Response();
                            $response->setHttpStatusCode(400);//
                            $response->setSuccess(false);
                            $response->addMessage("Encabezado Content Type no es JSON");
                            $response->send();
                            exit();
                        }
                        $postData = file_get_contents("php://input");
                        
                        if(!$json_data = json_decode($postData)){//Si no se pudo convertir a JSON
                            $response = new Response();
                            $response->setHttpStatusCode(400);//
                            $response->setSuccess(false);
                            $response->addMessage("El cuerpo de la soilicitud no es un JSON valido");
                            $response->send();
                            exit();
                        }
        
                        if(!isset($json_data->titulo) || !isset($json_data->completada) || !isset($json_data->categoria_id)){//Atributos que no pueden ser null
                            $response = new Response();
                            $response->setHttpStatusCode(400);//
                            $response->setSuccess(false);
                            (!isset($json_data->titulo) ? $response->addMessage("Falta el titulo") : false);//if
                            (!isset($json_data->completada) ? $response->addMessage("Es obligatorio indicar si se completo") : false);
                            (!isset($json_data->categoria_id) ? $response->addMessage("Falta el id de la categoria") : false);
                            $response->send();
                            exit();
                        }

                        $tarea = new Tarea(
                            null, 
                            $json_data->titulo,
                            (isset($json_data->descripcion) ? $json_data->descripcion : null),
                            (isset($json_data->fecha_limite) ? $json_data->fecha_limite : null),
                            $json_data->completada,
                            $json_data->categoria_id
                        );
                        
                        $titulo = $tarea->getTitulo();
                        $descripcion = $tarea->getDescripcion();
                        $fecha_limite = $tarea->getFechaLimite();
                        $completada = $tarea->getCompletada();
                        $categoria_id = $tarea->getCategoriaId();

                        $query = $connection->prepare('INSERT INTO tareas (titulo, descripcion, fecha_limite, completada, categoria_id) 
                                                    VALUES (:titulo, :descripcion, STR_TO_DATE(:fecha_limite, \'%Y-%m-%d %H:%i\'), :completada, :categoria_id)');

                        $query->bindParam(':titulo', $titulo, PDO::PARAM_STR);
                        $query->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                        $query->bindParam(':fecha_limite', $fecha_limite, PDO::PARAM_STR);
                        $query->bindParam(':completada', $completada, PDO::PARAM_STR);
                        $query->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
                        $query->execute();

                        $rowCount = $query->rowCount();
                        
                        if($rowCount === 0){
                            $response = new Response();
                            $response->setHttpStatusCode(500);//Error interno
                            $response->setSuccess(false);
                            $response->addMessage("Error al crear la tarea");
                            $response->send();
                            exit();
                        }
                        
                        $ultimo_ID = $connection->lastInsertId();
                        $query = $connection->prepare('SELECT id, titulo, descripcion, DATE_FORMAT(fecha_limite, "%Y-%m-%d %H:%i") fecha_limite, completada, categoria_id FROM tareas WHERE id = :id');
                        $query->bindParam(':id', $ultimo_ID, PDO::PARAM_INT);
                        $query->execute();

                        $rowCount = $query->rowCount();
                        
                        if($rowCount === 0){
                            $response = new Response();
                            $response->setHttpStatusCode(500);//Error interno
                            $response->setSuccess(false);
                            $response->addMessage("Error al obtener tarea despues de crearla");
                            $response->send();
                            exit();
                        }

                        $tareas = array();

                        while($row = $query->fetch(PDO::FETCH_ASSOC)){//Arreglo asociativo
                            $tarea = new Tarea($row['id'], $row['titulo'], $row['descripcion'], $row['fecha_limite'],  $row['completada'],  $row['categoria_id']);
                            $tareas[] = $tarea->getTareas();
                        }

                        $returnData = array();
                        $returnData['total registros'] = $rowCount;
                        $returnData['tareas'] = $tareas;

                        $response = new Response();
                        $response->setHttpStatusCode(201);//Cuando se ejecuto correctamente 
                        $response->setSuccess(true);
                        $response->addMessage("Tarea creada!");
                        $response->setData($returnData);
                        $response->send();
                        exit();
                        echo json_encode($tareas);

                    }
                    catch(TareaException $e){
                        $response = new Response();
                        $response->setHttpStatusCode(500); 
                        $response->setSuccess(false);
                        $response->addMessage($e->getMessage());
                        $response->send();
                        exit();
                    }
                    catch(PDOException $e){
                        error_log("Error en BD" . $e);
        
                        $response = new Response();
                        $response->setHttpStatusCode(500); 
                        $response->setSuccess(false);
                        $response->addMessage("Error en consulta de tareas");
                        $response->send();
                        exit();
                    }
                /*$response = new Response();
                $response->setHttpStatusCode(405);//Metodo no permitido, solo GET. 
                $response->setSuccess(false);
                $response->addMessage("Método no permitido");
                $response->send();
                exit();*/
            }
        }
    }  
?>