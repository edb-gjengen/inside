/*
 * menuExpandable3.js - implements an expandable menu based on a HTML list
 * Author: Dave Lindquist (http://www.gazingus.org)
 */

if (!document.getElementById){
    document.getElementById = function() { 
        return null; 
    }
}

function initializeMenu(menuId, actuatorId) {
    var menu = document.getElementById(menuId);
    var actuator = document.getElementById(actuatorId);

    if (menu == null || actuator == null){
        return;
    }

    actuator.parentNode.style.backgroundImage = "url(../inside/graphics/plus.png)";
    actuator.onclick = function() {
        var display = menu.style.display;
        this.parentNode.style.backgroundImage =
            (display == "block") ? "url(../inside/graphics/plus.png)" : "url(../inside/graphics/minus.png)";
        menu.style.display = (display == "block") ? "none" : "block";

        return false;
    }
}

function menuExpandCurrentSection(menuId){
    var menu = document.getElementById(menuId);
    if (menu == null){
        return;
    }
    menu.parentNode.style.backgroundImage = "url(../inside/graphics/minus.png)";
    menu.style.display = "block";
}

