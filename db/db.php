<?php

/**
 * Classe amb mètode estàtic que serveix per connectar a Base de Dades
 */
class Conectar{
    public static function conexion(){
        $conexion=new mysqli("localhost", "user", "user", "mypetcard");
        $conexion->query("SET NAMES 'utf8'");
        return $conexion;
    }
}
?>
