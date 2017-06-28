/**
 * Varriables passées par le contrôleur
 */
let jsVars = jQuery('#js-vars').data('vars').charData;

let ajaxPath = Routing.generate('game_edition_ajax');
let idGame = jsVars.idGame;
let autocompletePath = Routing.generate('game_edition_autocomplete');

/**
 * Ajoute un statistique
 */
(function () {
    $('#statistic-body').on('click', '#new-statistic-button', function () {
        let newStatInput = $('#new-statistic-input');
        let newStat = newStatInput.val().trim();
        let tempId = "temp-" + Math.floor(Math.random() * (1000));

        $('#statistic-tbody').append(`
            <tr id="${tempId}">
                <td>${newStat}</td>
                <td>
                    <select class="form-control input-xs">
                        <option>Oui</option>
                        <option selected >Non</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-statistic"> Supprimer</button>
                </td>
            </tr>
        `);
        newStatInput.val('');

        $.post(
            ajaxPath,
            {
                action: "add-statistic",
                stat: newStat,
                idGame: idGame
            },
            function (data) {
                $(`#${tempId}`).attr('id', data.id);
            },
            'json'
        );
    });
})();

/**
 * Supprime une statistique
 */
(function () {
    $('#statistic-tbody').on('click', '.remove-statistic', function () {
        let tr = $(this).parent().parent();
        let statId = tr.attr('id');
        tr.remove();

        $.post(
            ajaxPath,
            {
                action: "remove-stat",
                statId: statId,
                idGame: idGame
            },
            function (data) {
            },
            'json'
        );

    });
})();

/**
 * Ajoute une caractéristique
 */
(function () {
    $('#characteristic-body').on('click', '#new-characteristic-button', function () {
        let newCharacteristicInput = $('#new-characteristic-input');
        let characteristic = newCharacteristicInput.val().trim();
        let tempId = "temp-" + Math.floor(Math.random() * (1000));
        $('#characteristic-tbody').append(`
            <tr id="${tempId}">
                <td>${characteristic}</td>
                <td>
                    <button type="button" class="btn btn-danger remove-characteristic"> Supprimer</button>
                </td>
            </tr>
        `);
        newCharacteristicInput.val('');
        $.post(
          ajaxPath,
            {
                action: "add-characteristic",
                characteristic: characteristic,
                idGame: idGame
            },
            function(data){
              $(`#${tempId}`).attr('id', data.id);
            },
            'json'
        );

    });
})();

/**
 * Supprime une caractéristique
 */
(function () {
    $('#characteristic-tbody').on('click', '.remove-characteristic', function () {
        let tr = $(this).parent().parent();
        let trId = tr.attr('id');

        tr.remove();
        $.post(
          ajaxPath,
            {
                action: "remove-characteristic",
                characteristicId: trId,
                idGame: idGame
            },
            function(data){

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
            function (data) {
                console.log(data);
            },
            'json'
        )
    });
})();

/**
 * Modification de la valeur du nombre de sorts par personnage
 */
(function () {
    $(document).on('input', '#nb-spells-max', function () {
        $.post(
            ajaxPath,
            {
                action: "change-nb-spells-max",
                value: $(this).val(),
                "idGame": idGame
            },
            function (data) {
            },
            'json'
        );
    });
})();