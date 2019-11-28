(function (){
    $(function(){
        var fncUrl = 'AjaxRequestHandlers.php';
        var game = {};
        var gameId = false;
        var currentPlayer = {};
        var gameIntervalId = 0;

        $('#start-btn').click(onStartBtnClick);

        function onStartBtnClick(evt){
            console.log('start clicked');
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
            console.log(gameIntervalId);

            // Hide the start game group.
            $('#start-section').css('display', 'none');

            $('.game-id').html(game.gameId);

            // Show the game.
            checkPlayerCount();
            
        }

        function displayGame(){

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