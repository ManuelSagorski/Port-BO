/**
 *	router.js
 *
 *	Händelt das Routing für die Seite
 */


/* Klassen für die einzelnen Seiten werden geladen */
require(['classes/Vessel'], function() {
	Vessel = require('classes/Vessel');
	vessel = new Vessel();
	
	/* Laden der Standard-Seite */
	router.navigateTo('vessel');
});

require(['classes/Agency'], function() {
	Agency = require('classes/Agency');
	agency = new Agency();
});

require(['classes/Port'], function() {
	Port = require('classes/Port');
	port = new Port();
});

require(['classes/Settings'], function() {
	Settings = require('classes/Settings');
	settings = new Settings();
});

/* Router wird initialisiert */
var router = new Router({
    mode: 'history',
	root: '/boNew/public/',
    page404: function (path) {
        console.log('"/' + path + '" Page not found');
    }
});

/* Defintion der einzelnen Routen */
router.add('vessel', function () {
	vessel.open();
});

router.add('agency', function () {
	agency.open();
});

router.add('port', function () {
    port.open();
});

router.add('settings', function () {
    settings.open();
});

router.add('logout', function () {
    window.location.href = "../index.php?logout";
});


router.addUriListener();
$('a').on('click', (event) => {
	event.preventDefault();
	const target = $(event.target);
	const path = target.attr('href');
	router.navigateTo(path);
});

function prepareCol(col, newClass) {
	col.removeClass();
	col.addClass('mainCol ' + newClass);
}

function clearContend() {
	$('#mainColLeft').html(null);
	$('#mainColMiddle').html(null);
	$('#mainColRicht').html(null);
}
