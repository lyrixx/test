<?php

namespace poc;

use Castor\Attribute\AsTask;
use Symfony\Component\Process\Process;

use function Castor\context;
use function Castor\fs;
use function Castor\io;
use function Castor\run;

\defined('CASTOR_USE_CHDIR') || \define('CASTOR_USE_CHDIR', true);

const BUILTIN_COMMANDS = ['_complete', 'completion', 'help', 'list'];

#[AsTask(description: 'Installs the dependencies of the Castor copy, without its dev ones, like in the phar')]
function install(): void
{
    if (is_file(__DIR__ . '/castor/vendor/autoload.php') && !is_dir(__DIR__ . '/castor/vendor/phpunit')) {
        return;
    }

    // Without --no-dev, the dev packages of Castor (phpunit, sebastian/*...)
    // count as packages bundled with Castor, which the phar does not bundle
    run(['composer', 'install', '--no-dev', '--no-interaction'], context: context()->withWorkingDirectory(__DIR__ . '/castor'));
}

#[AsTask(description: 'Removes what the scenarios installed')]
function clean(): void
{
    fs()->remove([
        ...glob(__DIR__ . '/plugins/*/vendor'),
        ...glob(__DIR__ . '/plugins/*/composer.lock'),
        // Written when Composer::PLUGIN_REPOSITORY_USES_PLAIN_COMPOSER is false
        ...glob(__DIR__ . '/plugins/*/castor.composer.lock'),
        ...glob(__DIR__ . '/plugins/*/.castor/vendor'),
        ...glob(__DIR__ . '/{apps,plugins}/*/.castor.stub.php', \GLOB_BRACE),
        ...glob(__DIR__ . '/apps/*/.castor/vendor'),
        ...glob(__DIR__ . '/apps/*/castor.composer.lock'),
    ]);
}

#[AsTask(description: 'Runs the scenarios with the Castor copy, from a clean state')]
function check(): int
{
    install();
    clean();

    $failures = 0;
    $rows = [];
    $check = static function (string $scenario, string $status, string $detail = '') use (&$rows, &$failures): void {
        $rows[] = [$scenario, $status, $detail];
        $failures += '✘' === $status ? 1 : 0;
    };
    $expect = static fn (bool $ok): string => $ok ? '✔' : '✘';

    // The repository of a plugin which is a Castor application
    $tasks = tasks('plugins/qa');
    $check('plugins/qa: all its tasks, and the exposed ones of its dev plugin', $expect(['docker:up', 'qa:cs', 'website:build'] === $tasks), implode(', ', $tasks));
    $check('plugins/qa: composer.json + composer.lock + vendor/ only', $expect(is_file(__DIR__ . '/plugins/qa/composer.lock') && is_dir(__DIR__ . '/plugins/qa/vendor/michelf') && !file_exists(__DIR__ . '/plugins/qa/castor.composer.lock') && !file_exists(__DIR__ . '/plugins/qa/.castor/vendor')));
    $output = castor('plugins/qa', ['qa:cs'])->getOutput();
    $check('plugins/qa: its internal listener runs too', $expect(str_contains($output, '[acme/qa internal listener]')));
    $check('plugins/qa: an internal task uses a require-dev package', $expect(str_contains(castor('plugins/qa', ['website:build'])->getOutput(), '<h1>acme/qa</h1>')));

    // A project registering a plugin without castor.php, and requiring another one for its functions
    $tasks = tasks('apps/shop');
    $check('apps/shop: only the exposed tasks of acme/docker, none of acme/qa', $expect(['deploy', 'docker:up'] === $tasks), implode(', ', $tasks));
    $check('apps/shop: the exposed context of acme/docker', $expect(str_contains(castor('apps/shop', ['list'])->getOutput(), 'The context to use (docker)')));
    $check('apps/shop: the functions of a required package, not registered', $expect(str_contains(castor('apps/shop', ['deploy'])->getOutput(), '+v2')));
    $check('apps/shop: the require-dev of acme/qa is not installed', $expect(!file_exists(__DIR__ . '/apps/shop/.castor/vendor/michelf')));

    // A project registering the same plugin twice
    $tasks = tasks('apps/monorepo');
    $check('apps/monorepo: acme/qa twice, with two prefixes', $expect(['project-a:qa:cs', 'project-b:qa:cs'] === $tasks), implode(', ', $tasks));
    $output = castor('apps/monorepo', ['project-a:qa:cs'])->getOutput();
    $check('apps/monorepo: in project/a, the exposed listener runs once', $expect(str_contains($output, 'runs in ' . __DIR__ . '/apps/monorepo/project/a') && 1 === substr_count($output, 'exposed listener')));
    $check('apps/monorepo: the internal listener does not run', $expect(!str_contains($output, 'internal listener')));
    $check('apps/monorepo: in project/b', $expect(str_contains(castor('apps/monorepo', ['project-b:qa:cs'])->getOutput(), 'runs in ' . __DIR__ . '/apps/monorepo/project/b')));
    $check('apps/monorepo: only the exposed context of acme/qa', $expect(str_contains(castor('apps/monorepo', ['list'])->getOutput(), 'The context to use (ci)')));
    $check('apps/monorepo: the register_plugin() of acme/qa for its dev plugin is ignored', $expect(!file_exists(__DIR__ . '/apps/monorepo/.castor/vendor/acme/docker')), 'acme/docker is not installed, no error');

    // A library first, like doctrine/orm, with requirements Castor bundles in another version
    $output = castor('plugins/orm', ['orm:validate-schema'])->getOutput();
    $mixed = str_contains($output, 'Command loaded from ' . __DIR__ . '/castor/vendor/') && str_contains($output, 'Table loaded from ' . __DIR__ . '/plugins/orm/vendor/');
    $check('plugins/orm: two copies of symfony/console mixed in one process', $mixed ? '⚠' : '✘', 'Command from Castor (8.1), Table from vendor/ (7.4), silently');
    $process = castor('apps/orm-user', ['list']);
    $refused = !$process->isSuccessful() && str_contains($process->getErrorOutput() . $process->getOutput(), 'from the packages bundled with Castor');
    $check('apps/orm-user: the same plugin cannot be installed by a project', $refused ? '⚠' : '✘', 'symfony/console ^7.4 vs 8.1 bundled');

    io()->table(['Scenario', '', 'Detail'], $rows);

    return $failures ? 1 : 0;
}

/**
 * Runs the Castor copy in a directory of the POC.
 *
 * @param list<string> $args
 */
function castor(string $directory, array $args): Process
{
    return run(
        ['php', __DIR__ . '/castor/bin/castor', ...$args],
        context: context()->withWorkingDirectory(__DIR__ . '/' . $directory)->withQuiet()->withAllowFailure(),
    );
}

/**
 * The tasks of a directory of the POC, without the built-in commands.
 *
 * @return list<string>
 */
function tasks(string $directory): array
{
    $process = castor($directory, ['list', '--format=json']);

    if (!$process->isSuccessful()) {
        return ['(failed: ' . trim($process->getErrorOutput() ?: $process->getOutput()) . ')'];
    }

    $names = array_column(json_decode($process->getOutput(), true, flags: \JSON_THROW_ON_ERROR)['commands'], 'name');
    $names = array_filter($names, static fn (string $name): bool => !str_starts_with($name, 'castor:') && !\in_array($name, BUILTIN_COMMANDS, true));
    sort($names);

    return array_values($names);
}
