<?php

require_once("controllers/medicines_controller.php");
require_once 'controllers/categories_controller.php';

class home_controller {

    // Función que muestra la página principal
    function view($userCat = "", $medName = "", $presError = "", $loginFailed = "", $errorSearch = "", $showDeletePet = "") {

        if ($loginFailed === false) {
          $errorLogin = "yes";
          require_once("views/home_view.phtml");
        }

        if (!empty($_SESSION['user'])) {

            $med = new medicines_controller();
            $cat = new categories_controller();

            if (empty($medName)) {
                $data['medicines'] = $med->get_medicine();
            } else {
                $data['medicines'] = $med->get_medicine($medName);
            }

            $data['categories'] = $cat->all_categories();
            $data['species'] = $med->all_species();

            $data['administration'] = $med->all_way_administration();



            if (!empty($_SESSION['user']) && $_SESSION['userType'] == "veterinario") {

                require_once("views/vet_view.phtml");
            }

            if (!empty($_SESSION['user']) && $_SESSION['userType'] == "proveedor") {
                $med->medicine_graph();

            }

            if (!empty($_SESSION['user']) && $_SESSION['userType'] == "distribuidor") {
                require_once("views/farm_view.phtml");
            }
        } else {
            require_once("views/home_view.phtml");
        }
    }

    function error_search_view(){
      $error_searchc = true;
      require_once("views/vet_view.phtml");
    }


}

?>
