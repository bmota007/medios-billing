<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { 
            margin: 0.5in; /* Standard printing margins */
        }
        body { 
            margin: 0; 
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #030712 !important;
            color: #ffffff !important;
        }
    </style>
    @stack('page_styles')
</head>
<body>
    @yield('content')
</body>
</html>
