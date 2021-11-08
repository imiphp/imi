<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Test\Component\Model\Article;
use Imi\Test\Component\Model\Member;
use Imi\Test\Component\Model\MemberRoleRelation;
use Imi\Test\Component\Model\MemberWithArticles;
use Imi\Test\Component\Model\MemberWithRoles;
use Imi\Test\Component\Model\Polymorphic;

class ModelRelationTest extends BaseTest
{
    public function testInitData(): array
    {
        $memberIds = [];

        $member = Member::newInstance();
        $member->username = 'relation_a';
        $member->password = 'a';
        $member->insert();
        $this->assertGreaterThan(0, $member->id);
        $memberIds[] = $member->id;

        $member = Member::newInstance();
        $member->username = 'relation_b';
        $member->password = 'b';
        $member->insert();
        $this->assertGreaterThan(0, $member->id);
        $memberIds[] = $member->id;

        return [
            'memberIds' => $memberIds,
        ];
    }

    /**
     * @depends testInitData
     */
    public function testOneToOne(array $args): array
    {
        ['memberIds' => $memberIds] = $args;

        // 插入
        $articleIds = [];
        $article1 = Article::newInstance();
        $article1->title = 'relation_title_1';
        $article1->content = 'relation_content_1';
        $article1->memberId = $memberIds[0];
        $article1->ex->data = [
            'id' => 1,
        ];
        $article1->insert();
        $this->assertGreaterThan(0, $article1->id);
        $articleIds[] = $article1->id;

        $article2 = Article::newInstance();
        $article2->title = 'relation_title_2';
        $article2->content = 'relation_content_2';
        $article2->memberId = $memberIds[1];
        $article2->ex->data = [
            'id' => 2,
        ];
        $article2->insert();
        $this->assertGreaterThan(0, $article2->id);
        $articleIds[] = $article2->id;

        $article3 = Article::newInstance();
        $article3->title = 'relation_title_2';
        $article3->content = 'relation_content_2';
        $article3->memberId = $memberIds[1];
        $article3->ex->data = [
            'id' => 2,
        ];
        $article3->insert();
        $this->assertGreaterThan(0, $article3->id);

        // 查询
        $record1 = Article::find($article1->id);
        $this->assertNotNull($record1);
        $this->assertEquals($article1->id, $record1->id);
        $this->assertEquals($article1->memberId, $record1->memberId);
        $this->assertEquals($article1->title, $record1->title);
        $this->assertEquals($article1->content, $record1->content);
        $this->assertEquals([
            'id' => 1,
        ], // @phpstan-ignore-next-line
        $record1->ex->data->toArray());

        $record2 = Article::find($article2->id);
        $this->assertNotNull($record2);
        $this->assertEquals($record2->id, $record2->id);
        $this->assertEquals($record2->memberId, $record2->memberId);
        $this->assertEquals($record2->title, $record2->title);
        $this->assertEquals($article2->content, $record2->content);
        $this->assertEquals([
            'id' => 2,
        ], // @phpstan-ignore-next-line
        $record2->ex->data->toArray());

        // 查询列表
        $list = Article::query()->whereIn('id', $articleIds)->select()->getArray();
        $this->assertEquals(Article::convertListToArray([$record1, $record2]), Article::convertListToArray($list));
        // with
        $list = Article::query()->with('ex')->whereIn('id', $articleIds)->select()->getArray();
        $this->assertEquals(Article::convertListToArray([$record1, $record2]), Article::convertListToArray($list));

        // 更新
        $article3->title .= '1';
        $article3->content .= '1';
        $article3->ex->data['id'] = 11;
        $article3->update();

        $record = Article::find($article3->id);
        $this->assertNotNull($record);
        $this->assertEquals($article3->id, $record->id);
        $this->assertEquals($article3->memberId, $record->memberId);
        $this->assertEquals($article3->title, $record->title);
        $this->assertEquals($article3->content, $record->content);
        $this->assertEquals([
            'id' => 11,
        ], // @phpstan-ignore-next-line
        $record->ex->data->toArray());

        // 删除
        $article3->delete();
        $record = Article::find($article3->id);
        $this->assertNull($record);

        $args['articleIds'] = $articleIds;

        return $args;
    }

