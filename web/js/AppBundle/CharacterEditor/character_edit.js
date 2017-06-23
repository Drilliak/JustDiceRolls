let jsVars = jQuery('#js-vars').data('vars').charData;

let ajaxPath = jsVars.ajaxPath;
let idUser = jsVars.idUser;
let idPlayerCharacter = jsVars.idPlayerCharacter;

/**
 * Création d'un nouveau sort.
 */
(function () {
    /**
     * Affichage du formulaire de création d'un sort
     */
    let newSpellBody = $('#create-new-spell-body');
    newSpellBody.on('click', '#create-new-spell-button', function () {
        $('#create-new-spell-button').remove();
        newSpellBody.append(`
            <fieldset>
                <legend>Ajout d'un nouveau sort</legend>
                <div class="panel panel-default">
                    <form class="form-horizontal" id="add-new-spell-form">
                        <div class="form-group">
                            <div class="panel-body">
                                <label for="spell-name" class="control-label col-xs-3 col-sm-2">Nom</label>
                                <div class="col-xs-9 col-sm-10">
                                    <input id="spell-name" type="text" class="form-control" placeholder="Nom du sort" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="panel-body">
                                <label for="spell-description" class="control-label col-xs-3 col-sm-2">Description</label>
                                <div class="col-xs-9 col-sm-10">
                                    <textarea id="spell-description" class="form-control" rows="5" 
                                    placeholder="Entrez la description de votre compétence." required ></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-default" id="add-new-spell-button" value="Ajouter le sort">
                    </form>
                 
                    
                </div>
            </fieldset>    
        `);
    });

    /**
     * Ajout du sort
     */
    newSpellBody.on('submit', "#add-new-spell-form", function(e){
        e.preventDefault();
        let spellName = $('#spell-name').val();
        let spellDescription = $('#spell-description').val();
       $('#create-new-spell-body fieldset').remove();
       newSpellBody.append(`<button class="btn btn-default" id="create-new-spell-button">Ajouter un sort</button>`);
       $.post(
            ajaxPath,
           {
               action: "add-new-spell",
               "idUser": idUser,
               "idPlayerCharacter": idPlayerCharacter,
               "spellName": spellName,
               "spellDescription": spellDescription
           },
           function(data){ console.log(data)},
           'json'
       );
    });

})();