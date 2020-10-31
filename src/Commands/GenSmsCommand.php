<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Commands;

use Hyperf\Devtool\Generator\GeneratorCommand;

class GenSmsCommand extends GeneratorCommand
{
    public function __construct()
    {
        parent::__construct('gen:sms');
        $this->setDescription('Create a new SMS message class');
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/message.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Sms';
    }
}
