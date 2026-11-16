@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'animate-pulse bg-muted rounded ' . $class]) }} aria-hidden="true"></div>
