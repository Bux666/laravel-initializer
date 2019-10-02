<?php

namespace MadWeb\Initializer\Test;

use MadWeb\Initializer\Jobs\Supervisor\MakeQueueSupervisorConfig;
use MadWeb\Initializer\Jobs\Supervisor\MakeSocketSupervisorConfig;
use MadWeb\Initializer\Run;

class MakeSupervisorConfigJobTest extends RunnerCommandsTestCase
{
    protected const PATTERN = '/\[program:\w+-%s\]\n(\w+=.*\n)+/';

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function dispatch_queue_job($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatch(new MakeQueueSupervisorConfig([], 'test-queue.conf', base_path('/')));
        }, $command);

        $file_path = base_path('test-queue.conf');

        $this->assertRegExp(sprintf(self::PATTERN, 'queue'), file_get_contents($file_path));

        unlink($file_path);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function dispatch_queue_job_with_override_config($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatch(new MakeQueueSupervisorConfig([
                'directory' => 'some/awesome/path',
                'autostart' => false,
                'user' => 'test-user',
            ], 'test-queue.conf', base_path('/')));
        }, $command);

        $file_path = base_path('test-queue.conf');

        $generated_file_content = file_get_contents($file_path);
        $this->assertStringContainsString('directory=some/awesome/path', $generated_file_content);
        $this->assertStringContainsString('autostart=false', $generated_file_content);
        $this->assertStringContainsString('user=test-user', $generated_file_content);

        unlink($file_path);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function dispatch_socket_job($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatch(new MakeSocketSupervisorConfig([], 'test-queue.conf', base_path('/')));
        }, $command);

        $file_path = base_path('test-queue.conf');

        $this->assertRegExp(sprintf(self::PATTERN, 'socket'), file_get_contents($file_path));

        unlink($file_path);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function dispatch_socket_job_with_override_config($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatch(new MakeSocketSupervisorConfig([
                'directory' => 'some/awesome/path',
                'autostart' => false,
                'user' => 'test-user',
            ], 'test-queue.conf', base_path('/')));
        }, $command);

        $file_path = base_path('test-queue.conf');

        $generated_file_content = file_get_contents($file_path);
        $this->assertStringContainsString('directory=some/awesome/path', $generated_file_content);
        $this->assertStringContainsString('autostart=false', $generated_file_content);
        $this->assertStringContainsString('user=test-user', $generated_file_content);

        unlink($file_path);
    }
}
