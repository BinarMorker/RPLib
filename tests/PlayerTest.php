<?php

use PHPUnit\Framework\TestCase;
use RPLib\Entities\Attribute;
use RPLib\Entities\Player;
use RPLib\Entities\Relations\AttributeReference;
use RPLib\Entities\Relations\StatisticReference;
use RPLib\Entities\Statistic;

/**
 * @covers Player
 */
final class PlayerTest extends TestCase {

    public function testPlayerCanHaveRegisteredAttribute() {
        $reference = new AttributeReference();
        $reference->setName("Test");
        $attribute = new Attribute();
        $attribute->setName("Test");
        $attribute->setReference($reference);
        $player = new Player();
        $player->setAttribute($attribute);

        $this->assertTrue($player->hasAttribute($attribute->getName()));
    }

    public function testPlayerCanHaveRegisteredStatistic() {
        $reference = new StatisticReference();
        $reference->setName("Test");
        $statistic = new Statistic();
        $statistic->setName("Test");
        $statistic->setReference($reference);
        $player = new Player();
        $player->setStatistic($statistic);

        $this->assertTrue($player->hasStatistic($statistic->getName()));
    }

    public function testPlayerCannotHaveDuplicateAttribute() {
        $this->expectException(UnexpectedValueException::class);

        $reference = new AttributeReference();
        $reference->setName("Test");
        $attribute = new Attribute();
        $attribute->setName("Test");
        $attribute->setReference($reference);
        $player = new Player();
        $player->setAttribute($attribute);
        $player->setAttribute($attribute);
    }

    public function testPlayerCannotHaveDuplicateStatistic() {
        $this->expectException(UnexpectedValueException::class);

        $reference = new StatisticReference();
        $reference->setName("Test");
        $statistic = new Statistic();
        $statistic->setName("Test");
        $statistic->setReference($reference);
        $player = new Player();
        $player->setStatistic($statistic);
        $player->setStatistic($statistic);
    }

    public function testPlayerCannotHaveUnregisteredAttribute() {
        $this->expectException(OutOfRangeException::class);

        $reference = new AttributeReference();
        $reference->setName("TestNew");
        $attribute = new Attribute();
        $attribute->setName("Test");
        $attribute->setReference($reference);
        $player = new Player();
        $player->setAttribute($attribute);
    }

    public function testPlayerCannotHaveUnregisteredStatistic() {
        $this->expectException(OutOfRangeException::class);

        $reference = new StatisticReference();
        $reference->setName("TestNew");
        $statistic = new Statistic();
        $statistic->setName("Test");
        $statistic->setReference($reference);
        $player = new Player();
        $player->setStatistic($statistic);
    }

}