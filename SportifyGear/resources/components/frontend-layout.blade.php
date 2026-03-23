<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        :root {
            --primary: #D77B31;
            --secondary: #f38925;
            --background: rgb(216, 216, 216);
        }

        .container {
            width: 90%;
            margin: 0 auto;
        }
    </style>
</head>

<body class="bg-(--background)">

    <x-frontend-navbar />

    {{ $slot }}

    <x-frontend-footer />

    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
</body>

</html>
