<?php

class pets_model {

    public $db;
    private $species;
    private $petId;
    private $id;
    private $specie;
    private $sex;
    private $birthDate;
    private $dniProp;
    private $tlfProp;
    private $chip;
    private $name;
    private $weight;

    public function __construct() {
        $this->db = Conectar::conexion();
    }

    function getId() {
        return $this->id;
    }

    function getSpecie() {
        return $this->specie;
    }

    function getSex() {
        return $this->sex;
    }

    function getBirthDate() {
        return $this->birthDate;
    }

    function getDniProp() {
        return $this->dniProp;
    }

    function getTlfProp() {
        return $this->tlfProp;
    }

    function getChip() {
        return $this->chip;
    }

    function getName() {
        return $this->name;
    }

    function getWeight() {
        return $this->weight;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setSpecie($specie) {
        $this->specie = $specie;
    }

    function setSex($sex) {
        $this->sex = $sex;
    }

    function setBirthDate($birthDate) {
        $this->birthDate = $birthDate;
    }

    function setDniProp($dniProp) {
        $this->dniProp = $dniProp;
    }

    function setTlfProp($tlfProp) {
        $this->tlfProp = $tlfProp;
    }

    function setChip($chip) {
        $this->chip = $chip;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setWeight($weight) {
        $this->weight = $weight;
    }

    // Función que devuelve todas los tipos de animales
    public function get_species() {
        $query = "SELECT * FROM animales";

        $consulta = $this->db->query($query);
        while ($filas = $consulta->fetch_assoc()) {
            $this->species[] = $filas;
        }
        return $this->species;
    }

    // Función para añadir mascotas
    public function add_pet() {

        // si dni está vacio es porque no tiene dueño y se introducirá como null
        if (empty($this->dniProp)) {
            $query = "INSERT INTO mascota (especie, sexo, fechanac, chip, nombre, peso)
                    VALUES ({$this->specie},'{$this->sex}', '{$this->birthDate}', {$this->chip},
                    '{$this->name}',{$this->weight})";
        } else {
            $query = "INSERT INTO mascota (especie, sexo, fechanac, dni_propietario, chip, nombre, peso)
                    VALUES ({$this->specie},'{$this->sex}', '{$this->birthDate}', '{$this->dniProp}',{$this->chip},
                    '{$this->name}',{$this->weight})";
        }


        $result = $this->db->query($query);
        if ($this->db->error)
            return "true";
        else {
            return "false";
        }
    }

    // función para obtener el id de una mascota a partir del chip
    public function get_pet_id($chip) {
        $query = "SELECT id FROM mascota where chip = '{$chip}'";

        $consulta = $this->db->query($query);
        $filas = $consulta->fetch_assoc();
        $this->petId = $filas;

        return $this->petId;
    }

    // función para editar mascotas pasandole el chip de la mascota como parametro
    public function set_pet($chip) {
        $sql = "UPDATE mascota SET nombre= '{$this->name}', peso={$this->weight}, dni_propietario='{$this->dniProp}' WHERE chip = {$chip} ";

        $consulta = $this->db->query($sql);

        if ($this->db->error)
            return "$consulta<br>{$this->db->error}";
        else {
            return false;
        }
    }

    // función para que obtiene la información de la mascota a partir de dni
    public function get_info_pet_dni($dni) {

        $query = "select DISTINCT m.*, a.especie, TIMESTAMPDIFF(YEAR, m.fechanac, CURDATE()) AS age from mascota m, animales a where m.dni_propietario = '{$dni}' and a.id = m.especie";
        
        $consulta = $this->db->query($query);
        while ($filas = $consulta->fetch_assoc()) {
            $this->species[] = $filas;
        }
        return $this->species;
    }

    // función para obtener la información de la mascota a partir del chip
    // si tiene medicamentos crónicos ejecturará una consulta para obtener los medicamentos crónicos
    // si no ejecutará otra consulta para obtener la información básica de la mascota
    // pasandole el chip de la mascota como parametro
   
    public function get_info_pet_chip($chip) {

        $query = "select DISTINCT m.*, a.especie, r.medicamento, r.observacion, r.id as id_medicamento, r.cronico, med_nom.nombre as nombre_medicamento , TIMESTAMPDIFF(YEAR, m.fechanac, CURDATE()) AS age "
                . "from mascota m, recetas r, animales a, medicamento_nombre med_nom where a.id = m.especie and med_nom.id = r.medicamento and m.chip = {$chip} and r.cronico = 'y' and r.mascota = m.id";

        $consulta = $this->db->query($query);

        if ($consulta->num_rows == 0) {

            $query = "select DISTINCT m.*, a.especie, TIMESTAMPDIFF(YEAR, m.fechanac, CURDATE()) AS age from mascota m, animales a where a.id = m.especie and m.chip = {$chip}";
            $consulta = $this->db->query($query);

            while ($filas = $consulta->fetch_assoc()) {
                $this->species[] = $filas;
            }

            return $this->species;
        } else {

            while ($filas = $consulta->fetch_assoc()) {
                $this->species[] = $filas;
            }
            return $this->species;
        }
    }

    // función para eliminar recetas pasandole el id de la receta como parametro
    public function delete_prescription($idPrescription) {

        $sql = "DELETE from recetas WHERE id = {$idPrescription}";

        $result = $this->db->query($sql);
        if ($this->db->error)
            return "$sql<br>{$this->db->error}";
        else {
            return false;
        }
    }

    // función para eliminar todas las recetas asociadas a una mascota pasandole el id de la mascota como parametro
    public function delete_all_prescriptions_from_pet($idPet) {

        $sql = "DELETE from recetas where mascota = {$idPet}";


        $result = $this->db->query($sql);
        if ($this->db->error)
            return "$sql<br>{$this->db->error}";
        else {
            return false;
        }
    }

    // función para eliminar una mascota  
    public function delete_pet($idPet) {

        // antes de eliminar la mascota hay que eliminar todas las recetas asociadas a esa mascota
        $this->delete_all_prescriptions_from_pet($idPet);

        $sql = "DELETE from mascota where id = {$idPet}";

        $result = $this->db->query($sql);
        if ($this->db->error)
            return "$sql<br>{$this->db->error}";
        else {
            return false;
        }
    }

    // función para obtener información detallada a cerca de la mascota para las farmacias pasandole el chip como parametro
    public function get_more_info($chip) {

        $query = "select DISTINCT m.*, a.especie, r.medicamento, r.observacion, r.id as id_receta, r.cronico, r.recogido, r.cantidad, med_nom.nombre as nombre_medicamento ,
            TIMESTAMPDIFF(YEAR, m.fechanac, CURDATE()) AS age from mascota m, recetas r, animales a, 
            medicamento_nombre med_nom where a.id = m.especie and med_nom.id = r.medicamento and m.chip = {$chip}
 and r.mascota = m.id and r.recogido is null";

        $consulta = $this->db->query($query);

        while ($filas = $consulta->fetch_assoc()) {
            $this->species[] = $filas;
        }

        return $this->species;
    }

}
