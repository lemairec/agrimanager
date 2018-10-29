/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

const $ = require('jquery');
// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
require('bootstrap');
require('bootstrap-datepicker');
require('bootstrap-datepicker/js/locales/bootstrap-datepicker.fr.js');

require( 'datatables.net' )

//<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>


// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// var $ = require('jquery');

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

$('document').ready(function() {
    $('.js-datepicker').datepicker({
        dateFormat: 'dd/mm/yy',
        language: 'fr'
    });

    $('.js-datatable').DataTable({
        "lengthMenu": [[15, 100, -1], [15, 100, "All"]]
    });
});

console.log($('.js-datepicker'));


global.$ = global.jQuery = $;
