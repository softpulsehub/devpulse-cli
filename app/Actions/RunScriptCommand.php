<?php

declare(strict_types=1);

namespace App\Actions;

use Symfony\Component\Process\Process;

use function Laravel\Prompts\note;

final class RunScriptCommand
{
    public function handle(string $command, int $timeout = 0): void
    {
        $process = new Process(
            $this->toCommandArray($command),
            absolute_path()
        );

        $process->setTimeout($timeout);
        $process->setTty(Process::isTtySupported());
        $process->start();

        while ($process->isRunning()) {
            $this->displayProcessOutput($process);
        }
    }

    /**
     * @return array<string|null>
     */
    private function toCommandArray(string $command): array
    {
        return str_getcsv($command, ' ');
    }

    private function displayProcessOutput(Process $process): void
    {
        while ($process->isRunning() && ($buffer = $process->getIncrementalOutput())) {
            note($buffer);
        }
    }
}
