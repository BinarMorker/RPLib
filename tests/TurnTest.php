<?php

use PHPUnit\Framework\TestCase;
use RPLib\Entities\Attribute;
use RPLib\Entities\Relations\AttributeReference;
use RPLib\Entities\Relations\StatisticReference;
use RPLib\Entities\Turn;
use RPLib\Entities\Statistic;
use RPLib\Enums\TurnStatus;

/**
 * @covers Turn
 */
final class TurnTest extends TestCase {

    public function testTurnCanHaveRegisteredAttribute() {
        $reference = new AttributeReference();
        $reference->setName("Test");
        $attribute = new Attribute();
        $attribute->setName("Test");
        $attribute->setReference($reference);
        $turn = new Turn();
        $turn->setAttribute($attribute);

        $this->assertTrue($turn->hasAttribute($attribute->getName()));
    }

    public function testTurnCanHaveRegisteredStatistic() {
        $reference = new StatisticReference();
        $reference->setName("Test");
        $statistic = new Statistic();
        $statistic->setName("Test");
        $statistic->setReference($reference);
        $turn = new Turn();
        $turn->setStatistic($statistic);

        $this->assertTrue($turn->hasStatistic($statistic->getName()));
    }

    public function testTurnCannotHaveDuplicateAttribute() {
        $this->expectException(UnexpectedValueException::class);

        $reference = new AttributeReference();
        $reference->setName("Test");
        $attribute = new Attribute();
        $attribute->setName("Test");
        $attribute->setReference($reference);
        $turn = new Turn();
        $turn->setAttribute($attribute);
        $turn->setAttribute($attribute);
    }

    public function testTurnCannotHaveDuplicateStatistic() {
        $this->expectException(UnexpectedValueException::class);

        $reference = new StatisticReference();
        $reference->setName("Test");
        $statistic = new Statistic();
        $statistic->setName("Test");
        $statistic->setReference($reference);
        $turn = new Turn;
        $turn->setStatistic($statistic);
        $turn->setStatistic($statistic);
    }

    public function testTurnCannotHaveUnregisteredAttribute() {
        $this->expectException(OutOfRangeException::class);

        $reference = new AttributeReference();
        $reference->setName("TestNew");
        $attribute = new Attribute();
        $attribute->setName("Test");
        $attribute->setReference($reference);
        $turn = new Turn();
        $turn->setAttribute($attribute);
    }

    public function testTurnCannotHaveUnregisteredStatistic() {
        $this->expectException(OutOfRangeException::class);

        $reference = new StatisticReference();
        $reference->setName("TestNew");
        $statistic = new Statistic();
        $statistic->setName("Test");
        $statistic->setReference($reference);
        $turn = new Turn();
        $turn->setStatistic($statistic);
    }

    public function testTurnStatusCyclesCorrectly() {
        $turn = new Turn();
        $this->assertEquals(TurnStatus::NOT_STARTED, $turn->getStatus());

        $turn->begin();
        $this->assertEquals(TurnStatus::ONGOING, $turn->getStatus());

        $turn->begin();
        $this->assertEquals(TurnStatus::ONGOING, $turn->getStatus());

        $turn->pause();
        $this->assertEquals(TurnStatus::PAUSED, $turn->getStatus());

        $turn->pause();
        $this->assertEquals(TurnStatus::PAUSED, $turn->getStatus());

        $turn->begin();
        $this->assertEquals(TurnStatus::ONGOING, $turn->getStatus());

        $turn->finish();
        $this->assertEquals(TurnStatus::FINISHED, $turn->getStatus());

        $turn->begin();
        $this->assertEquals(TurnStatus::FINISHED_THEN_RETURNED, $turn->getStatus());

        $turn->skip();
        $this->assertEquals(TurnStatus::RETURNED_THEN_SKIPPED, $turn->getStatus());
    }

}