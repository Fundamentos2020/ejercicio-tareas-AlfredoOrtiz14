<?php

    class TareaException extends Exception{}

    class Tarea{        
        public $_id;
        public $_titulo;
        public $_descripcion;
        public $_fecha_limite;
        public $_completada;
        public $_categoria_id;

        public function __construct($id, $titulo,$descripcion, $fecha_limite, $completada, $categoria_id){             
            $this->setId($id);
            $this->setTitulo($titulo);
            $this->setDescripcion($descripcion);
            $this->setFechaLimite($fecha_limite);
            $this->setCompletada($completada);
            $this->setCategoriaId($categoria_id);
        }

        public function getId(){
            return $this->_id;
        }

        public function setId($id){
            if($id !== null && (!is_numeric($id) || $id <=0 || $id >= 2147483647) || $this->_id !== null){
                throw new TareaException("Error en ID de tarea");
            }
                $this->_id = $id;
        }

        public function getTitulo(){
            return $this->_titulo;
        }

        public function setTitulo($titulo){
            if($titulo === null || strlen($titulo) > 50){
                throw new TareaException("Error en texto de titulo de tarea");
            }
            $this->_titulo = $titulo;
        }

        public function getDescripcion(){
            return $this->_descripcion;
        }

        public function setDescripcion($descripcion){
            if($descripcion !== null && strlen($descripcion) > 150){
                throw new TareaException("Error en texto de descripcion de tarea");
            }
            $this->_descripcion = $descripcion;
        }

        public function getFechaLimite(){
            return $this->_fecha_limite;
        }

        public function setFechaLimite($fecha_limite){
            if($fecha_limite !== null && (date_format(date_create_from_format('Y-m-d H:i',$fecha_limite), 'Y-m-d H:i') !== $fecha_limite)){
                throw new TareaException("Error en fecha limite de tarea");
            }
            $this->_fecha_limite = $fecha_limite;
        }

        public function getCompletada(){
            return $this->_completada;
        }

        public function setCompletada($completada){
            if(strtoupper($completada) !== 'SI' && strtoupper($completada) !== 'NO'){
                throw new TareaException("Error en texto de completado de tarea");
            }
            $this->_completada = $completada;
        }

        public function getCategoriaId(){
            return $this->_categoria_id;
        }

        public function setCategoriaId($categoria_id){
            if(!is_numeric($categoria_id) || $categoria_id <=0 || $categoria_id >= 2147483647){
                throw new TareaException("Error en Categoria de ID de tarea");
            }
            $this->_categoria_id = $categoria_id;
        }

        public function getTareas(){
            $tareas = array();
            $tareas['id'] = $this->getId();
            $tareas['titulo'] = $this->getTitulo();
            $tareas['descripcion'] = $this->getDescripcion();
            $tareas['fecha_limite'] = $this->getFechaLimite();
            $tareas['completada'] = $this->getCompletada();
            $tareas['categoria_id'] = $this->getCategoriaId();
            return $tareas;
        }
    }
?>