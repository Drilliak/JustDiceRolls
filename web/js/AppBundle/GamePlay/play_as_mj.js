let jsVars = jQuery('#js-vars').data('vars').charData;

let ajaxPath = Routing.generate('game_play_mj_ajax');

let debug = true;
/**
 * Affiche ou masque les caractéristiques complètes du joueur
 */
(function () {
    let playersData = $('#players-data');
    playersData.on('click', '.show-player-data', function () {
        let playerId = $(this).parent().parent().attr('id').split('-')[2];
        $(this).toggleClass('show-player-data hide-player-data');
        $(this).find('span').toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
        $('#player-data-' + playerId).show();
    });

    playersData.on('click', '.hide-player-data', function () {
        let playerId = $(this).parent().parent().attr('id').split('-')[2];
        $(this).toggleClass('hide-player-data show-player-data');
        $(this).find('span').toggleClass('glyphicon-chevron-up glyphicon-chevron-down');
        $('#player-data-' + playerId).hide();
    });
})();

/**
 * Modification de la valeur d'un des champs "résumé" du tableau
 */
(function () {
    $('.player-summary').on('input', 'input', function () {
        let value = $(this).val();
        let statName = $(this).parent().attr('class');
        let statId = $(this).attr('id').split('-')[1];
        let playerId = $(this).parent().parent().attr('id').split('-')[2];
        let maxStat = $('#' + statName + '-max-' + statId);
        let statisticId = $(this).attr('id').split('-')[1];

        // Adaptation des barres affichées dans le résumé
        if (maxStat.length !== 0) {
            let maxValue = maxStat.val();
            let progressBar = $('#' + statName + "-progress-" + playerId);
            let width;
            if (maxValue === 0) {
                width = "100%";
            } else {
                width = value / maxValue * 100 + "%";
            }

            progressBar.css({"width": width});
            progressBar.html(`<span>${value}/${maxValue}</span>`);
        }

        // Modification des données sur le serveur
        $.post(
            ajaxPath,
            {
                action: "change-stat-value",
                value: value,
                statId: statisticId

            },
            function (data) {
                if (debug) {
                    console.log(data.message);
                }

            },
            'json'
        );
    });
})();

/**
 * Changer le nombre de sorts d'un joueur
 */
(function () {
    $('.player-data').on('input', '.nb-spells-max', function () {
        let nbSpells = $(this).val();
        let playerId = $(this).closest('tr').attr('id').split('-')[2];
        $.post(
            ajaxPath,
            {
                action: "change-nb-spells",
                value: nbSpells,
                playerId: playerId
            },
            function (data) {
                if (debug) {
                    console.log(data.message);
                }
            },
            'json'
        );
    });
})();

/**
 * Modification d'un statistique max
 */
(function () {
    $('.statistics').on('input', '.max-stat', function () {
        let stat = $(this).attr('id').split('-')[0];
        let maxValue = $(this).val();
        let playerId = $(this).closest('tr').attr('id').split('-')[2];
        let value = $('#player-summary-' + playerId + ' .' + stat).find('input').val();
        let statId = $(this).attr('id').split('-')[2];
        console.log(statId);

        let progressBar = $('#' + stat + "-progress-" + playerId);
        let width;
        if (maxValue === 0) {
            width = "100%";
        } else {
            width = value / maxValue * 100 + "%";
        }

        progressBar.css({"width": width});
        progressBar.html(`<span>${value}/${maxValue}</span>`);

        $.post(
            ajaxPath,
            {
                action: "change-max-stat-value",
                maxValue: maxValue,
                statId: statId
            },
            function (data) {
                if (debug) {
                    console.log(data.message);
                }
            },
            'json'
        );
    });
})();

(function () {
    $('.characteristics').on('input', '.charac-value', function () {
        let value = $(this).val();
        let characId = $(this).attr('id').split('-')[1];
        $.post(
            ajaxPath,
            {
                action: "change-characteristic",
                value: value,
                characId: characId,
            },
            function (data) {
                console.log(data.message);
            },
            'json'
        );
    });
})();