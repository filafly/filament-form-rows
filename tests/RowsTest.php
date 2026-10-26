<?php

namespace Filafly\FormRows\Tests;

use Filafly\FormRows\Components\Row;
use Filafly\FormRows\Components\Rows;
use Filafly\FormRows\Tests\Fixtures\RowsForm;
use Filafly\FormRows\Tests\Fixtures\WideInput;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\FusedGroup;
use Livewire\Livewire;

class RowsTest extends TestCase
{
    public function test_fields_render_as_rows_with_help_text_beside_the_label(): void
    {
        RowsForm::$schema = fn (): array => [
            Rows::make('Profile')->schema([
                TextInput::make('name')->helperText('Shown on invoices.'),
                Toggle::make('is_public'),
            ]),
        ];

        Livewire::test(RowsForm::class)
            ->assertOk()
            ->assertSeeHtml('fi-rows fi-sc-section')
            ->assertDontSeeHtml('fi-rows-field-stacked')
            ->assertSeeHtmlInOrder([
                'fi-rows-field fi-rows-field-has-label',
                'fi-rows-label-col', 'Name',
                'fi-rows-description', 'Shown on invoices.',
                'fi-rows-content-col', 'id="form.name"',
            ])
            ->assertSeeHtmlInOrder(['fi-rows-label-col', 'Is public', 'fi-rows-content-col', 'fi-toggle']);
    }

    public function test_wide_and_full_width_fields_stack(): void
    {
        $this->assertTrue(Rows::isStacked(Textarea::make('bio')));
        $this->assertTrue(Rows::isStacked(KeyValue::make('meta')));
        $this->assertTrue(Rows::isStacked(TextInput::make('headline')->columnSpanFull()));
        $this->assertFalse(Rows::isStacked(TextInput::make('name')));

        RowsForm::$schema = fn (): array => [
            Rows::make('Profile')->schema([
                Textarea::make('bio'),
            ]),
        ];

        Livewire::test(RowsForm::class)
            ->assertSeeHtml('fi-rows-field-stacked');
    }

    public function test_more_field_types_can_be_stacked(): void
    {
        $this->assertFalse(Rows::isStacked(WideInput::make('code')));

        Rows::stackFields([WideInput::class]);

        $this->assertTrue(Rows::isStacked(WideInput::make('code')));
        $this->assertFalse(Rows::isStacked(TextInput::make('name')));
    }

    public function test_validation_errors_stay_under_the_control(): void
    {
        RowsForm::$schema = fn (): array => [
            Rows::make('Profile')->schema([
                TextInput::make('name')->required(),
            ]),
        ];

        Livewire::test(RowsForm::class)
            ->call('save')
            ->assertHasErrors(['data.name' => 'required'])
            ->assertSeeHtmlInOrder(['fi-rows-content-col', 'id="form.name"', 'fi-fo-field-wrp-error-message']);
    }

    public function test_an_action_row_renders_its_label_description_and_actions(): void
    {
        RowsForm::$schema = fn (): array => [
            Rows::make('Danger zone')->schema([
                Row::make('Reset settings')
                    ->description('Restore every default.')
                    ->actions([
                        Action::make('reset')->label('Reset'),
                    ]),
            ]),
        ];

        Livewire::test(RowsForm::class)
            ->assertSeeHtmlInOrder([
                'fi-rows-row',
                'fi-rows-row-label', 'Reset settings',
                'fi-rows-description', 'Restore every default.',
                'fi-rows-content-col', 'Reset',
            ]);
    }

    public function test_a_fused_group_renders_inside_rows(): void
    {
        RowsForm::$schema = fn (): array => [
            Rows::make('Pricing')->schema([
                FusedGroup::make([
                    TextInput::make('amount'),
                    TextInput::make('currency'),
                ])->label('Price'),
            ]),
        ];

        Livewire::test(RowsForm::class)
            ->assertOk()
            ->assertSeeHtmlInOrder(['fi-rows-field', 'Price', 'fi-rows-content-col', 'id="form.amount"']);
    }
}
