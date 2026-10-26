@php
    $description = $getDescription();
    $label = $getLabel();
@endphp

<div
    {{
        $attributes
            ->merge(['id' => $getId()], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-rows-row'])
    }}
>
    <div class="fi-rows-label-col">
        @if (filled($label))
            <p class="fi-fo-field-label fi-rows-row-label">
                <span class="fi-fo-field-label-content">{{ $label }}</span>
            </p>
        @endif

        @if (filled($description))
            <p class="fi-rows-description">{{ $description }}</p>
        @endif
    </div>

    <div class="fi-rows-content-col">
        {{ $getChildSchema() }}
    </div>
</div>
