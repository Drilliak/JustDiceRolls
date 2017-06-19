let jsVars = jQuery('#js-vars').data('vars').charData;

let allowedCharacteristics = jsVars.allowedCharacteristics;
let players = jsVars.players;
let ajaxPath = jsVars.ajaxPath;
let gameId = jsVars.gameId;

let canonicalAllowedCharacteristics = [];

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
 * Crée le tableau des caractéristiques canoniques.
 */
(function () {
    for (allowedCharacteristic of allowedCharacteristics) {
        canonicalAllowedCharacteristics.push(format(allowedCharacteristic));
    }
})();

/**
 * Définit les entêtes du tableau
 */
(function () {
    let headers = `<tr class="header"><th class="user">Utilisateur</th><th class="character">Personnage</th>`;
    for (allowedCharacteristic of allowedCharacteristics) {
        headers += `<th class="${format(allowedCharacteristic)}">${allowedCharacteristic}</th>`;
    }
    headers += `</tr>`;
    $('table thead').append(headers);
})();

/**
 * Remplit le tableau
 */
(function () {
    console.log(players);
    for (player of players) {
        let row = `<tr class="player-${player.id}">`;
        if (player.playerName === "N/A") {
            row += `<th class="user">${player.username}</th><th style="text-align:center;" colspan="${allowedCharacteristics.length + 1}">Ce joueur n'a pas encore créé son personnage</th>`;
        } else {
            row += `<th class="user">${player.username}</th><th style="display: flex; justify-content: space-between;" class="character">${player.playerName}<span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span></th>`;
            for (canonicalAllowedCharacteristic of canonicalAllowedCharacteristics) {
                row += `<th class="${canonicalAllowedCharacteristic}"><input class="form-control" type="number" value="${player.characteristics[canonicalAllowedCharacteristic]}"></th>`;
            }
        }

        row += `</tr>`;
        $('table tbody').append(row);
    }

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
    $(document).on('click', 'table thead th', function () {
        if (nbCharacteristicsHidden == 0)
            $('#hidden-characteristics-menu').show();
        nbCharacteristicsHidden++;
        let characteristicName = $(this).attr('class');
        let nameShowed = $(this).text();
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
        $(this).toggleClass('glyphicon-menu-down glyphicon-menu-up');
        let playerId = $(this).closest('tr').attr('class').split('-')[1];
        let closestTr = $(this).closest('tr');
        $.get(
            ajaxPath,
            {
                action: "get-data-character",
                "playerId": playerId
            },
            function (data) {
                let tokenPath = data.tokenPath;
                closestTr.after(`
                <tr id="${'player-data-' + playerId}">
                    <th colspan="${allowedCharacteristics.length + 2}">
                        <div class="container">
                            <div class="col-xs-2 col-sm-1">
                                <img src="${tokenPath}">
                            </div>
                            <div class="col-xs-10 col-sm-11">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                       <div class="progress">
                                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" 
                                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:40%"></div>
                                        </div> 
                                    </li>
                                    <li class="list-group-item">Test2</li>
                                </ul>
                            </div>
                            
                        </div>                        
                    </th>
                </tr>
                `);
            },
            'json'
        );

    });
    $(document).on('click', '.glyphicon-menu-up', function () {
        let playerId = $(this).closest('tr').attr('class').split('-')[1];
        $(this).toggleClass('glyphicon-menu-up glyphicon-menu-down');
        $("#player-data-" + playerId).remove();
    });
})();


/**
 * Affichage clique droit différent sur les barres de progressions
 */
(function(){

    let contextMenu = $('#context-menu');

    $(document).on('contextmenu', '.progress-bar',function(e){
        lastProgressBarSelected = $(this);
        contextMenu.css({
           display: "block",
            left: e.pageX,
            top: e.pageY
        });

        $(document).mouseup(function(e){
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
(function(){
    let contextMenu = $('#context-menu');
    $(document).on('click', '#context-menu .progress-bar', function(){
       let newClass = $(this).attr('class');
       lastProgressBarSelected.removeClass().addClass(newClass);
    });

})();