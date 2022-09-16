<?php

declare(strict_types=1);

namespace Imi\Snowflake\Test;

use Imi\Snowflake\Test\Model\ArticleId;
use PHPUnit\Framework\TestCase;

class ModelGeneratorTest extends TestCase
{
    public function testModel(): void
    {
        // insert
        $record1 = ArticleId::newInstance();
        $record1->insert();
        $this->assertNotEmpty($record1->title);
        $this->assertNotEmpty($record1->content);
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        $record1->title = 'a';
        $record1->content = 'a';
        $record1->update();
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        $record1->title = 'b';
        $record1->content = 'b';
        $record1->save();
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        // save
        $record1 = ArticleId::newInstance();
        $record1->save();
        $this->assertNotEmpty($record1->title);
        $this->assertNotEmpty($record1->content);
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        $record1->title = 'a';
        $record1->content = 'a';
        $record1->update();
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());

        $record1->title = 'b';
        $record1->content = 'b';
        $record1->save();
        $record2 = ArticleId::find($record1->id);
        $this->assertEquals($record1->toArray(), $record2->toArray());
    }
}
