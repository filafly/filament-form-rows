# Changelog

## v1.0.1 - 2026-11-04

- The package homepage now points to its page on filafly.com, with docs and a screenshot. No code changes.

## v1.0.0 - 2026-11-04

First public release.

- `Rows`, a drop-in replacement for `Section` that lays each field out as a settings row: label and help text on the left, the control on the right, hairlines between rows.
- `helperText()` renders under the label. Wide controls (Builder, CheckboxList, KeyValue, MarkdownEditor, Repeater, RichEditor, Textarea, and anything `columnSpanFull()`) stack, and `Rows::stackFields()` adds your own.
- Rows split by the section's width, not the screen's, so a narrow section or modal stacks on its own.
- `Row`, a non-field row with a label, description and actions, for danger zones and one-off settings actions.
- Theming through `--fi-rows-*` CSS custom properties.
- Supports Filament v4 and v5.
