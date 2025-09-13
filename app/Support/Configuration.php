<?php

declare(strict_types=1);

namespace App\Support;

use App\Constants\LiteralValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\info;

/**
 * @phpstan-type ScriptShape array{name: string, command: string, color?: string}
 * @phpstan-type ConfigurationShape array{scripts: ScriptShape[]}
 */
final readonly class Configuration
{
    private const JSON_CONFIGURATION_NAME = 'devpulse.json';

    public function __construct(
        /** @var ScriptShape[] */
        private array $scripts = LiteralValue::EMPTY_ARRAY
    ) {
        // ...
    }

    public static function fileName(): string
    {
        return self::JSON_CONFIGURATION_NAME;
    }

    public static function filePath(): string
    {
        return absolute_path(self::fileName());
    }

    public static function exists(): bool
    {
        return File::exists(self::filePath());
    }

    public static function delete(): bool
    {
        return File::delete(self::filePath());
    }

    /**
     * @param  Collection<key-of<ConfigurationShape>, value-of<ConfigurationShape>>|LiteralValue::NULL  $configurationContent
     */
    public static function write(?Collection $configurationContent = LiteralValue::NULL): void
    {
        if (blank($configurationContent)) {
            $configurationContent = collect([
                'scripts' => [
                    // TODO:: Add Some Default Scripts
                ],
            ]);
        }

        File::put(
            self::filePath(),
            $configurationContent->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    public static function make(): self
    {
        if (! self::exists()) {
            info('Look like devpulse is not configured yet. Configuring now...');

            self::write();
        }

        $jsonContents = File::get(self::filePath());
        /** @var ConfigurationShape $jsonContentsAsArray */
        $jsonContentsAsArray = json_decode($jsonContents, true);

        return new self($jsonContentsAsArray['scripts']);
    }

    public static function instance(): self
    {
        return resolve(self::class);
    }

    /**
     * @return ScriptShape[]
     */
    public function scripts(string ...$keys): array
    {
        if (blank($keys)) {
            return $this->scripts;
        }

        /** @var ScriptShape[] $scripts */
        $scripts = Arr::where($this->scripts, fn (array $script): bool => in_array($script['name'], $keys));

        return array_reverse($scripts);
    }
}
