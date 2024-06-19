<!doctype html>
<html class="no-js" lang="de-DE">
<head>
    <!--
    ======================================================================================
    == Lovingly brought to you by mindtwo GmbH (http://www.mindtwo.de/) ==================

     _____ ______    ___   ________    ________   _________   ___       __    ________
    |\   _ \  _   \ |\  \ |\   ___  \ |\   ___ \ |\___   ___\|\  \     |\  \ |\   __  \
    \ \  \\\__\ \  \\ \  \\ \  \\ \  \\ \  \_|\ \\|___ \  \_|\ \  \    \ \  \\ \  \|\  \
     \ \  \\|__| \  \\ \  \\ \  \\ \  \\ \  \ \\ \    \ \  \  \ \  \  __\ \  \\ \  \\\  \
      \ \  \    \ \  \\ \  \\ \  \\ \  \\ \  \_\\ \    \ \  \  \ \  \|\__\_\  \\ \  \\\  \
       \ \__\    \ \__\\ \__\\ \__\\ \__\\ \_______\    \ \__\  \ \____________\\ \_______\
        \|__|     \|__| \|__| \|__| \|__| \|_______|     \|__|   \|____________| \|_______|

    =======================================================================================
    == Hi awesome developer! ==============================================================
    == You want to join our nerd-cave and deploy state of the art frontend applications? ==
    == Then take a look at our jobs page at http://www.mindtwo.de/team/jetzt-bewerben/ ====
    =======================================================================================
    -->

    <title>Missing translations | {{ config('app.name', 'Laravel') }}</title>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center mt-20">
            {{ __('Missing translations') }}
        </h2>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8 space-y-4 border py-6">
                <h3 class="font-semibold text-lg text-gray-800 leading-tight">{{ __('Filter') }}:</h3>

                {{-- Toggle to only show missing translations --}}
                <div class="flex items-center">
                    @if (request()->has('only_missing'))
                    <a href="{{ request()->fullUrlWithQuery(['only_missing' => null]) }}" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-blue-200 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2" role="switch" aria-checked="false">
                        <span class="sr-only">Show All Translations</span>
                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-5"></span>
                    </a>
                    @else
                    <a href="{{ request()->fullUrlWithQuery(['only_missing' => true]) }}" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-200 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2" role="switch" aria-checked="false">
                        <span class="sr-only">Only Show Missing Translations</span>
                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"></span>
                    </a>
                    @endif
                    <span class="ml-3 text-sm" id="annual-billing-label">
                        <span class="font-medium text-gray-900">{{ __('Only Show Missing Translations') }}</span>
                    </span>
                </div>

                <div>
                    {{-- Create links to exclude locales --}}
                    <h4 class="font-semibold text-sm text-gray-800 leading-tight">{{ __('Shown Locales') }}:</h4>
                    <div>
                        @foreach($locales as $locale)
                        <a href="{{ route('missing-translations.show', ['exclude' => array_merge($excluded, [$locale])]) }}" class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            {{ $locale }} &times;
                        </a>
                        @endforeach
                        {{-- Empty state --}}
                        @if(count($locales) === 0)
                        <span class="text-gray-500">{{ __('None') }}</span>
                        @endif
                    </div>
                </div>

                <div>
                    {{-- Create links to include locales --}}
                    <h4 class="font-semibold text-sm text-gray-800 leading-tight">{{ __('Hidden Locales') }}:</h4>
                    <div>
                        @foreach($excluded as $locale)
                        <a href="{{ route('missing-translations.show', ['exclude' => array_diff($excluded, [$locale])]) }}" class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            {{ $locale }} &times;
                        </a>
                        @endforeach
                        {{-- Empty state --}}
                        @if(count($excluded) === 0)
                        <span class="text-gray-500">{{ __('None') }}</span>
                        @endif
                    </div>
                </div>

                <div>
                    <a href="{{ route('missing-translations.show') }}" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        {{ __('Reset Filter') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <table class="w-full">
                            <thead class="border-b">
                                <tr>
                                    @foreach($table['header'] as $header)
                                    <th class="text-left py-3 px-4 whitespace-nowrap">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($table['rows'] as $row)
                                <tr>
                                    @foreach($row as $value)

                                    @if(!is_array($value))
                                    @php($style = (empty($value) ? ' class="bg-red-100 py-3 px-4"' : 'class="py-3 px-4"'))
                                    <td {!! $style !!}>{{ $value }}</td>
                                    @else
                                    <td>[]</td>
                                    @endif
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</body>
</html>
