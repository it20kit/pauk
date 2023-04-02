<?php

declare(strict_types=1);

namespace App\Systems;

use App\DeckFactory;
use App\EventBus;
use App\Events\Event;
use App\GameObject\Card;
use App\GameObject\Deck;

class GameManagerSystem extends AbstractSystem
{
    protected array $events = [
        "createInitialStateOfGame" => "createInitialStateOfGame",
        "checkPlayerInput" => "processingPlayerInput",
        "giveGameDataToDisplayInCaseOfError" => "giveGameDataToDisplayInCaseOfError",
        "updateGameState" => "updateGameState",
        "cancelMove" => "cancelMove",
        "dealCards" => "dealCards",
        "isGameOver" => "isCameOver",
        "searchCompletedDeck" => "searchCompletedDeck",
        "giveHint" => "giveHint",
        "createDecks" => "createDecks",
        "giveDecks" => "giveDecksToDisplay"
    ];

    public const
        CANCEL_MOVE = "c",
        SUGGEST = "h",
        DISTRIBUTE = "d";


    /**
     * @var Deck[] $decks;
     */

    private array $decks = [];

    private Deck $mainDeck;

    private array $completedDecks = [];

    private  int $step = 0;

    private int $score = 1000;

    private DeckFactory $deckFactory;

    public function __construct(DeckFactory $deckFactory)
    {
        $this->deckFactory= $deckFactory;
    }

    public function updateGameState(Event $logicEvent, EventBus $eventBus): void
    {
        $data = $logicEvent->getData();
        $moves = $data['moves'];
        $eventBus->clearEvents();
        $displayEvent = $this->createEvent();
        $from = $moves[0] - 1;
        $numberCard = $moves[1] -1;
        $to = $moves[2]- 1;
        $decks = $this->decks;
        try {
            $isTakenCard = $decks[$from]->isCardCanBeTaken($numberCard);
            if (!$isTakenCard) {
                throw new \Exception("This card cannot be taken!!!");
            }
            $card = $decks[$from]->takeCards($numberCard);
            $isCanPut = $decks[$to]->isCanYouPut($card);
            if (!$isCanPut) {
                throw new \Exception("You can't put it here!!!");
            }
            $decks[$from]->deletCardsInDeck($numberCard);
            $decks[$to]->addCards($card);
            $this->setLessScore(25);
            $this->setSteps();
            $typeEvent = "displayMainScreenWithDataEntry";
            if ($this->step > 11) {
                $resultSearch = $this->searchForCompletedDeck();
                if ($resultSearch !== false) {
                    $this->setMoreScore(1000);
                    $keyKing = $decks[$resultSearch]->searchKeyKingInCompletedDeck();
                    $decks[$resultSearch]->deletCardsInDeck($keyKing);
                    $kingCard = $this->deckFactory->createKingCard();
                    $this->setCompletedDecks($kingCard);
                }
            }
            if ($this->step > 100) {
                $isWin = $this->isGameOver();
                if ($isWin) {
                    $typeEvent = "displayWinnerScreen";
                }
            }

        }catch (\Exception $message) {
            $message = $message->getMessage();
            $typeEvent = "displayMainScreenMessage";
            $keyForData = "message";
            $displayEvent->setData($message, $keyForData);
        }
        $keyForData = "gameData";
        $gameData = $this->getGameData();
        $displayEvent->setData($gameData, $keyForData);
        $displayEvent->setType($typeEvent);
        $eventBus->push($displayEvent);
    }

    public function cancelMove(Event $event, EventBus $eventBus): void
    {
//        $from = $canceledMoves["from"];
//        $cards = $canceledMoves["card"];
//        $to = $canceledMoves["to"];
//        $numberPenultimateCard = $canceledMoves["NumberPenultimateCard"];
//        $state = $canceledMoves["state"];
//
//        if ($numberPenultimateCard > 0) {
//            $decks[$from]->setStateReversed($numberPenultimateCard, $state);
//        }
//        $whereToDelete = $decks[$to]->countingCard() - count($cards);
//        $decks[$to]->deletCardsInDeck($whereToDelete);
//        $decks[$from]->addCards($cards);
        echo "tut";
        sleep(100);
    }

