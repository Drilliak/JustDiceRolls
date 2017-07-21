/**
 * Classe gérant la connexion au websocket ainsi que l'acutalisation de la page.
 */
class SocketPlayer{
    constructor(idGame, username, wsUri){
        this.idGame = idGame;
        this.username = username;
        this.webSocket = WS.connect(wsUri);
        this.main();
    }

    /**
     * Fonction main appeléé dans le constructeur visant à déclencher tout le nécessaire javascript
     * de la page
     */
    main(){
        this.startConnection();
    }

    /**
     * Lance la connexion au WS et souscrit l'utilisateur au topic correspondant
     */
    startConnection(){
        let channel = `player-channel/${this.idGame}/${this.username}`;
        let that = this;
        this.webSocket.on("socket/connect", function(session){
            session.subscribe(channel, function(uri, payload){
                console.log('Received message', payload.msg);
                let data;
                try{
                    data =  JSON.parse(payload.msg);
                } catch(e){
                    data = payload.msg;
                }

                if (typeof data === "object"){
                    let action = data.action;
                    switch (action){
                        case "changeStatValue":
                            let statId = data.statId;
                            let value = data.value;
                            that.changeStatValue(statId, value);
                            break;
                    }
                }
            });

        });

        this.webSocket.on("socket/disconnect", function(error){
            console.log("Disconnected for " + error.reason + " with code " + error.code);
        });
    }

    changeStatValue(statId, value){

    }





}
$(document).ready(new SocketPlayer(GAME_ID, USERNAME, WS_URI));
