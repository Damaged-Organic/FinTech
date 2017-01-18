"use strict";

import $ from "jquery";
import selectify from "../lib/select";
import Menu from "../controllers/menu";
import Status from "../controllers/statusPanel";

window.$ = $;

$(function(){

    new Menu();
    new Status();

    $(".select-holder").selectify();
});
