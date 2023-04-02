<?php

namespace App\Systems;

use App\EventBus;
use App\Events\CreateScreenWithGameRulesEvent;
use App\Events\DisplayMainScreenWithDataEntryEvent;
use App\Events\Event;
use App\Painter\Painter;
use App\ProjectManager;

class GraphicSystem extends AbstractSystem
{
    private Painter $painter;

    private ProjectManager $projectManager;

    protected array $events = [
        "displayMainScreenWithDataEntry" => "createMainScreenForDataEntry",
        "displayMainScreenMessage" => "createMainScreenMessage",
        "displayWinnerScreen" => "createWinnerScreen"
    ];

    public function __construct(Painter $painter, ProjectManager $projectManager)
    {
        $this->painter = $painter;
        $this->projectManager = $projectManager;
    }

    public function createMainScreen(array $gameData): void
    {
        $firstXCoordinatesForDisplayingCardNumbersOnScreen = 2;
        $secondXCoordinatesForDisplayingCardNumbersOnScreen = 147;
        $this->addDecksInScreen($gameData);
        $this->addNumberDeckInScreen();
        $this->addNumberCardInScreen($firstXCoordinatesForDisplayingCardNumbersOnScreen);
        $this->addNumberCardInScreen($secondXCoordinatesForDisplayingCardNumbersOnScreen);
        $this->addCompletedDeckInScreen($gameData);
        $this->addMainDeckInScreen($gameData);
        $this->addCounterStepsInScreen($gameData);
        $this->addCounterScoreInScreen($gameData);
    }

    public function createScreenWithGameRules(): void
    {
        $this->painter->addPicture("START GAME", 80, 20);
        $this->painter->display();
        sleep(3);
        $this->painter->clear();
//        $eventBus->clearEvents();
//        $typeEvent = "createInitialStateOfGame";
//        $logicEvent = $this->createEvent();
//        $logicEvent->setType($typeEvent);
//        $eventBus->push($logicEvent);
    }

    public function createMainScreenForDataEntry(array $gameData): void
    {
        $xCoordinateForOutputMessage = 6;
        $yCoordinateForOutputMessage = 40;
        $this->painter->clear();
        $message = "Specify which deck, which card, and which deck to move the card to:";
        $this->painter->addPicture($message,$xCoordinateForOutputMessage,$yCoordinateForOutputMessage);
        $this->createMainScreen($gameData);
        $this->painter->display();
//        $inputEvent = $this->createEvent();
//        $typeEvent = "inputPlayer";
//        $inputEvent->setType($typeEvent);
//        $eventBus->push($inputEvent);
    }

    public function createMainScreenMessage(Event $displayEvent, EventBus $eventBus): void
    {
        $this->painter->clear();
        $this->createMainScreen($displayEvent);
        $this->createWindowMessage($displayEvent);
        $this->painter->display();
        sleep(3);
        $this->painter->clear();
        $data = $displayEvent->getData();
        $gameData = $data["gameData"];
        $eventBus->clearEvents();
        $typeEvent = "displayMainScreenWithDataEntry";
        $keyForData = "gameData";
        $displayEvent = $this->createEvent();
        $displayEvent->setType($typeEvent);
        $displayEvent->setData($gameData, $keyForData);
        $eventBus->push($displayEvent);
    }


