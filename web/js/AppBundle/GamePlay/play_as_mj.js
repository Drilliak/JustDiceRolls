let jsVars = jQuery('#js-vars').data('vars').charData;

let players = jsVars.players;
let ajaxPath = jsVars.ajaxPath;


let lastProgressBarSelected;
let nbCharacteristicsHidden = 0;

$('#hidden-characteristics-menu').hide();

/**
 * Formate une chaine de caractères en supprimant tous les espaces et en passant
 * tous les caractères en minuscules
 *
 * @param text
 * @returns {string}
 */
function format(text) {
    return text.replace(/\s/g, '').toLowerCase()
}

(function () {
    $('[data-toggle="tooltip"]').tooltip();
})();

/**
 * Changement d'une valeur dans le tableau
 */
(function () {
    $('input').on('paste keyup', function () {
        let characteristic = $(this).closest("th").attr('class');
        let playerId = $(this).closest("tr").attr('class').split("-")[1];
        let newValue = $(this).val().trim();

        $.post(
            ajaxPath,
            {
                "action": "update-stat",
                "playerId": playerId,
                "characteristic": characteristic,
                "newValue": newValue
            },
            function (data) {
            },
            'json'
        );
    });
})();


/**
 * Masque une colonne
 */
(function () {
    $(document).on('click', 'table thead th .glyphicon-eye-close', function () {
        if (nbCharacteristicsHidden == 0)
            $('#hidden-characteristics-menu').show();
        nbCharacteristicsHidden++;
        let characteristicName = $(this).parent().attr('class');
        let nameShowed = $(this).parent().text();
        $('.' + characteristicName).hide();
        $('#hidden-characteristics').append(`
            <li id="${characteristicName}"><a class="hidden-characteristic" href="javascript:void(0)">${nameShowed}</a></li>
        `);
    });
})();

/**
 * Réaffiche une colonne
 */
(function () {
    $(document).on('click', '.hidden-characteristic', function () {
        let characteristicName = $(this).closest('li').attr('id');
        $(this).closest('li').remove();
        nbCharacteristicsHidden--;
        if (nbCharacteristicsHidden == 0)
            $('#hidden-characteristics-menu').hide();
        $('.' + characteristicName).show();
    });
})();

/**
 * Affiche ou masque la fiche personnage
 */
(function () {
    $(document).on('click', '.glyphicon-menu-down', function () {
        let playerId = $(this).closest('tr').attr('class').split('-')[1];
        $(this).toggleClass('glyphicon-menu-down glyphicon-menu-up');
        $("#player-data-" + playerId).show();
    });

    $(document).on('click', '.glyphicon-menu-up', function () {
        let playerId = $(this).closest('tr').attr('class').split('-')[1];
        $(this).toggleClass('glyphicon-menu-up glyphicon-menu-down');
        $("#player-data-" + playerId).hide();
    });
})();

/**
 * Affichage clique droit différent sur les barres de progressions
 */
(function () {

    let contextMenu = $('#context-menu');

    $(document).on('contextmenu', '.progress-bar', function (e) {
        lastProgressBarSelected = $(this);
        contextMenu.css({
            display: "block",
            left: e.pageX,
            top: e.pageY
        });

        $(document).mouseup(function (e) {
            if (!contextMenu.is(e.target) && contextMenu.has(e.target).length === 0) {
                contextMenu.hide();
            }
        });
        return false;
    });
})();

/**
 * Changement de la barre de progression au clique sur le menu
 */
(function () {
    let contextMenu = $('#context-menu');
    $(document).on('click', '#context-menu .progress-bar', function () {
        let newClass = $(this).attr('class');
        lastProgressBarSelected.removeClass().addClass(newClass);
    });

})();