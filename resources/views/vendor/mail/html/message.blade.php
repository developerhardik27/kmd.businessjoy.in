<x-mail::layout>
    {{-- Header --}}
    <x-slot name="header">
        <x-mail::header :url="config('app.url')">
            <img src="{{ asset('admin/images/bjlogo3.png') }}" alt="Business Joy" style="max-width: 60%;">
        </x-mail::header>
    </x-slot>

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        <x-slot:subcopy>
            <x-mail::subcopy>
                {{ $subcopy }}
            </x-mail::subcopy>
        </x-slot:subcopy>
    @endisset

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>
