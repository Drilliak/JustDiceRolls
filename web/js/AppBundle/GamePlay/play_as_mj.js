let jsVars = jQuery('#js-vars').data('vars').charData;

let allowedCharacteristics = jsVars.allowedCharacteristics;
let players = jsVars.players;
let ajaxPath = jsVars.ajaxPath;
let gameId = jsVars.gameId;

let canonicalAllowedCharacteristics = [];
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
            row += `<th class="user">${player.username}</th><th style="display: flex; justify-content: space-between;" class="character">${player.playerName}<span class="glyphicon glyphicon-menu-down"></span></th>`;
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
                "playerId": playerId,
                "characteristic": characteristic,
                "newValue": newValue,
                "gameId": gameId
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
        characteristicName = $(this).attr('class');
        $('.' + characteristicName).hide();
    });
})();

/**
 * Affiche ou masque la fiche personnage
 */
(function () {
    $(document).on('click', '.glyphicon-menu-down', function () {
        $(this).toggleClass('glyphicon-menu-down glyphicon-menu-up');
        let playerId = $(this).closest('tr').attr('class').split('-')[1];
        $.get(

        );
        $(this).closest('tr').after(`
            <tr id="${'player-info-' + playerId}">
                <th colspan="${allowedCharacteristics.length + 2}">
                    <ul class="list-group">
                      <li class="list-group-item active">Cras justo odio</li>
                      <li class="list-group-item">Dapibus ac facilisis in</li>
                      <li class="list-group-item">Morbi leo risus</li>
                      <li class="list-group-item">Porta ac consectetur ac</li>
                      <li class="list-group-item">Vestibulum at eros</li>
                    </ul>
                </th>
            </tr>`);
    });
    $(document).on('click', '.glyphicon-menu-up', function () {
        let playerId = $(this).closest('tr').attr('class').split('-')[1];
        $(this).toggleClass('glyphicon-menu-up glyphicon-menu-down');
        $("#player-info-" + playerId).remove();
    });
})();