let jsVars = jQuery('#js-vars').data('vars').charData;

let players = jsVars.players;
let ajaxPath = jsVars.ajaxPath;
let idPlayers = jsVars.idPlayers;
let allowedCharacteristics = jsVars.allowedCharacteristics;

let lastProgressBarSelected;
let nbCharacteristicsHidden = 0;

$('#hidden-characteristics-menu').hide();


let canonicalCharacteristics = []

for (allowedCharacteristic of allowedCharacteristics){
    canonicalCharacteristics.push(allowedCharacteristic.trim().toLowerCase().replace(/\s/, ''));
}
console.log(canonicalCharacteristics)

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
 * Chargement des paramètres d'affichage
 */
(function(){
    if (localStorageSupported){
    } else {
        throw "Locale Storage not supported";
    }
})();

/**
 * Changement d'une valeur dans le tableau
 */
(function () {
    $('input').on('paste keyup mouseup', function () {
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

        if (characteristic.includes('max')){
            characteristic = characteristic.substr(0,characteristic.length-3);
        }

        if ($('#player-data-' + playerId + ' .characteristic .' + characteristic).find('.progress').length !==0){

            let value = $(`.player-${playerId} .${characteristic} input`).val();
            let maxValue = $(`.player-${playerId} .${characteristic + 'max'} input`).val();
            let width;
            if (maxValue === 0){
                width = "100%";
            } else {
                width = value/maxValue*100 + "%";
            }
            console.log(width);
            let progressBar = $('#player-data-' + playerId + ' .characteristic .' + characteristic)
                .find('.progress').find('.progress-bar');
            progressBar.css({ "width": width });
            progressBar.html(`<span>${value}/${maxValue}</span>`);

        } else {
            $('#player-data-' + playerId + ' .characteristic .' + characteristic).find('span').html(newValue);
        }
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
        $('.' + characteristicName).not('form .' + characteristicName).hide();
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
        let characteristicName = lastProgressBarSelected.parent().attr('class');
        let idPlayer = lastProgressBarSelected.closest('tr').attr('id').split('-')[2];

    });

})();