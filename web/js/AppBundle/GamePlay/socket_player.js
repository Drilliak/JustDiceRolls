// web/js/AppBuncle/GamePlay/socket_player.js

let socketAddr = jsVars.socketAddr;
let socketPort = jsVars.socketPort;

let conn = new WebSocket(`ws://${socketAddr}:${socketPort}`);
conn.onopen = function(e) {
    console.info("Connection established succesfully");
};

conn.onmessage = function(e) {
    console.log(e);
};

conn.onerror = function(e){
    console.error(e);
};