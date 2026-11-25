# Castor plugins POC

Proof of concept for [jolicode/castor#943](https://github.com/jolicode/castor/issues/943),
built on top of [#948](https://github.com/jolicode/castor/pull/948) (`6ed1f815`).

## The idea

- A plugin is a regular Castor application: a `castor.php` (optional), local
  `import()`, files it autoloads. Its repository has one `composer.json` and its
  `composer.lock`: no `castor.composer.json`, and the package never requires
  itself.
- The author marks what is exposed: `#[AsExposedTask]`, `#[AsExposedListener]`,
  `#[AsExposedContext]` and `#[AsExposedContextGenerator]`, which extend the
  `As*` attributes. Anything unmarked is internal: it only exists in the
  plugin's repository.
- `register_plugin()` (from #948) loads the plugin's `castor.php` and the files
  it autoloads, and keeps the exposed definitions only. Requiring a package
  without registering it gives its functions and classes, nothing else.
- A plugin can be registered several times, each time with its own prefix and
  working directory. The default working directory is the project's.

## Run it

Castor and Composer installed, PHP 8.4 or later:

```bash
castor poc:check   # installs, runs every scenario from a clean state
castor poc:clean   # removes what the scenarios installed
```

`poc:check` runs the Castor copy (`castor/bin/castor`) in each directory:

```text
plugins/qa: all its tasks, and the exposed ones of its dev plugin               ✔   docker:up, qa:cs, website:build
plugins/qa: composer.json + composer.lock + vendor/ only                        ✔
plugins/qa: its internal listener runs too                                      ✔
plugins/qa: an internal task uses a require-dev package                         ✔
apps/shop: only the exposed tasks of acme/docker, none of acme/qa               ✔   deploy, docker:up
apps/shop: the exposed context of acme/docker                                   ✔
apps/shop: the functions of a required package, not registered                  ✔
apps/shop: the require-dev of acme/qa is not installed                          ✔
apps/monorepo: acme/qa twice, with two prefixes                                 ✔   project-a:qa:cs, project-b:qa:cs
apps/monorepo: in project/a, the exposed listener runs once                     ✔
apps/monorepo: the internal listener does not run                               ✔
apps/monorepo: in project/b                                                     ✔
apps/monorepo: only the exposed context of acme/qa                              ✔
apps/monorepo: the register_plugin() of acme/qa for its dev plugin is ignored   ✔   acme/docker is not installed, no error
plugins/orm: two copies of symfony/console mixed in one process                 ⚠   Command from Castor (8.1), Table from vendor/ (7.4), silently
apps/orm-user: the same plugin cannot be installed by a project                 ⚠   symfony/console ^7.4 vs 8.1 bundled
```

## Layout

| Directory        | What                                                                                                                         |
|------------------|------------------------------------------------------------------------------------------------------------------------------|
| `castor/`        | Castor at #948, with the POC changes (see below)                                                                             |
| `plugins/qa`     | `acme/qa`: a plugin with a `castor.php` that imports `.castor/`, exposed and internal tasks, a `require` and a `require-dev` |
| `plugins/docker` | `acme/docker`: no `castor.php`, tasks and classes in its autoload, like `castor-php/docker`                                  |
| `plugins/orm`    | `acme/orm`: a library first, like `doctrine/orm`, requiring `symfony/console` 7 while Castor bundles 8                       |
| `apps/shop`      | Registers `acme/docker`, uses `acme/qa` for its functions only                                                               |
| `apps/monorepo`  | Registers `acme/qa` twice, as `project-a:` in `project/a` and `project-b:` in `project/b`                                    |
| `apps/orm-user`  | Requires and registers `acme/orm`                                                                                            |

The plugins are not on Packagist: every project, and `acme/qa` for its dev
plugin, requires them through a [path repository](https://getcomposer.org/doc/05-repositories.md#path),
symlinked by default:

```json
{
    "repositories": [{ "type": "path", "url": "../../plugins/qa" }],
    "require": { "acme/qa": "*@dev" }
}
```

Composer reads the `require` of the plugin's `composer.json` like for a
Packagist package, and never its `require-dev`. The only difference with a
Packagist archive: the symlinked directory also holds the plugin's `vendor/`,
which Castor never loads. A Satis or an `artifact` repository would remove
this difference, but would not change what the POC checks.

`plugins/docker` has no `castor.php` on purpose: running `castor` in it picks the
`castor.php` of the POC itself. It is only used from the projects.

## Findings

1. **A plain `vendor/` mixes two versions of the packages Castor bundles.** In
   the repository of `acme/orm`, `Command` comes from Castor (symfony/console
   8.1), and `Table` comes from the plugin's `vendor/` (7.4): the Composer
   autoloader of `vendor/` is prepended, so it wins for every class Castor has
   not loaded yet. Nothing warns. The same happens with any `require-dev` tool
   pulling a `symfony/*` package. Meanwhile, a project cannot even install
   `acme/orm`: its Composer resolves against the packages bundled with Castor
   and refuses. The plugin's repository hides a problem every user of the
   plugin hits. #934 avoided it by installing in `.castor/vendor/`, resolved
   against the bundled packages, without `require-dev`.
   `Composer::PLUGIN_REPOSITORY_USES_PLAIN_COMPOSER` switches between both.
   Set to `false`, the repository of `acme/orm` fails like its projects do (a
   resolution error), and the one of `acme/qa` fails too: its dev plugin
   `acme/docker` is a `require-dev`, so it is not installed.
2. **Running Castor from its sources counts its dev packages as bundled.**
   With phpunit installed, `sebastian/diff` 9 is "bundled", and `acme/qa`
   (`sebastian/diff ^7`) cannot be installed. `poc:install` installs Castor
   with `--no-dev`, like in the phar.
3. **A project loads the whole `castor.php` of a plugin**, internal files
   included, without the plugin's `require-dev`. It works as long as nothing
   runs at load time: `website.php` only uses `michelf/php-markdown` inside a
   function. A `register_plugin()` in the plugin's `castor.php` would fail at
   the project, as the dev plugin is not installed there, so the POC ignores
   it.
4. **The exposed contexts join the contexts of the project.** An exposed
   context with `default: true` would become the default one of the project.
   In the repository of `acme/qa`, Castor requires a default context as soon
   as there are two of them, so the internal one is the default.
5. **PHP defines a function only once.** The next registrations of a plugin
   reuse the definitions of the first one. Only the tasks are added again:
   the listeners would run twice, and the contexts would clash.
6. **A plain `composer install` and Castor share the repository of a
   plugin.** After `composer install`, Castor installs once more, as it has
   not written its `vendor/composer.installed` marker yet: `composer.lock` is
   left unchanged, and `vendor/` gets the marker and a `.gitignore`.
   `castor composer show` lists the packages of `composer.json`.

## Assumed and open

What the POC assumes, the position of the author of #943 (not settled with
the other maintainers):

- `AsExposed*` for tasks, listeners and contexts; anything unmarked is not
  exposed;
- the default working directory is the project's, and can be changed;
- several registrations, each with its own prefix and working directory;
- one `composer.json`, no `castor.composer.json`, no self-require;
- `import()` and `mount()` for local paths only (the POC keeps the
  deprecations of #948).

Still open, with the default the POC picked:

- a `register_plugin()` in the `castor.php` of a plugin, while a project
  loads it: ignored;
- listeners and contexts on the next registrations: first registration only;
- prefix or rename: prefix only;
- a filter on the project side: not implemented;
- the features of #948: kept, not used by the fixtures;
- backward compatibility: a package that is not registered exposes nothing,
  as in Castor 2.0, without the 1.x deprecation;
- the `acme/orm` case (finding 1): not solved.

## Changes to Castor

On top of #948, in `castor/src` (about 250 lines):

- `Attribute/AsExposed*.php`: the four attributes. `FunctionResolver` finds
  the listeners with `IS_INSTANCEOF`, like the other attributes.
- `Import/PluginRegistrar.php`, `functions.php`: `register_plugin()` takes a
  `workingDirectory`, allows several registrations, ignores the calls made
  while a plugin loads. Without features, the plugin's `castor.php` is loaded.
- `Kernel.php`: keeps the exposed definitions of a plugin, reuses them for the
  next registrations, and drops the definitions of the packages not
  registered.
- `Import/Remote/Composer.php`, `Console/ApplicationFactory.php`,
  `Console/Command/ComposerCommand.php`: the repository of a `castor-plugin`
  package uses `composer.json`, `composer.lock` and `vendor/`, dev
  requirements included, without the bundled packages.

Not done: the test suite of Castor is not updated (the POC changes behaviors it
asserts), PHPStan was not run, and of `castor composer`, only `show` was run in
the repository of a plugin. The projects require the plugins through
symlinked path repositories: a Packagist archive would not contain the
plugin's `vendor/`, which Castor never loads anyway.
