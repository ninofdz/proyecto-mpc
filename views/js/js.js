jQuery(document).ready(function () {

        $(".recetar").click(function(){
          if ($(this).hasClass("activated")) {
              $(this).removeClass("activated");
              $(this).html("recetar");
          } else {
            $(this).addClass("activated");
            $(this).html("cerrar");
          }
        });


        $(".filterProduct").change(function () {
      // si la clase filterProduct tiene la clase "activated" se la quita, sino, se la pone
        if ($(this).hasClass("activated")) {
            $(this).removeClass("activated");
        } else {
          $(this).addClass("activated");
        }

        $category = [];

        // los que tienen la clase "activated" los pondrá en un array
        $(".activated").each(function () {

            $category.push($(this).attr("id"));
        });

        // función para filtrar y recoger los valores de los precios
        $(".card").each(function () {

              // si hay algo en el array de marcas y el id del card no está en el array lo esconde, sino, lo muestra
              if ($category.length && jQuery.inArray($(this).find(".categoria").attr("id"), $category) === -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });

    });


    function close_accordion_section() {
        jQuery('.accordion .accordion-section-title').removeClass('active');
        jQuery('.accordion .accordion-section-content').slideUp(300).removeClass('open');
    }

    jQuery('.accordion-section-title').click(function (e) {
        // Grab current anchor value
        var currentAttrValue = jQuery(this).attr('href');

        if (jQuery(e.target).is('.active')) {
            close_accordion_section();
        } else {
            close_accordion_section();

            // Add active class to section title
            jQuery(this).addClass('active');
            // Open up the hidden content panel
            jQuery('.accordion ' + currentAttrValue).slideDown(300).addClass('open');
        }

        e.preventDefault();
    });



    $('#efectSearch').keyup(function ()
    {
        var word = $('#efectSearch').val();

        $.ajax(
                {
                    type: "POST",
                    url: "controllers/searchEfect_controller.php",
                    data: ({word: word.toString()}),
                    success: function (response)
                    {
                        medicines = $.parseJSON(response);

                        $('.card-medicines').remove();

                        var html = process_data(medicines);

                        $('.medicine-container').append(html);
                    }
                });

        return false;
    });

    // guardo en la variable currencies el array de productos de autocomplete_controller.php haciendo
    // una llamada vía AJAX

    var currencies = function () {
        var products = null;
        $.ajax({
            'async': false,
            'type': "GET",
            'global': false,
            'url': "controllers/autocomplete_controller.php",
            'success': function (response) {
                products = $.parseJSON(response);
            }
        });
        return products;
    }();

    // utiliza una libreria para autocompletar las palabras que vayan pasando por el buscador
    $('#autocomplete').autocomplete({
        lookup: currencies,
    });

});

function process_data(medicines) {

    var html = "";
medicines.forEach(function(medicine){
        html += "<div class='card mb-5 col-3 ml-5 card-medicines'>"
                + "<a data-toggle='modal' href='#modall' style='width:70%'><img class='card-img-top' src='" + medicine.url + "'></a>"
                + "<div class='card-block'>"
                + "<h4 class='card-title'>"
                + medicine.nombre
                +"</h4>"
                + "<div class='meta'>"
                + "<a data-toggle='modal' href='#modal'>"
                + medicine.categoria
                + "</a>"
                + "</div>"
                + "<div class='card-text'>"
                 + medicine.efecto
                + "</div>"

                + "<div class='collapse mt-2' id='collapseExample" + medicine.id + "'>"
                + "<div class='card card-body'>"
                + "<div class='meta'>"
                + "<span class='mt-2'>Efecto Secundario</span>"
                + "</div>"
                +   medicine.efecto_secundario
                + "</div>"
                + "<div class='row mt-4'>"
                + "<div class='col-6'>"
                + "<div class='form-group'>"
                + "<input type='number' class='form-control' id='exampleInputPassword1' placeholder='Cantidad'>"
                + "</div>"
                + "</div>"
                + "<div class='col-6'>"
                + "<div class='form-check mt-2'>"
                + "<input type='checkbox' class='form-check-input' id='exampleCheck1'>"
                + "<label class='form-check-label' for='exampleCheck1'>Medicamento crónico</label>"
                + "</div>"
                + "</div>"
                + "</div>"

                + "<div class='form-group'>"
                + "<label for='comment'>Observación</label>"
                + "<textarea class='form-control' rows='5' id='comment'></textarea>"
                + "</div>"

                + "<div class='form-group'>"
                + "<label for='usr'>Número de chip:</label>"
                + "<input type='text' class='form-control' id='usr'>"
                + "</div>"
                + "<button name='id' value='' class='btn btn-outline-danger float-right btn-sm' data-toggle='collapse' data-target='#collapseExample'>Confirmar receta</button>"

                + "</div>"

                + "</div>"
                + "<div class='card-footer mb-2'>"
                + "<button name='id' value='' class='btn btn-outline-success float-right btn-sm' data-toggle='collapse' data-target='#collapseExample" + medicine.id + "'>Recetar</button>"
                + "</div>"
                + "</div>"

    });

    return html;
}