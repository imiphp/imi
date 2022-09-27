<?php

declare(strict_types=1);

namespace Imi\Grpc\Test;

use Google\Protobuf\Internal\GPBUtil;
use Grpc\LoginRequest;
use Grpc\TestRequest;
use Imi\Grpc\Util\ProtobufUtil;

class ProtobufUtilTest extends BaseTest
{
    public function testSetMessageData(): TestRequest
    {
        $message = new TestRequest();
        ProtobufUtil::setMessageData($message, self::DATA);
        $this->assertEquals(self::DATA['int'], $message->getInt());
        $this->assertEquals(self::DATA['string'], $message->getString());
        $this->assertEquals(self::DATA['strings'], iterator_to_array($message->getStrings()));
        $this->assertEquals(self::DATA['message']['phone'], $message->getMessage()->getPhone());
        $this->assertEquals(self::DATA['message']['password'], $message->getMessage()->getPassword());
        $this->assertEquals(self::DATA['messages'][0]['phone'], $message->getMessages()[0]->getPhone());
        $this->assertEquals(self::DATA['messages'][0]['password'], $message->getMessages()[0]->getPassword());
        $this->assertEquals(self::DATA['messages'][1]['phone'], $message->getMessages()[1]->getPhone());
        $this->assertEquals(self::DATA['messages'][1]['password'], $message->getMessages()[1]->getPassword());
        /** @var LoginRequest $loginRequest */
        $loginRequest = $message->getAny()->unpack();
        $this->assertInstanceOf(LoginRequest::class, $loginRequest);
        $this->assertEquals(self::DATA['any']['phone'], $loginRequest->getPhone());
        $this->assertEquals(self::DATA['any']['password'], $loginRequest->getPassword());
        $this->assertEquals(self::DATA['map'], iterator_to_array($message->getMap()));
        $this->assertEquals(self::DATA['map2']['a']['phone'], $message->getMap2()['a']->getPhone());
        $this->assertEquals(self::DATA['map2']['a']['password'], $message->getMap2()['a']->getPassword());
        $this->assertEquals(self::DATA['map2']['b']['phone'], $message->getMap2()['b']->getPhone());
        $this->assertEquals(self::DATA['map2']['b']['password'], $message->getMap2()['b']->getPassword());
        /** @var LoginRequest $loginRequest */
        $loginRequest = $message->getAnys()[0]->unpack();
        $this->assertInstanceOf(LoginRequest::class, $loginRequest);
        $this->assertEquals(self::DATA['anys'][0]['phone'], $loginRequest->getPhone());
        $this->assertEquals(self::DATA['anys'][0]['password'], $loginRequest->getPassword());
        $this->assertEquals(self::DATA['enum'], $message->getEnum());
        $this->assertEquals(self::DATA['bool'], $message->getBool());
        $this->assertEquals(self::DATA['timestamp'], GPBUtil::formatTimestamp($message->getTimestamp()));
        $this->assertEquals(self::DATA['duration'], GPBUtil::formatDuration($message->getDuration()) . 's');
        $structFields = $message->getStruct()->getFields();
        $this->assertTrue($structFields['null']->hasNullValue());
        $this->assertEquals(self::DATA['struct']['null'], $structFields['null']->getNullValue());
        $this->assertTrue($structFields['number']->hasNumberValue());
        $this->assertEquals(self::DATA['struct']['number'], $structFields['number']->getNumberValue());
        $this->assertTrue($structFields['string']->hasStringValue());
        $this->assertEquals(self::DATA['struct']['string'], $structFields['string']->getStringValue());
        $this->assertTrue($structFields['bool']->hasBoolValue());
        $this->assertEquals(self::DATA['struct']['bool'], $structFields['bool']->getBoolValue());
        $this->assertTrue($structFields['struct']->hasStructValue());
        $this->assertEquals(self::DATA['struct']['struct']['id'], $structFields['struct']->getStructValue()->getFields()['id']->getNumberValue());
        $this->assertEquals(self::DATA['struct']['struct']['name'], $structFields['struct']->getStructValue()->getFields()['name']->getStringValue());
        $this->assertTrue($structFields['list1']->hasListValue());
        $values = $structFields['list1']->getListValue()->getValues();
        $this->assertEquals(self::DATA['struct']['list1'][0], $values[0]->getNumberValue());
        $this->assertEquals(self::DATA['struct']['list1'][1], $values[1]->getNumberValue());
        $this->assertEquals(self::DATA['struct']['list1'][2], $values[2]->getNumberValue());
        $this->assertTrue($structFields['list2']->hasListValue());
        $values = $structFields['list2']->getListValue()->getValues();
        $fields = $values[0]->getStructValue()->getFields();
        $this->assertEquals(self::DATA['struct']['list2'][0]['id'], $fields['id']->getNumberValue());
        $this->assertEquals(self::DATA['struct']['list2'][0]['name'], $fields['name']->getStringValue());
        $this->assertEquals(self::DATA['fieldMask'], GPBUtil::formatFieldMask($message->getFieldMask()));

        return $message;
    }

    public function testSetMessageDataIgnoreUnknown(): void
    {
        $message = new TestRequest();
        $data = self::DATA;
        $data['notfound'] = 1;
        ProtobufUtil::setMessageData($message, $data, true);
        $this->expectException(\Google\Protobuf\Internal\GPBDecodeException::class);
        ProtobufUtil::setMessageData($message, $data);
    }

    /**
     * @depends testSetMessageData
     */
    public function testGetMessageValue(TestRequest $message): void
    {
        $data = self::DATA;
        $this->assertEquals($data, ProtobufUtil::GetMessageValue($message));
        $data['enum'] = 'B';
        $this->assertEquals($data, ProtobufUtil::GetMessageValue($message, [
            'enumReturnType' => 'name',
        ]));
    }
}
