<?php

namespace Guirong\Laravel\Prompts\Concerns;

use InvalidArgumentException;
use Guirong\Laravel\Prompts\ConfirmPrompt;
use Guirong\Laravel\Prompts\MultiSelectPrompt;
use Guirong\Laravel\Prompts\Note;
use Guirong\Laravel\Prompts\PasswordPrompt;
use Guirong\Laravel\Prompts\SearchPrompt;
use Guirong\Laravel\Prompts\SelectPrompt;
use Guirong\Laravel\Prompts\Spinner;
use Guirong\Laravel\Prompts\SuggestPrompt;
use Guirong\Laravel\Prompts\TextPrompt;
use Guirong\Laravel\Prompts\Themes\Default\ConfirmPromptRenderer;
use Guirong\Laravel\Prompts\Themes\Default\MultiSelectPromptRenderer;
use Guirong\Laravel\Prompts\Themes\Default\NoteRenderer;
use Guirong\Laravel\Prompts\Themes\Default\PasswordPromptRenderer;
use Guirong\Laravel\Prompts\Themes\Default\SearchPromptRenderer;
use Guirong\Laravel\Prompts\Themes\Default\SelectPromptRenderer;
use Guirong\Laravel\Prompts\Themes\Default\SpinnerRenderer;
use Guirong\Laravel\Prompts\Themes\Default\SuggestPromptRenderer;
use Guirong\Laravel\Prompts\Themes\Default\TextPromptRenderer;

trait Themes
{
    /**
     * The name of the active theme.
     */
    protected static string $theme = 'default';

    /**
     * The available themes.
     *
     * @var array<string, array<class-string<\Guirong\Laravel\Prompts\Prompt>, class-string<object&callable>>>
     */
    protected static array $themes = [
        'default' => [
            TextPrompt::class => TextPromptRenderer::class,
            PasswordPrompt::class => PasswordPromptRenderer::class,
            SelectPrompt::class => SelectPromptRenderer::class,
            MultiSelectPrompt::class => MultiSelectPromptRenderer::class,
            ConfirmPrompt::class => ConfirmPromptRenderer::class,
            SearchPrompt::class => SearchPromptRenderer::class,
            SuggestPrompt::class => SuggestPromptRenderer::class,
            Spinner::class => SpinnerRenderer::class,
            Note::class => NoteRenderer::class,
        ],
    ];

    /**
     * Get or set the active theme.
     *
     * @throws \InvalidArgumentException
     */
    public static function theme(string $name = null): string
    {
        if ($name === null) {
            return static::$theme;
        }

        if (! isset(static::$themes[$name])) {
            throw new InvalidArgumentException("Prompt theme [{$name}] not found.");
        }

        return static::$theme = $name;
    }

    /**
     * Add a new theme.
     *
     * @param  array<class-string<\Guirong\Laravel\Prompts\Prompt>, class-string<object&callable>>  $renderers
     */
    public static function addTheme(string $name, array $renderers): void
    {
        if ($name === 'default') {
            throw new InvalidArgumentException('The default theme cannot be overridden.');
        }

        static::$themes[$name] = $renderers;
    }

    /**
     * Get the renderer for the current prompt.
     */
    protected function getRenderer(): callable
    {
        $class = get_class($this);

        return new (static::$themes[static::$theme][$class] ?? static::$themes['default'][$class])($this);
    }

    /**
     * Render the prompt using the active theme.
     */
    protected function renderTheme(): string
    {
        $renderer = $this->getRenderer();

        return $renderer($this);
    }
}
