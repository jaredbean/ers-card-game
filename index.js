(function (){
    $(function(){
        var fncUrl = 'serverSideFunctions.php';
        var game = {};
        var gameId = false;

        // First turn on AJAX message checking.
        var inervalId = setInterval(function (){
            $.get(fncUrl,
                {
                    action: 'checkMsgs'
                },
                function (response){
                    var messages = JSON.parse(response);

                    var msgHtml = '';

                    messages.forEach(function (m){
                        msgHtml += '<div>' + m + '</div>';
                    });

                    $('#messages').html(msgHtml);
                });
        },
        500);


        
        $('#send-msg-btn').click(function (evt){
            var msg = $('#msg-in').val();
            sendMsg(msg);
        });

        function sendMsg(msg){
            $.post(fncUrl,
                {
                    action: 'sendMsg',
                    msg: msg
                },
                function (response){
                    console.log(response, 'Message sent');
                });
        }


        $('#start-btn').click(function (evt){
            var personName = $('#name-in').val();
            gameId = $('#game-id-in').val();

            // If game ID is left blank, start new game.
            if (!gameId){
                // Start a new game.
                $.post(fncUrl,
                    {
                        action: 'newGame',
                        name: personName
                    },
                    function (response){
                        var responseValue = JSON.parse(response);
                        gameId = responseValue.gameId;
                        console.log(response);
                        startGame();
                    });
            }
            else {
                // Look up existing game.

                $.get(fncUrl,
                    {
                        action: 'findGame',
                        gameId: gameId
                    },
                    function (response){
                        console.log(response);
                        startGame();
                    });
            }
        });
        
        function getGameState(){
            $.get(fncUrl, 
                {
                    action: 'gameState'
                },
                function (response){
                    gameState = response;
                });
        }

        function saveGameState(){
            $.post(fncUrl,
                {
                    action: 'saveGameState'
                },
                function (response){
                    console.log('Game state saved');
                })
        }

        function startGame(){
            setInterval(function (){
                console.log(gameId);
                $.get(fncUrl,
                    {
                        action: 'getGame',
                        gameId: gameId
                    },
                    function(response){
                        game = JSON.parse(response);
                        console.log(game);
                    })
            }, 200);
        }
    })
})()