    /**
     * @depends testOneToOne
     */
    public function testOneToMany(array $args): void
    {
        ['memberIds' => $memberIds, 'articleIds' => $articleIds] = $args;
        $member = MemberWithArticles::find($memberIds[0]);
        $this->assertNotNull($member);
        $article = Article::find($articleIds[0]);
        $this->assertNotNull($article);
        $this->assertEquals($member->articles[0]->convertToArray(), $article->convertToArray());

        // 查询列表
        /** @var MemberWithArticles[] $list1 */
        $list1 = MemberWithArticles::query()->whereIn('id', $memberIds)->select()->getArray();
        // with
        /** @var MemberWithArticles[] $list2 */
        $list2 = MemberWithArticles::query()->with('articles')->whereIn('id', $memberIds)->select()->getArray();

        /** @var Article[] $articles */
        $articles = Article::query()->whereIn('id', $articleIds)->select()->getArray();
        $this->assertEquals($articles[0]->convertToArray(), $list1[0]->articles[0]->convertToArray());
        $this->assertEquals($articles[1]->convertToArray(), $list1[1]->articles[0]->convertToArray());
        $this->assertEquals($articles[0]->convertToArray(), $list2[0]->articles[0]->convertToArray());
        $this->assertEquals($articles[1]->convertToArray(), $list2[1]->articles[0]->convertToArray());
        // 注解 with
        $this->assertEquals($articles[0]->convertToArray(), $list1[0]->articlesWith[0]->convertToArray());
        $this->assertEquals($articles[1]->convertToArray(), $list1[1]->articlesWith[0]->convertToArray());
        $this->assertEquals($articles[0]->convertToArray(), $list2[0]->articlesWith[0]->convertToArray());
        $this->assertEquals($articles[1]->convertToArray(), $list2[1]->articlesWith[0]->convertToArray());
    }

    /**
     * @depends testOneToOne
     */
    public function testManyToMany(array $args): void
    {
        ['memberIds' => $memberIds] = $args;
        $memberId = $memberIds[0];
        // 更新
        $record = MemberWithRoles::find($memberId);
        $this->assertNotNull($record);
        // @phpstan-ignore-next-line
        $record->roleRelations->append(
            MemberRoleRelation::newInstance(['role_id' => 1]),
            MemberRoleRelation::newInstance(['role_id' => 2]),
        );
        $record->save();
        $record = MemberWithRoles::find($memberIds[1]);
        $this->assertNotNull($record);
        // @phpstan-ignore-next-line
        $record->roleRelations->append(
            MemberRoleRelation::newInstance(['role_id' => 3]),
            MemberRoleRelation::newInstance(['role_id' => 4]),
            MemberRoleRelation::newInstance(['role_id' => 5]),
        );
        $record->save();

        // 查询
        $record = MemberWithRoles::find($memberId);
        $this->assertCount(2, $record->roleRelations);
        $this->assertCount(2, $record->roles);
        foreach ([1, 2] as $i => $roleId)
        {
            $relation = $record->roleRelations[$i];
            $this->assertEquals($memberId, $relation->memberId);
            $this->assertEquals($roleId, $relation->roleId);

            $role = $record->roles[$i];
            $this->assertEquals($roleId, $role->id);
        }
        // with
        $records = MemberWithRoles::query()->with('roleRelations')->whereIn('id', $memberIds)->select()->getArray();
        $this->assertCount(2, $records);
        $record = $records[0];
        $this->assertCount(2, $record->roleRelations);
        $this->assertCount(2, $record->roles);
        foreach ([1, 2] as $i => $roleId)
        {
            $relation = $record->roleRelations[$i];
            $this->assertEquals($memberIds[0], $relation->memberId);
            $this->assertEquals($roleId, $relation->roleId);

            $role = $record->roles[$i];
            $this->assertEquals($roleId, $role->id);
        }
        // 注解 with
        $this->assertCount(2, $record->roleRelationsWith);
        $this->assertCount(2, $record->rolesWith);
        foreach ([1, 2] as $i => $roleId)
        {
            $relation = $record->roleRelationsWith[$i];
            $this->assertEquals($memberIds[0], $relation->memberId);
            $this->assertEquals($roleId, $relation->roleId);

            $role = $record->rolesWith[$i];
            $this->assertEquals($roleId, $role->id);
        }

        $record = $records[1];
        $this->assertCount(3, $record->roleRelations);
        $this->assertCount(3, $record->roles);
        foreach ([3, 4, 5] as $i => $roleId)
        {
            $relation = $record->roleRelations[$i];
            $this->assertEquals($memberIds[1], $relation->memberId);
            $this->assertEquals($roleId, $relation->roleId);

            $role = $record->roles[$i];
            $this->assertEquals($roleId, $role->id);
        }
        // 注解 with
        $this->assertCount(3, $record->roleRelationsWith);
        $this->assertCount(3, $record->rolesWith);
        foreach ([3, 4, 5] as $i => $roleId)
        {
            $relation = $record->roleRelationsWith[$i];
            $this->assertEquals($memberIds[1], $relation->memberId);
            $this->assertEquals($roleId, $relation->roleId);

            $role = $record->rolesWith[$i];
            $this->assertEquals($roleId, $role->id);
        }
    }