    private function isGameOver(): bool
    {
        $numberOfEmptyDecks = null;
        foreach ($this->decks as $deck) {
            if ($deck->isDeckEmpty()) {
                $numberOfEmptyDecks++;
            }
        }
        return $numberOfEmptyDecks === count($this->decks);
    }

    private function createHint()
    {
        $hint = [];
        $cards = [];
        $j = 0;

        $searchCardCanBeTaken = function (Deck $deck) {
            for ($i = 0; $i < $deck->countingCard(); $i++) {
                if ($deck->isCardCanBeTaken($i)) {
                    return $deck->getCard($i);
                }
            }
            return false;
        };

        for ($i = 0; $i < count($this->decks); $i++) {
            $deck = $this->decks[$j];
            if ($i !== $j) {
                $card = $searchCardCanBeTaken($deck);
                if ($searchCardCanBeTaken($deck) !== false) {
                    $cards[] = $card;
                    if ($this->decks[$i]->isCanYouPut($cards)) {
                        $typeCard = $card->getType();
                        $hint["from"] = $j + 1;
                        $hint["type"] = $typeCard;
                        $hint["to"] = $i + 1;
                        return $hint;
                    }
                    $cards = [];
                }
            }
            if ($i === count($this->decks) - 1) {
                $i = -1;
                $j++;
            }
            if ($j === count($this->decks)) {
                return false;
            }
        }
    }

    public function giveHint(Event $logicEvent, EventBus $eventBus): void
    {
        $hint = $this->createHint();

        if ($hint !== false) {
            $from = $hint[0];
            $numberCard = $hint[1];
            $to = $hint[2];

            $hintMessage = "Deck $from card $numberCard to $to";
            $typeEvent = "displayMainScreenMessage";
            $displayEvent = $this->createEvent($typeEvent);
            $gameData = $this->getGameData();
            $keyByData = "gameData";
            $this->addDataToEvent($displayEvent, $gameData, $keyByData);
        }
    }

    public function createDecks(): void
    {
        $this->mainDeck = $this->deckFactory->createMainDeck();
        $numberOfCards = 6;
        for ($i = 1; $i <= 10; $i++) {
            if ($i > 4) {
                $numberOfCards = 5;
            }
            $decks[] = $this->deckFactory->createDeck($this->mainDeck->giveCards($numberOfCards));
        }
        $this->decks = $decks;
    }

    public function createInitialStateOfGame(Event $logicEvent, EventBus $eventBus): void
    {
        $this->createDecks();
        $gameData = $this->getGameData();
        $eventBus->clearEvents();
        $typeEvent = "displayMainScreenWithDataEntry";
        $keyByData = "gameData";
        $logicEvent = $this->createEvent();
        $logicEvent->setType($typeEvent);
        $logicEvent->setData($gameData, $keyByData);
        $eventBus->push($logicEvent);
    }

    function dealCards(Event $logicEvent, EventBus $eventBus): void
    {
        $takeCardForMainDeck = 1;
        $numberOfDeck = 10;
        $numberOfCardsInMainDeck = $this->mainDeck->countingCard();
        $gameData = $this->getGameData();
        $eventBus->clearEvents();
        $logicEvent = $this->createEvent();
        $keyForData = "gameData";
        $logicEvent->setData($gameData, $keyForData);


        if ($numberOfCardsInMainDeck !== 0) {
            for ($i = 0; $i < $numberOfDeck; $i++) {
                $card = $this->mainDeck->giveCards($takeCardForMainDeck);
                $this->decks[$i]->addCards($card);
            }
            $typeEvent = "displayMainScreenWithDataEntry";
        } else {
            $typeEvent = "displayMainScreenMessage";
            $message = "Main deck empty";
            $keyForData = "message";
            $logicEvent->setData($message, $keyForData);
        }
        $logicEvent->setType($typeEvent);
        $eventBus->push($logicEvent);
    }

