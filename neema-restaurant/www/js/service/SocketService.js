
app.factory('SocketService',[
    'socketFactory','URL_SOCKET',
    function(socketFactory,URL_SOCKET){
        var myIoSocket = io(URL_SOCKET);
        return socketFactory({
            ioSocket: myIoSocket
        });
    }
]);