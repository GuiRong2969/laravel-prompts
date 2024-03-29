<?php

namespace Guirong\Laravel\Prompts\Themes\Default;

use Guirong\Laravel\Prompts\MultiSelectPrompt;

class MultiSelectPromptRenderer extends Renderer
{
    use Concerns\DrawsBoxes;
    use Concerns\DrawsScrollbars;

    /**
     * Render the multiselect prompt.
     */
    public function __invoke(MultiSelectPrompt $prompt): string
    {
        return match ($prompt->state) {
            'submit' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->renderSelectedOptions($prompt)
                ),

            'cancel' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->renderOptions($prompt),
                    color: 'red',
                )
                ->error('Cancelled.'),

            'error' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->renderOptions($prompt),
                    color: 'yellow',
                )
                ->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),

            default => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->renderOptions($prompt),
                )
                ->newLine(), // Space for errors
        };
    }

    /**
     * Render the options.
     */
    protected function renderOptions(MultiSelectPrompt $prompt): string
    {
        return $this->scroll(
            collect($prompt->options)
                ->values()
                ->map(fn ($label) => $this->truncate($this->format($label), $prompt->terminal()->cols() - 12))
                ->map(function ($label, $index) use ($prompt) {
                    $active = $index === $prompt->highlighted;
                    if (array_is_list($prompt->options)) {
                        $value = $prompt->options[$index];
                    } else {
                        $value = array_keys($prompt->options)[$index];
                    }
                    $selected = in_array($value, $prompt->value());

                    if ($prompt->state === 'cancel') {
                        return $this->dim(match (true) {
                            $active && $selected => "› ◼ {$this->strikethrough($label)}  ",
                            $active => "› ◻ {$this->strikethrough($label)}  ",
                            $selected => "  ◼ {$this->strikethrough($label)}  ",
                            default => "  ◻ {$this->strikethrough($label)}  ",
                        });
                    }

                    return match (true) {
                        $active && $selected => "{$this->cyan('› ◼')} {$label}  ",
                        $active => "{$this->cyan('›')} ◻ {$label}  ",
                        $selected => "  {$this->cyan('◼')} {$this->dim($label)}  ",
                        default => "  {$this->dim('◻')} {$this->dim($label)}  ",
                    };
                }),
            $prompt->highlighted,
            min($prompt->scroll, $prompt->terminal()->lines() - 5),
            min($this->longest($prompt->options, padding: 6), $prompt->terminal()->cols() - 6),
            $prompt->state === 'cancel' ? 'dim' : 'cyan'
        )->implode(PHP_EOL);
    }

    /**
     * Render the selected options.
     */
    protected function renderSelectedOptions(MultiSelectPrompt $prompt): string
    {
        if (count($prompt->labels()) === 0) {
            return $this->gray('None');
        }

        return implode("\n", array_map(
            fn ($label) => $this->truncate($this->format($label), $prompt->terminal()->cols() - 6),
            $prompt->labels()
        ));
    }
}
