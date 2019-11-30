(function (){
    $(function(){
        var fncUrl = 'AjaxRequestHandlers.php';
        var game = {};
        var gameId = false;
        var currentPlayer = {};
        var opponent = false;
        var gameIntervalId = 0;

        $('#start-btn').click(onStartBtnClick);
        $('#draw').click(onDrawClick);
        $('#slap').click(onSlapClick);

        function onStartBtnClick(evt){
            currentPlayer.userName = $('#name-in').val();
            gameId = $('#game-id-in').val();

                $('#error-section').css('display', 'none');
            // If game ID is left blank, start new game.
            if (!gameId){
                // Start a new game.
                $.post(fncUrl,
                    {
                        action: 'newGame',
                        name: currentPlayer.userName
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
                        name: currentPlayer.userName
                    },
                    function (response){
                        if (isJsonString(response)){
                            game = JSON.parse(response);

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

                        if (!opponent && game.players.length > 1){
                            opponent = game.players.find(function (p){
                                return p.playerId !== currentPlayer.playerId;
                            });

                            $('#opponent-name').html('<strong>' + opponent.userName + '</strong>');
                        }

                        checkPlayerCount();
                        // Show/Hides sections of the game.
                        if (game.isPlaying){

                            // Shows top five cards in the discard pile.
                            updateDiscardPile();
                        }
                        
                    }
                    else {
                        $('#error-section').css('display', 'block');
                        // if it's a string, put it in error message section.
                        $('#error-section').html(response);
                    }
                });
        }

        function updateDiscardPile(){
            // Clear the elements in discard pile.
            $('#discard-pile').empty();

            var startIdx = game.gameDeck.cards.length-6;

            // Check if discard pile length is less than 5.
            if (startIdx < 0){
                startIdx = 0;
            }
            var topFiveCards = game.gameDeck.cards.slice(startIdx, game.gameDeck.cards.length);

            topFiveCards.forEach(function (card){
                var element = $('div');
                element.addClass('card ' + card.suit + '_' + card.value);

                $('#discard-pile').append(element);
            });
        }

        function startGame(){
            // Get current player data.
            currentPlayer = game.players.find(function (p){
                return p.userName === currentPlayer.userName;
            });

            $('#current-player-name').html('<strong>' + currentPlayer.userName + '</strong>');
            // Start the interval.
            gameIntervalId = setInterval(getGameState, 300);

            // Hide the start game group.
            $('#start-section').css('display', 'none');

            $('.game-id').html(game.gameId);
            
        }

        function checkPlayerCount(){
            if (game.players.length < 2){
                $('#wait-section').css('display', 'block');
                $('#game-section').css('display', 'none');
            }
            else {
                $('#wait-section').css('display', 'none');
                $('#game-section').css('display', 'block');

                // Set current player info
                currentPlayer = game.players.find(function (p){
                    return p.playerId === currentPlayer.playerId;
                });

                $('#current-player-card-count').html(currentPlayer.playerDeck.cards.length);

                // Set opponent info
                if (opponent){
                    opponent = game.players.find(function (p){
                        return p.playerId === opponent.playerId;
                    });
    
                    $('#opponent-card-count').html(opponent.playerDeck.cards.length);
                }
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