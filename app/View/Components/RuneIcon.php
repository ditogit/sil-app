<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RuneIcon extends Component
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $class = 'w-5 h-5',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|string
    {
        $key = preg_replace('/[^a-z0-9-]/', '', strtolower($this->name));

        if (! isset(static::$cache[$key])) {
            $path = resource_path("svg/{$key}.svg");

            if (! is_file($path)) {
                return '';
            }

            static::$cache[$key] = (string) file_get_contents($path);
        }

        $svg = preg_replace(
            '/<svg([^>]*)>/',
            '<svg$1 class="'.e($this->class, false).'" aria-hidden="true">',
            static::$cache[$key],
            1,
        );

        // Strip the file's default width/height so the w-*/h-* classes apply.
        $svg = (string) preg_replace('/\s(?:width|height)="[^"]*"/', '', $svg);

        return $svg;
    }
}