    /**
     * @depends testOneToOne
     */
    public function testPolymorphicToOne(array $args): void
    {
        ['memberIds' => $memberIds, 'articleIds' => $articleIds] = $args;
        $record1 = Polymorphic::newInstance();
        $record1->type = 1;
        $record1->toOne = $memberIds[0];
        $record1->insert();
        $record2 = Polymorphic::newInstance();
        $record2->type = 2;
        $record2->toOne = $articleIds[0];
        $record2->insert();
        $record3 = Polymorphic::newInstance();
        $record3->type = 1;
        $record3->toOne = $memberIds[1];
        $record3->insert();
        $record4 = Polymorphic::newInstance();
        $record4->type = 2;
        $record4->toOne = $articleIds[1];
        $record4->insert();

        // 查询
        $record = Polymorphic::find($record1->id);
        $this->assertNotNull($record);
        $this->assertInstanceOf(Member::class, $record->getToOneResult());

        $record = Polymorphic::find($record2->id);
        $this->assertNotNull($record);
        $this->assertInstanceOf(Article::class, $record->getToOneResult());

        $list = Polymorphic::query()->whereIn('id', [$record1->id, $record2->id, $record3->id, $record4->id])->select()->getArray();
        $this->assertCount(4, $list);
        $this->assertInstanceOf(Member::class, $r = $list[0]->getToOneResult());
        $this->assertEquals($memberIds[0], $r->id);
        $this->assertInstanceOf(Article::class, $r = $list[1]->getToOneResult());
        $this->assertEquals($articleIds[0], $r->id);
        $this->assertInstanceOf(Member::class, $r = $list[2]->getToOneResult());
        $this->assertEquals($memberIds[1], $r->id);
        $this->assertInstanceOf(Article::class, $r = $list[3]->getToOneResult());
        $this->assertEquals($articleIds[1], $r->id);

        // with
        $list = Polymorphic::query()->with('toOneResult')->whereIn('id', [$record1->id, $record2->id, $record3->id, $record4->id])->select()->getArray();
        $this->assertCount(4, $list);
        $this->assertInstanceOf(Member::class, $r = $list[0]->getToOneResult());
        $this->assertEquals($memberIds[0], $r->id);
        $this->assertInstanceOf(Article::class, $r = $list[1]->getToOneResult());
        $this->assertEquals($articleIds[0], $r->id);
        $this->assertInstanceOf(Member::class, $r = $list[2]->getToOneResult());
        $this->assertEquals($memberIds[1], $r->id);
        $this->assertInstanceOf(Article::class, $r = $list[3]->getToOneResult());
        $this->assertEquals($articleIds[1], $r->id);
    }

    /**
     * @depends testOneToOne
     */
    public function testPolymorphicOneToOne(array $args): void
    {
        ['memberIds' => $memberIds, 'articleIds' => $articleIds] = $args;
        $article1 = Article::newInstance();
        $article1->memberId = 998;
        $article1->title = 'PolymorphicOneToOne';
        $article1->content = '';
        $article1->insert();
        $article2 = Article::newInstance();
        $article2->memberId = 999;
        $article2->title = 'PolymorphicOneToOne';
        $article2->content = '';
        $article2->insert();

        $record1 = Polymorphic::newInstance();
        $record1->type = 3;
        $record1->oneToOne = 998;
        $record1->insert();
        $record2 = Polymorphic::newInstance();
        $record2->type = 3;
        $record2->oneToOne = 999;
        $record2->insert();
        $record3 = Polymorphic::newInstance();
        $record3->type = 3;
        $record3->oneToOne = 1000;
        $record3->insert();

        // 查询
        $record = Polymorphic::find($record1->id);
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getOneToOneResult());
        $this->assertEquals(998, $record->oneToOne);
        $this->assertEquals(998, $r->memberId);
        $this->assertEquals($article1->id, $r->id);
        $this->assertEquals('PolymorphicOneToOne', $r->title);
        $record = Polymorphic::find($record3->id);
        $this->assertNotNull($record);
        $this->assertNull($record->getOneToOneResult());

