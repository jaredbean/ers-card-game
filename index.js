(function (){
    $(function(){
        var fncUrl = 'AjaxRequestHandlers.php';
        var game = {};
        var gameId = false;
        var currentPlayer = {};
        var gameIntervalId = 0;

        function getCard(){
            var suit = "C";

            var value = "3";
            var cardClass = "";
            //remove old class
            $('#top-card').removeClass();

            // Add class
            $('#top-card').addClass('card ' + value + '_' + suit)
        }

        $('#start-btn').click(onStartBtnClick);
        $('#draw').click(onDrawClick);
        $('#slap').click(onSlapClick);

        function onStartBtnClick(evt){
            currentPlayer.name = $('#name-in').val();
            gameId = $('#game-id-in').val();

                $('#error-section').css('display', 'none');
            // If game ID is left blank, start new game.
            if (!gameId){
                // Start a new game.
                $.post(fncUrl,
                    {
                        action: 'newGame',
                        name: currentPlayer.name
                    },
                    function (response){
                        if (isJsonString(response)){
                            var responseValue = JSON.parse(response);
                            game = responseValue;
                            gameId = responseValue.gameId;

                            // Get current player data.
                            currentPlayer = game.players.find(function (p){
                                return p.name === currentPlayer.name;
                            });

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
                        name: currentPlayer.name
                    },
                    function (response){
                        if (isJsonString(response)){
                            game = JSON.parse(response);

                            // Get current player data.
                            currentPlayer = game.players.find(function (p){
                                return p.name === currentPlayer.name;
                            });

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

        function onDrawClick(){
            $.post(fncUrl,
                {
                    action: 'playCard',
                    gameId: game.gameId,
                    playerId: currentPlayer.playerId
                });
        }

        function onSlapClick(){
            $.post(fncUrl,
                {
                    action: 'slapCard',
                    gameId: game.gameId,
                    playerId: currentPlayer.playerId
                });
        }
        
        function getGameState(){
            $.get(fncUrl,
                {
                    action: 'getGame',
                    gameId: gameId
                },
                function(response){
                    if (isJsonString(response)){
                        game = JSON.parse(response);

                        checkPlayerCount();
                    }
                    else {
                        $('#error-section').css('display', 'block');
                        // if it's a string, put it in error message section.
                        $('#error-section').html(response);
                    }
                });
        }

        function updateDiscardPile(){
            var newDiscardPileElements = [];
            $('#discard-pile').empty();
            var topFiveCards = game.discardDeck.cards.slice(0, 5);
            topFiveCards.forEach(function (card){
                var element = $('div');
                element.addClass('card ' + card.suit + '_' + card.value);

                $('#discard-pile').prepend(element);
            });
        }

        function startGame(){
            // Start the interval.
            gameIntervalId = setInterval(getGameState, 300);

            // Hide the start game group.
            $('#start-section').css('display', 'none');

            $('.game-id').html(game.gameId);

            // Show the game.
            checkPlayerCount();
            if (game.players[game.indexOfPlayersTurn].playerId !== currentPlayer.playerId){
                $('#draw').enable(false);
            }
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

            return game.players.length < 2;
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