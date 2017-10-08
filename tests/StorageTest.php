<?php

use PHPUnit\Framework\TestCase;
use RPLib\Core\Storage;
use RPLib\Entities\Attribute;
use RPLib\Entities\Game;
use RPLib\Entities\Player;
use RPLib\Entities\Turn;
use RPLib\Entities\Relations\AttributeReference;
use RPLib\Enums\ValueType;

/**
 * @covers Storage
 */
final class StorageTest extends TestCase {

    public function testStorageCanLoadApineDatabase() {
        $storage = Storage::getInstance();
        $connection = $storage->getConnection();
        $this->assertNotNull($connection);
        $this->assertEquals("Apine\Core\Database", get_class($connection));
    }

    public function testCanSavePlayer() {
        Storage::getInstance()->volatileTransaction(function() {
            $player = new Player();
            $player->setName("Test");
            $this->assertNull($player->getId());
            $player->save();
            $this->assertNotNull($player->getId());
            $this->assertNotEquals(-1, $player->getId());
        });
    }

    public function testSavedPlayerCanBeRetreived() {
        Storage::getInstance()->volatileTransaction(function() {
            $player = new Player();
            $player->setName("Test");
            $player->save();
            $this->assertNotNull($player->getId());
            $player2 = new Player($player->getId());
            $this->assertEquals($player->getName(), $player2->getName());
        });
    }

    public function testCannotSaveAttributeWithoutReference() {
        $this->expectException(UnexpectedValueException::class);

        Storage::getInstance()->volatileTransaction(function() {
            $attribute = new Attribute();
            $attribute->setName("Test");
            $this->assertNull($attribute->getId());
            $attribute->save();
        });
    }

    public function testCanSaveAttributeWithReference() {
        Storage::getInstance()->volatileTransaction(function() {
            $reference = new AttributeReference();
            $reference->setName("Test");
            $reference->setValueType(ValueType::STRING);
            $reference->save();
            $attribute = new Attribute();
            $attribute->setName("Test");
            $attribute->setReference($reference);
            $this->assertNull($attribute->getId());
            $attribute->save();
            $this->assertNotNull($attribute->getId());
        });
    }
    
    public function testCanSaveTurn() {
        Storage::getInstance()->volatileTransaction(function () {
            $player = new Player();
            $player->setName("Test");
            $player->save();
            
            $game = new Game();
            $game->setName("Test");
            $game->addPlayer($player);
            $game->save();
            
            $turn = new Turn();
            $turn->setName("Test");
            $turn->setPlayer($player);
            $turn->setGame($game);
            $this->assertNull($turn->getId());
            $turn->save();
            $this->assertNotNull($turn->getId());
            $this->assertNotEquals(-1, $turn->getId());
        });
    }
    
    public function testCannotSaveTurnWithoutPlayer() {
        $this->expectException(PDOException::class);
        
        Storage::getInstance()->volatileTransaction(function () {
            $turn = new Turn();
            $turn->setName("Test");
            $this->assertNull($turn->getId());
            $turn->save();
        });
    }
    
    public function testCannotSaveTurnWithoutGame() {
        $this->expectException(PDOException::class);
        
        Storage::getInstance()->volatileTransaction(function () {
            $player = new Player();
            $player->setName("Test");
            $player->save();
            $turn = new Turn();
            $turn->setName("Test");
            $turn->setPlayer($player);
            $this->assertNull($turn->getId());
            $turn->save();
        });
    }

}