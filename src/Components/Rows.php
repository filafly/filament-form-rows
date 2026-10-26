<?php

namespace Filafly\FormRows\Components;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

/**
 * A section whose fields render as full-width rows divided by hairlines: the
 * label and help text on the left, the control in a fixed-width column on the
 * right.
 */
class Rows extends Section
{
    /**
     * Fields too wide for the control column. They stack instead: label and
     * help text above, the control across the full row.
     *
     * @var array<class-string<Field>>
     */
    protected static array $stackedFields = [
        Builder::class,
        CheckboxList::class,
        KeyValue::class,
        MarkdownEditor::class,
        Repeater::class,
        RichEditor::class,
        Textarea::class,
    ];

    /**
     * Stack more field types, e.g. a custom field with a wide control.
     *
     * @param  array<class-string<Field>>  $fields
     */
    public static function stackFields(array $fields): void
    {
        static::$stackedFields = [...static::$stackedFields, ...$fields];
    }

    /**
     * A field stacks when its type is wide or it asks for the full width with
     * columnSpanFull().
     */
    public static function isStacked(Field $field): bool
    {
        if ($field->getColumnSpan('default') === 'full') {
            return true;
        }

        foreach (static::$stackedFields as $stackedField) {
            if ($field instanceof $stackedField) {
                return true;
            }
        }

        return false;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Inline labels make toggles and checkboxes render their control as
        // the field content instead of beside their own label.
        $this
            ->inlineLabel()
            ->fieldWrapperView('filafly-form-rows::field-row');
    }

    /**
     * @return array<mixed>
     */
    public function getExtraAttributes(): array
    {
        $attributes = parent::getExtraAttributes();

        $attributes['class'] = trim('fi-rows '.($attributes['class'] ?? ''));

        return $attributes;
    }
}
