<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

$header = <<<'TEXT'
This file is part of EasySwoole
@link     https://github.com/easy-swoole
@document https://www.easyswoole.com
@license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
TEXT;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'header' => $header,
            'separate' => 'bottom',
        ],
        'encoding' => true,
        'single_quote' => true,
        'class_attributes_separation' => true,
        'no_unused_imports' => true,
        'global_namespace_import' => true,
        'standardize_not_equals' => true,
        'declare_strict_types' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__ . '/App'
            ])
            ->append([
                __FILE__,
                __DIR__ . '/bootstrap.php',
                __DIR__ . '/EasySwooleEvent.php',
                __DIR__ . '/dev.php',
                __DIR__ . '/produce.php',
            ])
    )
    ->setUsingCache(false);
