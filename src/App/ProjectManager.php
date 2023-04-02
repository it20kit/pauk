<?php

declare(strict_types=1);

namespace App;

use App\GameObject\Deck;

class ProjectManager
{
    public function getFullFaceUpCard(string $type): string
    {
        $picture = "";
        for ($i = 1; $i <= 70; $i++) {
            if ($i >= 2 && $i < 10) {
                $picture .= "_";
                continue;
            }

            if ($i % 10 === 1 && $i !== 1) {
                $picture .= "|";
                continue;
            }

            if ($i === 10) {
                $picture .= "\n";
                continue;
            }

            if ($type !== "10") {
                if ($i === 13 || $i == 58) {
                    $picture .= $type;
                    continue;
                }
            }
            if (($i % 10 === 0) && ($i !== 10)) {
                $picture .= "|\n";
                continue;
            }

            if ($i > 60 && $i < 70) {
                $picture .= "_";
                continue;
            }
            $picture .= " ";
        }

        return $picture;
    }

    public function toStringCardReversed(): string
    {
        $picture = "";
        for ($i = 1; $i <= 70; $i++) {
            if ($i === 1) {
                $picture .= " ";
                continue;
            }
            if (($i > 1 && $i < 10) || ($i > 61 && $i < 70)) {
                $picture .= "_";
                continue;
            }
            if ($i === 10) {
                $picture .= "\n";
                continue;
            }
            if (($i % 10 === 1) && ($i !== 1)) {
                $picture .= "|";
                continue;
            }
            if (($i % 10 === 0) && ($i !== 10)) {
                $picture .= "|\n";
                continue;
            }
            $picture .= "*";
        }
        return $picture;
    }

    private function getHalfVisibleFaceDownCard(): string
    {
        $picture = "";
        for ($i = 1; $i <= 20; $i++) {
            if (($i % 10 === 1) && ($i !== 1)) {
                $picture .= "|";
                continue;
            }
            if (($i % 10 === 0) && ($i !== 10)) {
                $picture .= "|\n";
                continue;
            }
            if ($i === 10) {
                $picture .= "\n";
                continue;
            }
            if ($i > 1 && $i < 10) {
                $picture .= "_";
                continue;
            }
            if ($i === 1) {
                $picture .= " ";
            }
            if ($i !== 1) {
                $picture .= "*";
            }
        }
        return $picture;
    }

    private function getHalfVisibleFaceUpCard(string $type): string
    {
        $picture = "";
        for ($i = 1; $i <= 20; $i++) {
            if (($i % 10 === 1) && $i !== 1) {
                $picture .= "|";
                continue;
            }
            if (($i % 10 === 0) && ($i !== 10)) {
                $picture .= "|\n";
                continue;
            }
            if ($i === 10) {
                $picture .= "\n";
                continue;
            }
            if ($i === 13) {
                $picture .= $type;
                continue;
            }
            if ($i > 1 && $i < 10) {
                $picture .= "_";
                continue;
            }
            $picture .= " ";
        }
        return $picture;
    }


    public function getStringDeckRepresentation(Deck $deck): string
    {
        $picture = "";
        $cards = $deck->getCards();
        $lastCardIndex = count($cards) - 1;
        foreach ($cards as $index => $card) {
            if ($card->isReversed()) {
                $picture .= $this->getHalfVisibleFaceDownCard();
            }
            if(!$card->isReversed() && $index !== $lastCardIndex) {
                $type = $card->getType();
                $picture .= $this->getHalfVisibleFaceUpCard($type);
            }
            if ($index === $lastCardIndex) {
                $type = $card->getType();
                $picture .= $this->getFullFaceUpCard($type);
            }
        }
        return $picture;
    }
}