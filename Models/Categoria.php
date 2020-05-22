<?php
    class Categoria{

        private $_id;
        private $_nombre;

        public function __construct($id, $nombre){
            $this->setId($id);
            $this->setNombre($nombre);
        }

        public function getId(){
            return $this->_id;
        }

        public function setId($id){
            $this->_id = $id;
        }

        public function getNombre(){
            return $this->_nombre;
        }

        public function setNombre($nombre){
            $this->_nombre = $nombre;
        }

        public function getCategorias(){
            $categoria = array();
            $categoria['id'] = $this->getId();
            $categoria['nombre'] = $this->getNombre();
            return $categoria;
        }
    }
?>