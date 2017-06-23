/**
 * Varriables passées par le contrôleur
 */
let jsVars = jQuery('#js-vars').data('vars').charData;

let ajaxPath = jsVars.ajaxPath;
let idGame = jsVars.idGame;
let autocompletePath = jsVars.autocompletePath;

/**
 * Ajoute une caractéristique
 */
(function () {
    $("#add-characteristic").click(function () {

        // Si le champ de saisie de la nouvelle caractéristique est vide,
        // on ne fait rien. Html5 affichera la nécessité de remplir ce champ.
        let newCharacteristicName = $('#new-characteristic').val();
        if (newCharacteristicName == '') {
            return;
        }
        // Ajoute la caractéristique au tableau de la page
        $("#allowed-characteristics").append(`
       <tr>
            <td class="characteristic-name"> ${newCharacteristicName} </td>
            <td class="characteristic-has-max"> 
                <select class = "form-control input-xs has-max">
                    <option>Oui</option>
                    <option selected>Non</option>
                </select> 
            </td>
            <td><button type="button" class="btn btn-danger remove-characteristic">Supprimer</button></td>
        </tr>
       `);

        // Envoie la caractéristique au serveur pour l'ajoute à la BDD
        $.get(
            ajaxPath,
            {
                action: 'add-characteristic',
                "idGame": idGame,
                "newCharacteristicName": newCharacteristicName
            },
            function (data) {
            },
            'json'
        );
    });


})();

/**
 * Modifier la valeur hasMax d'une caractéristique
 */
(function () {
    $('#allowed-characteristics').on('change', 'select', function (e) {
        let newHasMax = $(this).val().trim();
        let characteristicName = $(this).closest('tr').find(".characteristic-name").text();

        $.post(
            ajaxPath,
            {
                action: 'change-has-max',
                "idGame": idGame,
                "characteristicName": characteristicName,
                "newHasMax": newHasMax
            },
            function (data) {
                console.log(data);
            },
            'json'
        );
    });
})();

/**
 * Supprime une caractéristique
 */
(function () {
    $('#allowed-characteristics').on('click', '.remove-characteristic', function (e) {
        let characteristicName = $(this).closest('tr').find(".characteristic-name").text().trim();
        let hasMax = $(this).closest('tr').find('.has-max').val();
        $(this).closest('tr').remove();

        $.post(
            ajaxPath,
            {
                action: 'remove-characteristic',
                "idGame": idGame,
                "characteristicName": characteristicName,
                "hasMax": hasMax
            },
            function (data) {
                console.log(data);
            },
            'json'
        );
    });
})();

/**
 * Autocompletion pour les joueurs.
 */
(function () {
    $('#new-player').autocomplete({
        source: autocompletePath,
        select: function (event, ui) {
            let playerName = ui.item.value.trim();
            $("#players").append(`
                <tr>
                    <td class="player-name">${playerName}</td>
                    <td>
                        <button type="button" class="btn btn-danger remove-player">Retirer
                        </button>
                    </td>
                </tr>
            `);
            $.post(
                ajaxPath,
                {
                    action: "add-player",
                    'idGame': idGame,
                    'playerName': playerName
                },
                function (data) {

                },
                'json'
            );
            ui.item.value = "";
        }
    });
})();

/**
 * Retire un joueur d'une partie.
 */
(function () {
    $('#players').on('click', '.remove-player', function (e) {
        let playerName = $(this).closest('tr').find(".player-name").text();
        $(this).closest('tr').remove();
        $.post(
            ajaxPath,
            {
                action: "remove-player",
                'idGame': idGame,
                "playerName": playerName
            },
            function(data){
                console.log(data);
            },
            'json'
        )
    });
})();

/**
 * Modification de la valeur du nombre de sorts par personnage
 */
(function(){
    $(document).on('input','#nb-spells-max', function(){
        $.post(
            ajaxPath,
            {
                action: "change-nb-spells-max",
                value: $(this).val(),
                "idGame": idGame
            },
            function(data){},
            'json'
        );
    });
})();