{{-- format-ignore-start --}}
@props([
    'lightIcon' => 'sun',
    'lightText' => 'Light',
    'darkIcon' => 'moon',
    'darkText' => 'Dark',
    'systemIcon' => 'computer-desktop',
    'systemText' => 'System',
    'iconRight' => true,
    'iconType' => 'outline',
    'iconDir' => '',
    'modular' => false,
    'class' => '',
    'nonce' => config('bladewind.script.nonce', null),
])
@php
    $iconRight = parseBladewindVariable($iconRight);
    $modular = parseBladewindVariable($modular);
@endphp
{{-- format-ignore-end --}}

@once
    <x-bladewind::dropmenu :modular="$modular" icon_right="{{$iconRight}}">
        <x-slot:trigger>
            <div class="p-1.5 rounded-lg text-gray-500 hover:text-gray-700 dark:text-dark-300 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-dark-700 transition flex items-center justify-center cursor-pointer" title="Pilih Tema (Terang / Gelap / Sistem)">
                <x-bladewind::icon
                        name="{{$lightIcon}}"
                        type="{{$iconType}}"
                        dir="{{$iconDir}}"
                        class="size-5 text-amber-500 stroke-2 theme-light hidden {{$class}}"/>
                <x-bladewind::icon
                        name="{{$darkIcon}}"
                        type="{{$iconType}}"
                        dir="{{$iconDir}}"
                        class="size-5 text-indigo-400 stroke-2 theme-dark hidden {{$class}}"/>
                <x-bladewind::icon
                        name="{{$systemIcon}}"
                        type="{{$iconType}}"
                        dir="{{$iconDir}}"
                        class="size-5 text-gray-500 dark:text-dark-300 stroke-2 theme-system hidden {{$class}}"/>
            </div>
        </x-slot:trigger>
        <x-bladewind::dropmenu.item data-bw-theme="light" icon="{{$lightIcon}}" icon_css="stroke-2">
            {{$lightText}}
        </x-bladewind::dropmenu.item>
        <x-bladewind::dropmenu.item data-bw-theme="dark" icon="{{$darkIcon}}" icon_css="stroke-2">
            {{$darkText}}
        </x-bladewind::dropmenu.item>
        <x-bladewind::dropmenu.item data-bw-theme="system" icon="{{$systemIcon}}" icon_css="stroke-2">
            {{$systemText}}
        </x-bladewind::dropmenu.item>
    </x-bladewind::dropmenu>
    <x-bladewind::script :nonce="$nonce">
        const chooseTheme = (theme) => {
            theme = (theme !== 'null' && theme !== undefined && theme !== null) ? theme : 'system';
            addToStorage('theme', theme);

            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (theme === 'light') {
                document.documentElement.classList.remove('dark');
            } else if (theme === 'system') {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }

            hide('.theme-dark');
            hide('.theme-light');
            hide('.theme-system');
            changeCss('.theme-dark','inline-block','remove');
            changeCss('.theme-light','inline-block','remove');
            changeCss('.theme-system','inline-block','remove');
            unhide(`.theme-${theme}`);
            changeCss(`.theme-${theme}`, 'inline-block');

            domEls('[data-bw-theme]')?.forEach((item) => {
                const isActive = item.getAttribute('data-bw-theme') === theme;
                item.classList.toggle('text-primary-600', isActive);
                item.classList.toggle('dark:text-primary-400', isActive);
                item.classList.toggle('font-semibold', isActive);
            });
        };

        /* delegated rather than three inline onclicks, so a strict CSP does not
           disable theme switching. see #608 */
        bwOn('click', '[data-bw-theme]', (el) => chooseTheme(el.getAttribute('data-bw-theme')));

        chooseTheme(getFromStorage('theme'));

        // Listen for changes in the system theme
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (getFromStorage('theme') === 'system') {
                chooseTheme('system');
            }
        });
    </x-bladewind::script>
@endonce