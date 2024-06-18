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
