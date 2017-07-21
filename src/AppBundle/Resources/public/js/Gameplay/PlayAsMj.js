class PlayAsMj {
    constructor() {
        this.jsVars = $('#js-vars').data('vars').charData;
        this.ajaxPath = Routing.generate('game_play_mj_ajax');
        console.log(this.ajaxPath);
        this.main();
    }

    main() {
        this.changeFullDataVisibility();
        this.changeStatValue();
    }

    /**
     * Change la visibilité (l'affiche ou le masque selon l'état actuel)
     * du bloc donnant les donnée complètes du joueur
     */
    changeFullDataVisibility() {
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
    }

    /**
     * Change la statistique d'un personnage
     */
    changeStatValue() {
        let ajaxPath = this.ajaxPath;
        $('.player-summary').on('input', 'input', function () {
            let value = $(this).val();
            let statName = $(this).parent().attr('class');
            let statId = $(this).attr('id').split('-')[1];
            let playerId = $(this).parent().parent().attr('id').split('-')[2];
            let maxStat = $('#' + statName + '-max-' + statId);
            let statisticId = $(this).attr('id').split('-')[1];
            let username = $(this).parent().parent().find('td.user').text().trim();

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

            console.log(username);
            $.post(
                ajaxPath,
                {
                    action: "change-stat-value",
                    value: value,
                    statId: statisticId,
                    "username": username

                },
                function (data) {
                    console.log(data.message);
                },
                'json'
            );
        });
    }
}

$(document).ready(new PlayAsMj());