    private function createWindowMessage(Event $displayEvent): void
    {
        $xCoordinateForAddingBorderUpToScreen = 48;
        $yCoordinateForAddingBorderUpToScreen = 35;
        $xCoordinateForAddingMessageToScreen = 51;
        $yCoordinateForAddingMessageToScreen = 40;
        $xCoordinateForAddingBorderDownToScreen = 48;
        $yCoordinateForAddingBorderDownToScreen = 45;
        $xCoordinateForAddingLateralBorderLeftToScreen = 48;
        $yCoordinateForAddingLateralBorderLeftToScreen = 35;
        $xCoordinateForAddingLateralBorderRightToScreen = 97;
        $yCoordinateForAddingLateralBorderRightToScreen = 35;
        $data = $displayEvent->getData();
        $message = $data["message"];
        $symbol = "*";
        $painter = $this->painter;

        $createUpBorderWindow = function (string $symbol): string {
            return str_repeat($symbol,50);
        };
        $createLateralBorder = function (string $symbol): string {
            $symbol .= "\n";
            return str_repeat($symbol,10);
        };

        $borderErrorUp = $createUpBorderWindow($symbol);
        $borderErrorDown = $borderErrorUp;
        $lateralBorder = $createLateralBorder($symbol);

        $painter->addPicture($borderErrorUp, $xCoordinateForAddingBorderUpToScreen,
            $yCoordinateForAddingBorderUpToScreen);
        $painter->addPicture($message, $xCoordinateForAddingMessageToScreen, $yCoordinateForAddingMessageToScreen);
        $painter->addPicture($borderErrorDown,$xCoordinateForAddingBorderDownToScreen,
            $yCoordinateForAddingBorderDownToScreen);
        $painter->addPicture($lateralBorder,$xCoordinateForAddingLateralBorderLeftToScreen,
            $yCoordinateForAddingLateralBorderLeftToScreen);
        $painter->addPicture($lateralBorder,$xCoordinateForAddingLateralBorderRightToScreen,
            $yCoordinateForAddingLateralBorderRightToScreen);
    }

    private function addMainDeckInScreen(array $gameData): void
    {
        $xCoordinateForAddingMainDeckToScreen = 150;
        $yCoordinateForAddingMainDeckToScreen = 4;
        $xCoordinateForAddingCardInDeckToScreen = 150;
        $yCoordinateForAddingCardInDeckToScreen = 3;
        $mainDeck = $gameData["mainDeck"];
        $numberOfCardInDeck = $mainDeck->countingCard();
        $numberOfDecksOfTenPieces = $numberOfCardInDeck / 10;
        $this->painter->addPicture("Card in MainDeck: $numberOfCardInDeck", $xCoordinateForAddingCardInDeckToScreen,
            $yCoordinateForAddingCardInDeckToScreen);

        for ($i = 1; $i <= $numberOfDecksOfTenPieces; $i++) {
            $this->painter->addPicture($this->projectManager->toStringCardReversed(),
                $xCoordinateForAddingMainDeckToScreen, $yCoordinateForAddingMainDeckToScreen);
            $xCoordinateForAddingMainDeckToScreen +=3;
        }
    }

    private function addCompletedDeckInScreen(array $gameData): void
    {
        $xCoordinateForAddingCompletedDecksToScreen = 150;
        $yCoordinateForAddingCompletedDecksToScreen = 40;
        $completedDeck = $gameData["completedDecks"];
        if (count($completedDeck) !== 0) {
            $numberOfCompletedDecks = count($completedDeck);
            $type = $completedDeck[0]->getType();
            for ($i = 1; $i <= $numberOfCompletedDecks; $i++) {
                $this->painter->addPicture($this->projectManager->getFullFaceUpCard($type),
                    $xCoordinateForAddingCompletedDecksToScreen,$yCoordinateForAddingCompletedDecksToScreen);
                $xCoordinateForAddingCompletedDecksToScreen+= 3;
            }
        }
    }

    public function createWinnerScreen(Event $displayEvent, EventBus $eventBus): void
    {

        $this->painter->clear();
        $xCoordinateForAddingFirstMessageToScreen = 69;
        $yCoordinateForAddingFirsMessageToScreen = 38;
        $xCoordinateForAddingSecondMessageToScreen = 70;
        $yCoordinateForAddingSecondMessageToScreen = 36;
        $xCoordinateForAddingPictureToScreen = 60;
        $yCoordinateForAddingPictureToScreen = 40;
        $data = $displayEvent->getData();
        $score = $data["gameData"]["score"];
        $step = $data["gameData"]["steps"];
        $eventBus->clearEvents();
        $sample = "
  \                 /     0         |\    |
   \      /\       /      |         | \   |
    \    /  \     /       |         |  \  |
     \  /    \   /        |         |   \ |
      \/      \ /         |         |    \|
    ";
        $this->addNumberDeckInScreen();
        $this->addWinningDecksInScreen();
        $this->painter->addPicture($sample, $xCoordinateForAddingPictureToScreen, $yCoordinateForAddingPictureToScreen);
        $this->painter->addPicture("You have scored $score points!!!", $xCoordinateForAddingFirstMessageToScreen,
        $yCoordinateForAddingFirsMessageToScreen);
        $this->painter->addPicture("You have taken $step steps!!!", $xCoordinateForAddingSecondMessageToScreen,
        $yCoordinateForAddingSecondMessageToScreen);
        $this->painter->display();
    }

