/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
Window.prototype.$ = $;

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');
require('animate.css');
// the fort awesome module
require('@fortawesome/fontawesome-free/js/all.js');

// or you can include specific pieces
require('bootstrap/js/dist/tooltip');
require('bootstrap/js/dist/popover');

import bsCustomFileInput from "bs-custom-file-input";

$(document).ready(function () {
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });
    //This code helps file input to be completed
    bsCustomFileInput.init();
});