        // 查询列表
        $list = Polymorphic::query()->whereIn('id', [$record1->id, $record2->id, $record3->id])->select()->getArray();
        $this->assertCount(3, $list);
        $record = $list[0];
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getOneToOneResult());
        $this->assertEquals(998, $record->oneToOne);
        $this->assertEquals(998, $r->memberId);
        $this->assertEquals($article1->id, $r->id);
        $this->assertEquals('PolymorphicOneToOne', $r->title);
        $record = $list[1];
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getOneToOneResult());
        $this->assertEquals(999, $record->oneToOne);
        $this->assertEquals(999, $r->memberId);
        $this->assertEquals($article2->id, $r->id);
        $this->assertEquals('PolymorphicOneToOne', $r->title);
        $record = $list[2];
        $this->assertNull($record->getOneToOneResult());

        // with
        $list = Polymorphic::query()->with('oneToOneResult')->whereIn('id', [$record1->id, $record2->id, $record3->id])->select()->getArray();
        $this->assertCount(3, $list);
        $record = $list[0];
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getOneToOneResult());
        $this->assertEquals(998, $record->oneToOne);
        $this->assertEquals(998, $r->memberId);
        $this->assertEquals($article1->id, $r->id);
        $this->assertEquals('PolymorphicOneToOne', $r->title);
        $record = $list[1];
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getOneToOneResult());
        $this->assertEquals(999, $record->oneToOne);
        $this->assertEquals(999, $r->memberId);
        $this->assertEquals($article2->id, $r->id);
        $this->assertEquals('PolymorphicOneToOne', $r->title);
        $record = $list[2];
        $this->assertNull($record->getOneToOneResult());
    }

    /**
     * @depends testOneToOne
     */
    public function testPolymorphicOneToMany(array $args): void
    {
        ['memberIds' => $memberIds, 'articleIds' => $articleIds] = $args;
        $article1 = Article::newInstance();
        $article1->memberId = 998;
        $article1->title = 'PolymorphicOneToMany';
        $article1->content = '';
        $article1->insert();
        $article2 = Article::newInstance();
        $article2->memberId = 999;
        $article2->title = 'PolymorphicOneToMany';
        $article2->content = '';
        $article2->insert();

        $record1 = Polymorphic::newInstance();
        $record1->type = 4;
        $record1->oneToMany = 998;
        $record1->insert();
        $record2 = Polymorphic::newInstance();
        $record2->type = 4;
        $record2->oneToMany = 999;
        $record2->insert();
        $record3 = Polymorphic::newInstance();
        $record3->type = 4;
        $record3->oneToMany = 1000;
        $record3->insert();

        // 查询
        $record = Polymorphic::find($record1->id);
        $this->assertNotNull($record);
        $this->assertCount(1, $r = $record->getOneToManyResult());
        $this->assertEquals(998, $record->oneToMany);
        $this->assertEquals(998, $r[0]->memberId);
        $this->assertEquals($article1->id, $r[0]->id);
        $this->assertEquals('PolymorphicOneToMany', $r[0]->title);
        $record = Polymorphic::find($record3->id);
        $this->assertNotNull($record);
        $this->assertCount(0, $record->getOneToManyResult());

        // 查询列表
        $list = Polymorphic::query()->whereIn('id', [$record1->id, $record2->id, $record3->id])->select()->getArray();
        $this->assertCount(3, $list);
        $record = $list[0];
        $this->assertNotNull($record);
        $this->assertCount(1, $r = $record->getOneToManyResult());
        $this->assertEquals(998, $record->oneToMany);
        $this->assertEquals(998, $r[0]->memberId);
        $this->assertEquals($article1->id, $r[0]->id);
        $this->assertEquals('PolymorphicOneToMany', $r[0]->title);
        $record = $list[1];
        $this->assertNotNull($record);
        $this->assertCount(1, $r = $record->getOneToManyResult());
        $this->assertEquals(999, $record->oneToMany);
        $this->assertEquals(999, $r[0]->memberId);
        $this->assertEquals($article2->id, $r[0]->id);
        $this->assertEquals('PolymorphicOneToMany', $r[0]->title);
        $record = $list[2];
        $this->assertCount(0, $record->getOneToManyResult());

        // with
        $list = Polymorphic::query()->with('oneToManyResult')->whereIn('id', [$record1->id, $record2->id, $record3->id])->select()->getArray();
        $this->assertCount(3, $list);
        $record = $list[0];
        $this->assertNotNull($record);
        $this->assertCount(1, $r = $record->getOneToManyResult());
        $this->assertEquals(998, $record->oneToMany);
        $this->assertEquals(998, $r[0]->memberId);
        $this->assertEquals($article1->id, $r[0]->id);
        $this->assertEquals('PolymorphicOneToMany', $r[0]->title);
        $record = $list[1];
        $this->assertNotNull($record);
        $this->assertCount(1, $r = $record->getOneToManyResult());
        $this->assertEquals(999, $record->oneToMany);
        $this->assertEquals(999, $r[0]->memberId);
        $this->assertEquals($article2->id, $r[0]->id);
        $this->assertEquals('PolymorphicOneToMany', $r[0]->title);
        $record = $list[2];
        $this->assertCount(0, $record->getOneToManyResult());
    }

    public function testPolymorphicManyToMany(): void
    {
        $record = MemberRoleRelation::newInstance(['type' => 1, 'member_id' => 1000, 'role_id' => 1]);
        $record->save();
        $record = MemberRoleRelation::newInstance(['type' => 1, 'member_id' => 1000, 'role_id' => 2]);
        $record->save();
        $record = MemberRoleRelation::newInstance(['type' => 1, 'member_id' => 1001, 'role_id' => 3]);
        $record->save();
        $record = MemberRoleRelation::newInstance(['type' => 1, 'member_id' => 1001, 'role_id' => 4]);
        $record->save();

        $record1 = Polymorphic::newInstance();
        $record1->type = 5;
        $record1->manyToMany = 1000;
        $record1->insert();
        $record2 = Polymorphic::newInstance();
        $record2->type = 5;
        $record2->manyToMany = 1001;
        $record2->insert();

        // 查询
        $record = Polymorphic::find($record1->id);
        $this->assertNotNull($record);
        $this->assertNotNull($record->getManyToManyResult());
        $this->assertNotNull($r = $record->getManyToManyResultList());
        $this->assertCount(2, $r);
        $this->assertEquals(1, $r[0]->id);
        $this->assertEquals(2, $r[1]->id);

        // 查询列表
        $list = Polymorphic::query()->whereIn('id', [$record1->id, $record2->id])->select()->getArray();
        $this->assertCount(2, $list);

        $record = $list[0];
        $this->assertNotNull($record);
        $this->assertNotNull($record->getManyToManyResult());
        $this->assertNotNull($r = $record->getManyToManyResultList());
        $this->assertCount(2, $r);
        $this->assertEquals(1, $r[0]->id);
        $this->assertEquals(2, $r[1]->id);

        $record = $list[1];
        $this->assertNotNull($record);
        $this->assertNotNull($record->getManyToManyResult());
        $this->assertNotNull($r = $record->getManyToManyResultList());
        $this->assertCount(2, $r);
        $this->assertEquals(3, $r[0]->id);
        $this->assertEquals(4, $r[1]->id);

        // with
        $list = Polymorphic::query()->with('manyToManyResult')->whereIn('id', [$record1->id, $record2->id])->select()->getArray();
        $this->assertCount(2, $list);

        $record = $list[0];
        $this->assertNotNull($record);
        $this->assertNotNull($record->getManyToManyResult());
        $this->assertNotNull($r = $record->getManyToManyResultList());
        $this->assertCount(2, $r);
        $this->assertEquals(1, $r[0]->id);
        $this->assertEquals(2, $r[1]->id);

        $record = $list[1];
        $this->assertNotNull($record);
        $this->assertNotNull($record->getManyToManyResult());
        $this->assertNotNull($r = $record->getManyToManyResultList());
        $this->assertCount(2, $r);
        $this->assertEquals(3, $r[0]->id);
        $this->assertEquals(4, $r[1]->id);
    }

    public function testPolymorphicToMany(): void
    {
        $record = MemberRoleRelation::newInstance(['type' => 6, 'member_id' => 2000, 'role_id' => 1]);
        $record->save();
        $record = MemberRoleRelation::newInstance(['type' => 6, 'member_id' => 2000, 'role_id' => 2]);
        $record->save();
        $record = MemberRoleRelation::newInstance(['type' => 6, 'member_id' => 2001, 'role_id' => 3]);
        $record->save();
        $record = MemberRoleRelation::newInstance(['type' => 6, 'member_id' => 2001, 'role_id' => 4]);
        $record->save();

        $record1 = Polymorphic::newInstance();
        $record1->type = 6;
        $record1->toMany = 2000;
        $record1->insert();
        $record2 = Polymorphic::newInstance();
        $record2->type = 6;
        $record2->toMany = 2001;
        $record2->insert();

        // 查询
        $record = Polymorphic::find($record1->id);
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getToManyResult());
        $this->assertCount(2, $r);
        $this->assertEquals(1, $r[0]->id);
        $this->assertEquals(2, $r[1]->id);

        // 查询列表
        $list = Polymorphic::query()->whereIn('id', [$record1->id, $record2->id])->select()->getArray();
        $this->assertCount(2, $list);

        $record = $list[0];
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getToManyResult());
        $this->assertCount(2, $r);
        $this->assertEquals(1, $r[0]->id);
        $this->assertEquals(2, $r[1]->id);

        $record = $list[1];
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getToManyResult());
        $this->assertCount(2, $r);
        $this->assertEquals(3, $r[0]->id);
        $this->assertEquals(4, $r[1]->id);

        // with
        $list = Polymorphic::query()->with('toManyResult')->whereIn('id', [$record1->id, $record2->id])->select()->getArray();
        $this->assertCount(2, $list);

        $record = $list[0];
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getToManyResult());
        $this->assertCount(2, $r);
        $this->assertEquals(1, $r[0]->id);
        $this->assertEquals(2, $r[1]->id);

        $record = $list[1];
        $this->assertNotNull($record);
        $this->assertNotNull($r = $record->getToManyResult());
        $this->assertCount(2, $r);
        $this->assertEquals(3, $r[0]->id);
        $this->assertEquals(4, $r[1]->id);
    }

    /**
     * @depends testInitData
     */
    public function testQueryRelations(array $args): void
    {
        ['memberIds' => $memberIds] = $args;

        // 插入
        $articleIds = [];
        $article1 = Article::newInstance();
        $article1->title = 'relation_title_1';
        $article1->content = 'relation_content_1';
        $article1->memberId = $memberIds[0];
        $article1->ex->data = [
            'id' => 1,
        ];
        $article1->insert();
        $this->assertGreaterThan(0, $article1->id);
        $articleIds[] = $article1->id;

        $article2 = Article::newInstance();
        $article2->title = 'relation_title_2';
        $article2->content = 'relation_content_2';
        $article2->memberId = $memberIds[1];
        $article2->ex->data = [
            'id' => 2,
        ];
        $article2->insert();
        $this->assertGreaterThan(0, $article2->id);
        $articleIds[] = $article2->id;

        $article3 = Article::newInstance();
        $article3->title = 'relation_title_2';
        $article3->content = 'relation_content_2';
        $article3->memberId = $memberIds[1];
        $article3->ex->data = [
            'id' => 2,
        ];
        $article3->insert();
        $this->assertGreaterThan(0, $article3->id);

        // 查询
        $record1 = Article::find($article1->id);
        $record1->queryRelations('queryRelationsList');
        $this->assertNotNull($record1);
        $this->assertEquals($article1->id, $record1->id);
        $this->assertEquals($article1->memberId, $record1->memberId);
        $this->assertEquals($article1->title, $record1->title);
        $this->assertEquals($article1->content, $record1->content);
        $this->assertEquals([
            'id' => 1,
        ], // @phpstan-ignore-next-line
        $record1->queryRelationsList->data->toArray());

        $record2 = Article::find($article2->id);
        $record2->queryRelations('queryRelationsList');
        $this->assertNotNull($record2);
        $this->assertEquals($record2->id, $record2->id);
        $this->assertEquals($record2->memberId, $record2->memberId);
        $this->assertEquals($record2->title, $record2->title);
        $this->assertEquals($article2->content, $record2->content);
        $this->assertEquals([
            'id' => 2,
        ], // @phpstan-ignore-next-line
        $record2->queryRelationsList->data->toArray());

        // 查询列表
        $list = Article::query()->whereIn('id', $articleIds)->select()->getArray();
        Article::queryRelationsList($list, 'queryRelationsList');
        $this->assertEquals(Article::convertListToArray([$record1, $record2]), Article::convertListToArray($list));
    }
}