    private function addWinningDecksInScreen(): void
    {
        $sample = "
 ________ 
| K      |
 ________
| D      |
 ________
| B      |
 ________ 
| t      |
 ________
| 9      |
 ________ 
| 8      |
 ________
| 7      |
 ________
| 6      |
 ________ 
| 5      |
 _________
| 4      |
 ________
| 3      |
 ________
| 2      |
 ________
| T      |
|        |
|        |
|      T |
|________|

        ";
        $xCoordinateForAddingSampleToScreen = 8;
        $yCoordinateForAddingSampleToScreen = 3;
        $numberOfSample = 10;

        for ($i = 1; $i <= $numberOfSample; $i++) {
            $this->painter->addPicture($sample, $xCoordinateForAddingSampleToScreen, $yCoordinateForAddingSampleToScreen);
            $xCoordinateForAddingSampleToScreen += 14;
        }
    }


    private function addNumberDeckInScreen(): void
    {
        $xCoordinateForAddingNumberDecksToScreen = 12;
        $yCoordinateForAddingNumberDecksToScreen = 2;
        for ($i =  1; $i <= 10; $i++) {
            $i = (string)$i;
            $this->painter->addPicture($i, $xCoordinateForAddingNumberDecksToScreen,
                $yCoordinateForAddingNumberDecksToScreen);
            $xCoordinateForAddingNumberDecksToScreen += 14;
            $i = (int)$i;
        }
    }

    private function addNumberCardInScreen(int $xCoordinateForAddingNumberCardsToScreen): void
    {
        $yCoordinateForAddingNumberCardsToScreen = 4;
        for ($i = 1; $i <= 20; $i++) {
            $i = (string)$i;
            $this->painter->addPicture($i, $xCoordinateForAddingNumberCardsToScreen,
                $yCoordinateForAddingNumberCardsToScreen);
            $yCoordinateForAddingNumberCardsToScreen += 2;
            $i = (int)$i;
        }
    }

    private function addDecksInScreen($gameData): void
    {
        $decks = $gameData["decks"];
        $xCoordinateForAddingDecksToScreen = 8;
        $yCoordinateForAddingDecksToScreen = 3;

        foreach ($decks as $deck) {
            $picture = $this->projectManager->getStringDeckRepresentation($deck);
            $this->painter->addPicture($picture, $xCoordinateForAddingDecksToScreen,$yCoordinateForAddingDecksToScreen);
            $xCoordinateForAddingDecksToScreen += 14;
        }
    }

    private function addCounterStepsInScreen(array $gameData): void
    {
        $xCoordinateForAddingStepsToScreen = 150;
        $yCoordinateForAddingStepsToScreen = 12;
        $steps = $gameData["steps"];
        $string = "Steps taken:$steps";
        $this->painter->addPicture($string,$xCoordinateForAddingStepsToScreen,$yCoordinateForAddingStepsToScreen);
    }

    private function addCounterScoreInScreen(array $gameData): void
    {
        $xCoordinateForAddingScoreToScreen = 150;
        $yCoordinateForAddingScoreToScreen = 14;
        $score = $gameData["score"];
        $string = "Score:$score";
        $this->painter->addPicture($string, $xCoordinateForAddingScoreToScreen, $yCoordinateForAddingScoreToScreen);
    }

    public function getSubscriptions(): array
    {
        return [
            CreateScreenWithGameRulesEvent::class => fn() => $this->createScreenWithGameRules(),
            DisplayMainScreenWithDataEntryEvent::class => function (DisplayMainScreenWithDataEntryEvent $event) {
                $this->createMainScreenForDataEntry($event->getGameData());
            },
        ];
    }
}

