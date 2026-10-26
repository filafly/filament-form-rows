<?php

namespace Filafly\FormRows\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

/**
 * A row that is not a field: a label and description on the left, and any
 * schema on the right, typically an action ("Delete workspace" + a button).
 */
class Row extends Component
{
    use HasDescription;
    use HasLabel;

    protected string $view = 'filafly-form-rows::row';

    final public function __construct(string|Htmlable|Closure|null $label = null)
    {
        $this->label($label);
    }

    public static function make(string|Htmlable|Closure|null $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    /**
     * Shorthand for a schema holding only actions, aligned to the row's end.
     *
     * @param  array<Action | ActionGroup>  $actions
     */
    public function actions(array $actions): static
    {
        return $this->schema([
            Actions::make($actions)->alignEnd(),
        ]);
    }
}
