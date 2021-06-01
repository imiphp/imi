<?php

declare(strict_types=1);

namespace Imi\AC\Test\Tool;

use Imi\AC\AccessControl\Member;
use Imi\AC\AccessControl\Operation as ACOperation;
use Imi\AC\AccessControl\Role;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Tool;

/**
 * @Tool("test")
 */
class TestTool
{
    public function __construct()
    {
    }

    /**
     * @Operation("test")
     *
     * @return void
     */
    public function test()
    {
        // ACOperation::create('文章新增');
        // ACOperation::create('文章更新');
        // ACOperation::create('文章删除', null, '测试说明');

        // $role = Role::create('test-' . mt_rand(), 'test-' . mt_rand(), 'des-' . mt_rand());
        // if($role)
        // {
        // 	var_dump($role->getRoleInfo()->toArray());
        // }
        // $role = Role::create('test-' . mt_rand());
        // if($role)
        // {
        // 	var_dump($role->getRoleInfo()->toArray());
        // }

        // $role = new Role('test');
        // var_dump($role->getRoleInfo()->toArray());
        // $role->addOperations('文章新增', '文章更新');
        // var_dump(json_decode(json_encode($role->getOperations()), true), json_decode(json_encode($role->getOperationTree()), true));

        // var_dump($role->hasOperations('文章新增'), $role->hasOperations('文章新增', '文章更新'), $role->hasOperations('文章新增', '文章删除'));

        // $role->setOperations('文章新增');
        // var_dump(json_decode(json_encode($role->getOperations()), true));

        // $role->removeOperations('文章新增');
        // var_dump(json_decode(json_encode($role->getOperations()), true));

        $member = new Member(1);
        $member->addRoles('test-297864474', 'test-710491826');
        var_dump('roles', json_decode(json_encode($member->getRoles()), true));
        var_dump('operations', json_decode(json_encode($member->getOperations()), true));
        $member->setRoles('test-297864474', 'test-710491826');
        $member->removeRoles('test-297864474');
        var_dump('roles', json_decode(json_encode($member->getRoles()), true));
        var_dump('operations', json_decode(json_encode($member->getOperations()), true));
        var_dump($member->hasRoles('test-297864474'), $member->hasRoles('test-710491826'));

        $member->addOperations('30');
        var_dump('operations', json_decode(json_encode($member->getOperations()), true));
        $member->setOperations('28');
        var_dump('operations', json_decode(json_encode($member->getOperations()), true));
        var_dump($member->hasOperations('28'));
        var_dump($member->hasOperations('30'));
        $member->removeOperations('28');
        var_dump('operations', json_decode(json_encode($member->getOperations()), true));
    }
}
