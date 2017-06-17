let jsVars = jQuery('#js-vars').data('vars').charData;

let allowedCharacteristics = jsVars.allowedCharacteristics;
let players = jsVars.players;


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
(function(){
    for (allowedCharacteristic of allowedCharacteristics){
        canonicalAllowedCharacteristics.push(format(allowedCharacteristic));
    }
})();

/**
 * Définit les entêtes du tableau
 */
(function () {
    let headers = `<tr class="header"><th class="user">Utilisateur</th><th class="character">Personnage</th>`;
    for (allowedCharacteristic of allowedCharacteristics){
        headers += `<th class="${format(allowedCharacteristic)}">${allowedCharacteristic}</th>`;
    }
    headers += `</tr>`;
    $('table thead').append(headers);
})();

/**
 * Remplit le tableau
 */
(function(){
    console.log(players);
    for (player of players){
        let row = `<tr class="player-${player.id}">`;
        if (player.playerName === "N/A"){
            row += `<th class="user">${player.username}</th><th style="text-align:center;" colspan="${allowedCharacteristics.length+1}">Ce joueur n'a pas encore créé son personnage</th>`;
        } else {
            row += `<th class="user">${player.username}</th><th class="character">${player.playerName}</th>`;
            for (canonicalAllowedCharacteristic of canonicalAllowedCharacteristics){
                row += `<th class="${canonicalAllowedCharacteristic}"><input class="form-control" value="${player.characteristics[canonicalAllowedCharacteristic]}"></th>`;
            }
        }

        row += `</tr>`;
        $('table tbody').append(row);
    }

})();