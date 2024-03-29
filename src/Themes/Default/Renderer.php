<?php

namespace Guirong\Laravel\Prompts\Themes\Default;

use InvalidArgumentException;
use Guirong\Laravel\Prompts\Concerns\Colors;
use Guirong\Laravel\Prompts\Prompt;

abstract class Renderer
{
    use Colors;

    /**
     * The output to be rendered.
     */
    protected string $output = '';

    /**
     * Create a new renderer instance.
     */
    public function __construct(protected Prompt $prompt)
    {
        //
    }

    /**
     * Render a line of output.
     */
    protected function line(string $message): self
    {
        $this->output .= $message.PHP_EOL;

        return $this;
    }

    /**
     * Render a new line.
     */
    protected function newLine(int $count = 1): self
    {
        $this->output .= str_repeat(PHP_EOL, $count);

        return $this;
    }

    /**
     * Render a warning message.
     */
    protected function warning(string $message): self
    {
        return $this->line($this->yellow("  ⚠ {$message}"));
    }

    /**
     * Render an error message.
     */
    protected function error(string $message): self
    {
        return $this->line($this->red("  ⚠ {$message}"));
    }

    /**
     * Truncate a value with an ellipsis if it exceeds the given length.
     */
    protected function truncate(string $value, int $length): string
    {
        if ($length <= 0) {
            throw new InvalidArgumentException("Length [{$length}] must be greater than zero.");
        }

        return mb_strlen($value) <= $length ? $value : (mb_substr($value, 0, $length - 1).'…');
    }

    /**
     * Render the output with a blank line above and below.
     */
    public function __toString()
    {
        return str_repeat(PHP_EOL, 2 - $this->prompt->newLinesWritten())
            .$this->output
            .(in_array($this->prompt->state, ['submit', 'cancel']) ? PHP_EOL : '');
    }
}
