<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\RunScriptCommand;
use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Terminal;

use function Termwind\render;

/**
 * @phpstan-import-type ScriptShape from Configuration
 */
final class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run {names} {--concurrently}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run predefined shell scripts';

    /**
     * Execute the console command.
     */
    public function handle(Terminal $terminal, Configuration $configuration, RunScriptCommand $runCommandAction): void
    {
        /** @var string[] $names */
        $names = str($this->argument('names'))->explode(',')->toArray();
        $concurrently = $this->option('concurrently');

        $scripts = $configuration->scripts(...$names);

        if (filled($missingScripts = $this->missingScripts($names, $scripts))) {
            $this->error(sprintf(
                ' Script `%s` does not exist! ',
                implode(', ', $missingScripts)
            ));

            return;
        }

        if ($concurrently) {
            $commands = array_map(fn (array $script): string => $this->scriptCommand($script, $concurrently), $scripts);
            $colors = array_map(fn (array $script): string => $script['color'] ?? $this->randomHexColor(), $scripts);

            $command = sprintf(
                '%s -c "%s" --names=%s %s',
                'npx concurrently',
                implode(',', $colors),
                implode(',', array_column($scripts, 'name')),
                implode(' ', $commands)
            );

            $runCommandAction->handle($command);

            return;
        }

        foreach ($scripts as $script) {
            when(! $this->isDevPulseCommand($script['command']),
                fn () => $this->scriptHeading($script, $terminal->getWidth())
            );

            $runCommandAction->handle($this->scriptCommand($script));
            $this->newLine();
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    /**
     * @param  array<string>  $names
     * @param  array<ScriptShape>  $scripts
     * @return array<string>
     */
    public function missingScripts(array $names, array $scripts): array
    {
        return array_diff($names, array_column($scripts, 'name'));
    }

    /**
     * Prints a formatted script title.
     *
     * @param  array<key-of<ScriptShape>, value-of<ScriptShape>>  $script
     */
    private function scriptHeading(array $script, int $terminalWidth): void
    {
        $title = "Running: {$script['name']} `{$script['command']}` ";
        $dots = str_repeat('.', max($terminalWidth - mb_strlen($title) - 1, 0));

        render(<<<HTML
            <div>
                <span class="mx-1">$title</span>
                <span class="text-gray-500">$dots</span>
            </div>
        HTML);
    }

    private function randomHexColor(): string
    {
        $chars = 'ABCDEF0123456789';
        $color = '#';
        for ($i = 0; $i < 6; $i++) {
            $color .= $chars[random_int(0, mb_strlen($chars) - 1)];
        }

        return $color;
    }

    /**
     * @param  ScriptShape  $script
     */
    private function scriptCommand(array $script, bool $concurrently = false): string
    {
        $command = str($script['command']);

        /** @var string $appName */
        $appName = config('app.name');

        return $command
            ->when($concurrently, fn (Stringable $s) => $s->wrap('"'))
            ->toString();
    }

    private function isDevPulseCommand(string $command): bool
    {
        /** @var string $appName */
        $appName = config('app.name');

        return Str::startsWith($command, $appName);
    }
}