    private function searchForCompletedDeck(): int|bool
    {
        foreach ($this->decks as $index => $deck) {
            $isDeckCompiled = $deck->thisDeckCompleted();
            if ($isDeckCompiled) {
                return $index;
            }
        }
        return false;
    }

    public function giveGameDataToDisplayInCaseOfError(Event $logicEvent, EventBus $eventBus): void
    {
        $data = $logicEvent->getData();
        $message = $data['message'];
        $gameData = $this->getGameData();
        $keyForData = "gameData";
        $typeEvent = "displayMainScreenMessage";
        $eventBus->clearEvents();
        $displayEvent =  $this->createEvent();
        $displayEvent->setType($typeEvent);
        $displayEvent->setData($gameData, $keyForData);
        $displayEvent->setData($message, "message");
        $eventBus->push($displayEvent);
    }

    private function getSteps(): int
    {
        return $this->step;
    }

    private function setSteps(): void
    {
        $this->step++;
    }

    private function getScore(): int
    {
        return $this->score;
    }

    private function setLessScore(int $scoreReceived): void
    {
        $this->score -= $scoreReceived;
    }

    private function setMoreScore(int $scoreReceived): void
    {
        $this->score += $scoreReceived;
    }

    private function getDecks(): array
    {
        return $this->decks;
    }

    private function getMainDeck(): Deck
    {
        return $this->mainDeck;
    }

    private function getCompletedDecks(): array
    {
        return $this->completedDecks;
    }

    private function setCompletedDecks(Card $card): void
    {
        $this->completedDecks[] = $card;
    }

    private function getGameData(): array
    {
        $gameData = [];
        $gameData["score"] = $this->getScore();
        $gameData["steps"] = $this->getSteps();
        $gameData["decks"] = $this->getDecks();
        $gameData["mainDeck"] = $this->getMainDeck();
        $gameData["completedDecks"] = $this->getCompletedDecks();

        return  $gameData;
    }

    public function processingPlayerInput(Event $logicEvent, EventBus $eventBus): void
    {
        $typeEvent = "giveGameDataToDisplayInCaseOfError";
        $data = $logicEvent->getData();
        $inputPlayer = $data["acceptedData"];
        $eventBus->clearEvents();
        $logicEvent = $this->createEvent();
        $gameData = $this->getGameData();

        if (count($inputPlayer) === 1 && !is_numeric($inputPlayer[0])) {
            $command = $inputPlayer[0];
            if ($command === self::DISTRIBUTE) {
                $typeEvent = "dealCards";
            }
            if ($command === self::CANCEL_MOVE) {
                $typeEvent = "cancelMove";
            }
            if ($command === self::SUGGEST) {
                $typeEvent = "giveHint";
            }
            if ($command !== self::SUGGEST && $command !== self::DISTRIBUTE && $command !== self::CANCEL_MOVE) {
                $typeEvent = "giveGameDataToDisplayInCaseOfError";
                $logicEvent->setData("error" , "message");
            }
        }
        if (count($inputPlayer) === 3) {
            $from = $inputPlayer[0];
            $numberCard = $inputPlayer[1];
            $to = $inputPlayer[2];

            if ($from > 10 || $to > 10) {
                $message = "";
            }

            if ($from <0 && $numberCard < 0 && $to < 0) {
                $message = "";
            }
            if ($from <= 10 && $from > 0 && $numberCard > 0 && $to <= 10 && $to > 0) {
                $typeEvent = "updateGameState";
                $logicEvent->setData($inputPlayer, "moves");
            }
        }
        if (count($inputPlayer) > 3) {
            $message = "Incorrect input";
            $logicEvent->setData($message, "message");
        }
        if (count($inputPlayer) === 2) {
            $message = "Incorrect input";
            $logicEvent->setData($message, "message");
        }
        if (count($inputPlayer) === 1 && is_numeric($inputPlayer[0])) {
            $message = "Incorrect input";
            $logicEvent->setData($message, "message");
        }
        if (count($inputPlayer) === 0) {
            $message = "Incorrect input";
            $logicEvent->setData($message, "message");
        }
        $logicEvent->setType($typeEvent);
        $eventBus->push($logicEvent);
    }



}