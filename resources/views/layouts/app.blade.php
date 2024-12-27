<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name', 'Task App') }}</title>

    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 font-sans antialiased">
    <main class="container flex justify-center flex-col items-center mx-auto mt-8">
      
        <h1 class="text-5xl font-bold">Task App</h1>
        @yield('content')
    </main>
</body>
</html>
 