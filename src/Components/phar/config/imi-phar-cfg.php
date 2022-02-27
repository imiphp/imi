<?php

declare(strict_types=1);

// 以下输入的路径，无特殊说明都请写入当前工作目录中的相对路径
return [
    // 构建产物
    'output'       => 'build/imi.phar',

    // 参与打包的文件
    'files'        => [],

    // 参与打包的目录
    'dirs'         => [
        'ApiServer',
        'config',
    ],

    // 要排除的的目录，仅对 dirs 扫描到的内容有作用。
    'excludeDirs'  => [],

    // 要排除的的文件，仅对 dirs 扫描到的内容有作用。
    'excludeFiles' => [],

    // 包含 vendor 目录
    // 可选值：
    //   - true  : 打包 vendor 中的文件
    //   - false : 不打包 vendor 中的文件
    //   - symfony/finder实例 : 完全自定义如何获得文件
    // 无特殊情况勿动
    'vendorScan'   => true,

    // 传入 symfony/finder 实例，支持多个，完全自定义扫描的内容。
    'finder' => [],

    // 是否转存构建时的 git 信息
    'dumpGitInfo' => true,

    // 压缩算法，一旦启用压缩，则执行环境也必须加载对应的依赖库
    // 由于兼容性问题，该选项暂时屏蔽
    // 可选值：
    //   - \Phar::NONE : 不压缩
    //   - \Phar::GZ   : 必须启用扩展 zlib
    //   - \Phar::BZ2  : 必须启用扩展 bzip2
    'compression' => \Phar::GZ,
];
