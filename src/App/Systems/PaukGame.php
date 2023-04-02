<?php

declare(strict_types=1);

namespace App\Systems;

use App\DeckFactory;
use App\Events\CreateScreenWithGameRulesEvent;
use App\Events\DisplayMainScreenWithDataEntryEvent;
use App\Events\InputEvent;
use App\GameObject\Card;
use App\GameObject\Deck;

class PaukGame extends Game
{
    private bool $initialized = false;
    private DeckFactory $deckFactory;
    private Deck $mainDeck;
    private int $score = 1000;
    private int $step = 0;
    /**
     * @var Deck[]
     */
    private array $decks;
    /**
     * @var Card[]
     */
    private array $completedDecks = [];

    public function __construct(DeckFactory $deckFactory)
    {
        $this->deckFactory = $deckFactory;
    }

    public function getSubscriptions(): array
    {
        return [
            ...parent::getSubscriptions(),
            InputEvent::class => function () {
            },
        ];
    }

    protected function update(): void
    {
        if (!$this->initialized) {
            $this->initialize();
            $this->initialized = true;
        }
    }

    private function initialize(): void
    {
        $this->createDecks();
        $gameData = $this->getGameData();
        $event = new DisplayMainScreenWithDataEntryEvent();
        $event->setGameData($gameData);
        $this->eventPusher->push(
            new CreateScreenWithGameRulesEvent(),
            $event
        );
    }

    private function createDecks(): void
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

    private function getGameData(): array
    {
        $gameData = [];
        $gameData["score"] = $this->score;
        $gameData["steps"] = $this->step;
        $gameData["decks"] = $this->decks;
        $gameData["mainDeck"] = $this->mainDeck;
        $gameData["completedDecks"] = $this->completedDecks;

        return  $gameData;
    }
}
