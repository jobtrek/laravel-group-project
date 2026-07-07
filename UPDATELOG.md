# Update Log

## Fix #277: Unescaped `fallbackUrl` in comeBackButton onclick handler

`resources/views/components/projects-details/comeBackButton.blade.php` built its
`onclick` handler with `window.location.href = '{{ $fallbackUrl }}';`. Blade's
`{{ }}` only HTML-escapes, but this value sits inside a single-quoted JavaScript
string within an inline event handler attribute. A `fallbackUrl` containing a
single quote could break out of the string and inject arbitrary JS. All current
callers pass `route()`-generated URLs, so this was a latent rather than actively
exploited vector, but it was fixed anyway.

Replaced `'{{ $fallbackUrl }}'` with `@js($fallbackUrl)`, which encodes the value
via `Illuminate\Support\Js::from()` (JSON-encoding with `JSON_HEX_*` flags) and
supplies its own surrounding quotes — safe for both the JS string context and the
enclosing double-quoted HTML attribute. Verified by hand-simulating the encoding
logic with a payload containing `'`, `"`, `<`, `>`, and `&`: no raw special
characters survive in the rendered output.

Checked all three call sites (`edit.blade.php`, `projectsDetails.blade.php`,
`phase_details.blade.php`) — only `edit.blade.php` passes a `fallback-url`
(via `route()`); the others use the component's default (no fallback branch),
so behavior is unchanged for them. No other Blade templates were found using
`{{ }}` inside `onclick=`/`onchange=`/etc. attribute strings, so no other files
needed changes.
