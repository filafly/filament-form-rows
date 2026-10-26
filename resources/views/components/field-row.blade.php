{{--
    Based on filament-forms::components.field-wrapper. The structure differs in
    one way: help text renders under the label instead of under the control,
    so a row reads "what it is, why it matters" on the left and "set it" on the
    right. Keep the rest in step with Filament's wrapper when upgrading.

    Filament renders this with view(). Fields pass $field, $slot, $attributes,
    $labelPrefix, $labelSuffix, $labelTag, $hasErrors, $errorMessage and
    $errorMessages; fused groups pass only some of those, plus their own
    $areHtmlErrorMessagesAllowed, so everything but $field and $slot defaults.
--}}
@php
    use Filafly\FormRows\Components\Rows;
    use Filament\Forms\Components\Field;
    use Filament\Support\View\ComponentAttributeBag;
    use Illuminate\View\ComponentSlot;

    $attributes ??= new ComponentAttributeBag;
    $errorMessage ??= null;
    $errorMessages ??= null;
    $hasErrors ??= true;
    $labelPrefix ??= null;
    $labelSuffix ??= null;
    $labelTag ??= 'label';
    $areHtmlErrorMessagesAllowed ??= method_exists($field, 'areHtmlValidationMessagesAllowed') && $field->areHtmlValidationMessagesAllowed();

    $id = $field->getId();
    $isDisabled = $field->isDisabled();
    $label = $field->getLabel();
    $labelSrOnly = $field->isLabelHidden();
    $required = $field->isMarkedAsRequired();

    $aboveLabelSchema = $field->getChildSchema($field::ABOVE_LABEL_SCHEMA_KEY)?->toHtmlString();
    $belowLabelSchema = $field->getChildSchema($field::BELOW_LABEL_SCHEMA_KEY)?->toHtmlString();
    $beforeLabelSchema = $field->getChildSchema($field::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
    $afterLabelSchema = $field->getChildSchema($field::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
    $aboveContentSchema = $field->getChildSchema($field::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $belowContentSchema = $field->getChildSchema($field::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $beforeContentSchema = $field->getChildSchema($field::BEFORE_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $afterContentSchema = $field->getChildSchema($field::AFTER_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $aboveErrorMessageSchema = $field->getChildSchema($field::ABOVE_ERROR_MESSAGE_SCHEMA_KEY)?->toHtmlString();
    $belowErrorMessageSchema = $field->getChildSchema($field::BELOW_ERROR_MESSAGE_SCHEMA_KEY)?->toHtmlString();

    // The below-content schema is the help text when the field set one with
    // helperText(); anything else placed there stays under the control.
    $isBelowContentHelperText = filled($belowContentSchema) && filled($field->getHelperTextId());

    $isStacked = ($field instanceof Field) && Rows::isStacked($field);

    $hasError = $hasErrors && (filled($errorMessage) || filled($errorMessages));

    $hasLabelColumn = (filled($label) && (! $labelSrOnly)) || $aboveLabelSchema || $belowLabelSchema || $beforeLabelSchema || $afterLabelSchema || $labelPrefix || $labelSuffix || $isBelowContentHelperText;
@endphp

<div
    data-field-wrapper
    {{
        $attributes
            ->merge($field->getExtraFieldWrapperAttributes(), escape: false)
            ->class([
                'fi-fo-field',
                'fi-rows-field',
                'fi-rows-field-has-label' => $hasLabelColumn,
                'fi-rows-field-stacked' => $isStacked,
            ])
    }}
>
    @if (($field instanceof Field) && filled($requiredDescription = $field->getRequiredDescription()))
        <span id="{{ $id }}-required" class="fi-sr-only">
            {{ $requiredDescription }}
        </span>
    @endif

    @if (filled($label) && $labelSrOnly)
        <{{ $labelTag }}
            @if ($labelTag === 'label')
                for="{{ $id }}"
            @elseif (filled($id))
                id="{{ $id }}-label"
            @endif
            class="fi-fo-field-label fi-sr-only"
        >
            {{ $label }}
        </{{ $labelTag }}>
    @endif

    @if ($hasLabelColumn)
        <div class="fi-rows-label-col">
            {{ $aboveLabelSchema }}

            <div
                @class([
                    'fi-fo-field-label-ctn',
                    ($label instanceof ComponentSlot) ? $label->attributes->get('class') : null,
                ])
            >
                {{ $beforeLabelSchema }}

                @if ((filled($label) && (! $labelSrOnly)) || $labelPrefix || $labelSuffix)
                    <{{ $labelTag }}
                        @if ($labelTag === 'label')
                            for="{{ $id }}"
                        @elseif (filled($id))
                            id="{{ $id }}-label"
                        @endif
                        class="fi-fo-field-label"
                    >
                        {{ $labelPrefix }}

                        @if (filled($label) && (! $labelSrOnly))
                            <span class="fi-fo-field-label-content">
                                {{ $label }}@if ($required && (! $isDisabled))<sup class="fi-fo-field-label-required-mark">*</sup>
                                @endif
                            </span>
                        @endif

                        {{ $labelSuffix }}
                    </{{ $labelTag }}>
                @endif

                {{ $afterLabelSchema }}
            </div>

            {{ $belowLabelSchema }}

            @if ($isBelowContentHelperText)
                <div class="fi-rows-description">
                    {{ $belowContentSchema }}
                </div>
            @endif
        </div>
    @endif

    <div class="fi-rows-content-col">
        {{ $aboveContentSchema }}

        @if ($beforeContentSchema || $afterContentSchema)
            <div class="fi-fo-field-content-ctn">
                {{ $beforeContentSchema }}

                <div class="fi-fo-field-content">
                    {{ $slot }}
                </div>

                {{ $afterContentSchema }}
            </div>
        @else
            {{ $slot }}
        @endif

        @if (! $isBelowContentHelperText)
            {{ $belowContentSchema }}
        @endif

        @if ($hasError)
            {{ $aboveErrorMessageSchema }}

            @if (filled($errorMessages))
                <ul
                    @if (filled($id))
                        id="{{ $id }}-error"
                    @endif
                    data-validation-error
                    class="fi-fo-field-wrp-error-list"
                >
                    @foreach ($errorMessages as $errorMessage)
                        <li class="fi-fo-field-wrp-error-message">
                            @if ($areHtmlErrorMessagesAllowed)
                                {!! $errorMessage !!}
                            @else
                                {{ $errorMessage }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @elseif ($areHtmlErrorMessagesAllowed)
                <div
                    @if (filled($id))
                        id="{{ $id }}-error"
                    @endif
                    data-validation-error
                    class="fi-fo-field-wrp-error-message"
                >
                    {!! $errorMessage !!}
                </div>
            @else
                <p
                    @if (filled($id))
                        id="{{ $id }}-error"
                    @endif
                    data-validation-error
                    class="fi-fo-field-wrp-error-message"
                >
                    {{ $errorMessage }}
                </p>
            @endif

            {{ $belowErrorMessageSchema }}
        @endif
    </div>
</div>
