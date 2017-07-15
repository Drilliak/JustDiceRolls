let webSocket = WS.connect(WS_URI);

webSocket.on("socket/connect", function(session){
   session.subscribe("acme/channel/1", function(uri, payload){
       console.log('Received message', payload.msg);
   });

});

webSocket.on("socket/disconnect", function(error){
    console.log("Disconnected for " + error.reason + " with code " + error.code);
});