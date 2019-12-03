(function (){
    $(function(){
        var fncUrl = 'AjaxRequestHandlers.php';
        var game = {};
        var currentDiscardPile = [];
        var gameId = false;
        var currentPlayer = {};
        var opponent = false;
        var gameIntervalId = 0;

        // Add click event handlers.
        $('#start-btn').click(onStartBtnClick);
        $('#draw').click(onDrawClick);
        $('#slap').click(onSlapClick);

        /**
         * Saves the player info to a new or existing game and starts the game.
         * @param {event} evt the event data.
         */
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

        /**
         * Draw button event handler. Calls the play card server function.
         */
        function onDrawClick(){
            // Check players turn.

            // if (game.playerIndex === currentPlayer.)
            $.post(fncUrl,
                {
                    action: 'playCard',
                    gameId: game.gameId,
                    playerId: currentPlayer.playerId
                },
                function (response){
                    if (isJsonString(response)){

                    }
                    else {
                        $('#error-section').css('display', 'block');
                        // if it's a string, put it in error message section.
                        $('#error-section').html(response);
                    }
                }
                );
        }

        /**
         * Slap button event handler. Calls the slap card server function.
         */
        function onSlapClick(){
            $.post(fncUrl,
                {
                    action: 'slapCard',
                    gameId: game.gameId,
                    playerId: currentPlayer.playerId
                },
                function (response){
                    if (isJsonString(response)){
                        if (JSON.parse(response)){
                            // displayMessage('You won the slap.');
                        }
                    }
                    else {
                        $('#error-section').css('display', 'block');
                        // if it's a string, put it in error message section.
                        $('#error-section').html(response);
                    }
                }
                );
        }

        /**
         * Starts the interval that checks the game state and updates the display to start the game.
         */
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
        
        /**
         * Checks the game state and updates the UI according to it.
         */
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
                            // Look up the player whose turn it is.
                            var turnPlayer = game.players[game.playerIndex];
                            $('#player-turn-name').html(turnPlayer.userName);

                            // Shows top five cards in the discard pile.
                            updateDiscardPile();

                            $('#draw').prop('disabled', !currentPlayer.playerDeck.isClickable);

                            // if(game.isRoundWon){
                            //     displayMessage(game.nameOfRoundWinner + ' won the round.');
                            // }
                        }

                        if (game.isGameOver) {
                            $('#draw').prop('disabled', true);
                            $('#slap').prop('disabled', true);
                        }
						
						if (game.isRoundWon == true)
						{
							$('#round-win-section').css('display', 'block');
							$('.round-winner').html(game.nameOfRoundWinner + " won the round.");
						}
						
						if (game.isRoundWon == false)
						{
							$('#round-win-section').css('display', 'none');
						}
						
						if (game.isGameOver == true)
						{
							$('#game-win-section').css('display', 'block');
							$('.game-winner').html(game.nameOfGameWinner + " is the winner.");
						}
                        
                    }
                    else {
                        $('#error-section').css('display', 'block');
                        // if it's a string, put it in error message section.
                        $('#error-section').html(response);
                    }
                });
        }

        /**
         * Updates the display according to the player count and 
         * player data.
         */
        function checkPlayerCount(){
            
            // Show or hide the game section according to the player count.
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
        
        /**
         * Display cards in the discard pile according to the game deck property 
         * on the game object.
         */
        function updateDiscardPile(){
            if (game.gameDeck.cards.length > 0){
                var startIdx = game.gameDeck.cards.length-5;
    
                // Check if discard pile length is less than 5.
                if (startIdx < 0){
                    startIdx = 0;
                }
                var topFiveCards = game.gameDeck.cards.slice(startIdx, game.gameDeck.cards.length);
                if (!topFiveCards.every(function (c, idx){ return currentDiscardPile[idx] && cardEquals(c, currentDiscardPile[idx]); })){
                    
                    // Clear the elements in discard pile.
                    $('#discard-pile').empty();
                    topFiveCards.forEach(function (card){
                        var $element = $(document.createElement('div'));
                        $element.addClass('discarded card ' + card.suit + '_' + card.value);
        
                        $('#discard-pile').append($element);
                        currentDiscardPile.push(card)
                    });
                }
            }
            else if (currentDiscardPile.length !== 0) {
                // Clear the elements in discard pile.
                $('#discard-pile').empty();
                var $newElement = $(document.createElement('div'));

                $newElement.addClass('card empty');
                $('#discard-pile').append($newElement);
            }
        }

        /**
         * Checks a string to determine if it's in a JSON format.
         * @param {string} jsonString the string to check.
         */
        function isJsonString(jsonString){
            try {
                JSON.parse(jsonString);
            }
            catch {
                return false;
            }
            return true;
        }

        function cardEquals(card1, card2){
            return card1.suit === card2.suit &&
                    card1.value === card2.value;
        }

        // function displayMessage(message){
        //     $('.msg-box').html(message);
        //     $('.msg-box').fadeIn();

        //     setTimeout(function (){
        //         $('.msg-box').fadeOut();
        //     }, 2000);
        // }
    })
})()