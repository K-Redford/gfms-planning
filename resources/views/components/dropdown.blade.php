@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div
    class="relative inline-flex items-center"
    x-data="{
        open: false,
        top: 0,
        left: 0,
        update() {
            const trigger = this.$refs.trigger.getBoundingClientRect();
            const menu = this.$refs.menu;
            const menuWidth = menu ? menu.offsetWidth : 192;
            let left = trigger.left;
            const maxLeft = window.innerWidth - menuWidth - 8;
            if (left > maxLeft) left = Math.max(8, maxLeft);
            this.left = left;
            this.top = trigger.bottom + 8;
        }
    }"
    @click.outside="open = false"
    @close.stop="open = false"
    @resize.window="open && update()"
    @scroll.window="open && update()"
>
    <div class="inline-flex items-center" x-ref="trigger" @click="open = ! open; if (open) { $nextTick(() => update()); }">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-ref="menu"
            :style="`top: ${top}px; left: ${left}px;`"
            class="fixed z-50 {{ $width }} rounded-md shadow-lg"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
