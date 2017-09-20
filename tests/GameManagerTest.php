<?php

use PHPUnit\Framework\TestCase;
use RPLib\Core\GameManager;
use RPLib\Entities\Game;
use RPLib\Entities\Player;
use RPLib\Entities\Relations\AttributeReference;
use RPLib\Entities\Relations\StatisticReference;
use RPLib\Enums\ValueType;

/**
 * @covers GameManager
 */
final class GameManagerTest extends TestCase {

    public function testGameManagerCanRegisterAttributeReference() {
        $gameManager = GameManager::getInstance();
        $reference = new AttributeReference();
        $reference->setName("Test");
        $reference->setValueType(ValueType::INTEGER);

        $gameManager->getRegistry()->players->addAttribute($reference);
        $gameManager->getRegistry()->turns->addAttribute($reference);
        $gameManager->getRegistry()->characters->addAttribute($reference);

        $this->assertContains($reference, $gameManager->getRegistry()->players->getAttributes());
    }

    public function testGameManagerCanRegisterStatisticReference() {
        $gameManager = GameManager::getInstance();
        $reference = new StatisticReference();
        $reference->setName("Test");
        $reference->setValueType(ValueType::FLOAT);

        $gameManager->getRegistry()->players->addStatistic($reference);
        $gameManager->getRegistry()->turns->addStatistic($reference);
        $gameManager->getRegistry()->characters->addStatistic($reference);

        $this->assertContains($reference, $gameManager->getRegistry()->players->getStatistics());
    }

    public function testGameManagerIsSingleton() {
        $gameManager = GameManager::getInstance();
        $attribute = new AttributeReference();
        $attribute->setName("Test");
        $statistic = new StatisticReference();
        $statistic->setName("Test");

        $this->assertTrue($gameManager->getRegistry()->players->hasAttribute($attribute));
        $this->assertTrue($gameManager->getRegistry()->players->hasStatistic($statistic));
    }

    public function testGameManagerCannotHaveDuplicateAttributeReference() {
        $this->expectException(UnexpectedValueException::class);

        $gameManager = GameManager::getInstance();
        $reference = new AttributeReference();
        $reference->setName("Test");
        $gameManager->getRegistry()->players->addAttribute($reference);
    }

    public function testGameManagerCannotHaveDuplicateStatisticReference() {
        $this->expectException(UnexpectedValueException::class);

        $gameManager = GameManager::getInstance();
        $reference = new StatisticReference();
        $reference->setName("Test");
        $gameManager->getRegistry()->players->addStatistic($reference);
    }

    public function testGameManagerNextTurnWorkflow() {
        $gameManager = GameManager::getInstance();

        $player1 = new Player();
        $player1->setName("Test #1");

        $player2 = new Player();
        $player2->setName("Test #2");

        $player3 = new Player();
        $player3->setName("Test #3");

        $game = new Game();
        $game->addPlayer($player1);
        $game->addPlayer($player2);
        $game->addPlayer($player3);
        $gameManager->addGame($game);

        $this->assertEquals(3, count($game->getPlayers()));

        $turn = $game->getNextTurn();
        $turn->begin();
        $turn->finish();
        $game->addTurn($turn);

        $turn = $game->getNextTurn();
        $turn->begin();
        $turn->finish();
        $game->addTurn($turn);

        $this->assertEquals(2, count($game->getTurns()));

        $this->assertEquals("Test #2", $game->getCurrentTurn()->getPlayer()->getName());
    }

}