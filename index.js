(function (){
    $(function(){
        var fncUrl = 'AjaxRequestHandlers.php';
        var game = {};
        var gameId = false;
        var gameIntervalId = 0;

        // First turn on AJAX message checking.
        // var inervalId = setInterval(function (){
        //     $.get(fncUrl,
        //         {
        //             action: 'checkMsgs'
        //         },
        //         function (response){
        //             var messages = JSON.parse(response);

        //             var msgHtml = '';

        //             messages.forEach(function (m){
        //                 msgHtml += '<div>' + m + '</div>';
        //             });

        //             $('#messages').html(msgHtml);
        //         });
        // },
        // 500);


        
        // $('#send-msg-btn').click(function (evt){
        //     var msg = $('#msg-in').val();
        //     sendMsg(msg);
        // });

        // function sendMsg(msg){
        //     $.post(fncUrl,
        //         {
        //             action: 'sendMsg',
        //             msg: msg
        //         },
        //         function (response){
        //             console.log(response, 'Message sent');
        //         });
        // }

        $('#start-btn').click(onStartBtnClick);

        function onStartBtnClick(evt){
            console.log('start clicked');
            var personName = $('#name-in').val();
            gameId = $('#game-id-in').val();

                $('#error-section').css('display', 'none');
            // If game ID is left blank, start new game.
            if (!gameId){
                // Start a new game.
                $.post(fncUrl,
                    {
                        action: 'newGame',
                        name: personName
                    },
                    function (response){
                        if (isJsonString(response)){
                            var responseValue = JSON.parse(response);
                            game = responseValue;
                            gameId = responseValue.gameId;
                            startGame();
                        }
                        else {
                            $('#error-section').css('display', 'block');
                            // if it's a string, put it in error message section.
                            $('#error-section').html(response);
                        }
                    });
            }
            else {
                // Look up existing game.

                $.post(fncUrl,
                    {
                        action: 'findGame',
                        gameId: gameId,
                        name: personName
                    },
                    function (response){
                        if (isJsonString(response)){
                            game = response;
                            console.log(response);
                            startGame();
                        }
                        else {
                            $('#error-section').css('display', 'block');
                            // if it's a string, put it in error message section.
                            $('#error-section').html(response);
                        }
                    });
            }
        }
        
        function getGameState(){
            console.log(gameId);
            $.get(fncUrl,
                {
                    action: 'getGame',
                    gameId: gameId
                },
                function(response){
                    if (isJsonString(response)){
                        game = JSON.parse(response);

                        checkPlayerCount();

                        console.log(game);
                    }
                    else {
                        $('#error-section').css('display', 'block');
                        // if it's a string, put it in error message section.
                        $('#error-section').html(response);
                    }
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
            // Start the interval.
            gameIntervalId = setInterval(getGameState, 300);
            console.log(gameIntervalId);

            // Hide the start game group.
            $('#start-section').css('display', 'none');

            // Show the game.
            checkPlayerCount();
            
        }

        function checkPlayerCount(){
            if (game.players.length < 2){
                $('#wait-section').css('display', 'block');
                $('#game-section').css('display', 'none');
            }
            else {
                $('#wait-section').css('display', 'none');
                $('#game-section').css('display', 'block');
            }
        }

        function isJsonString(jsonString){
            try {
                JSON.parse(jsonString);
            }
            catch {
                return false;
            }
            return true;
        }
    })
})()