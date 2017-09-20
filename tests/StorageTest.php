<?php

use PHPUnit\Framework\TestCase;
use RPLib\Core\Storage;
use RPLib\Entities\Attribute;
use RPLib\Entities\Player;
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

}