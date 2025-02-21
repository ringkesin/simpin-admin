<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow" />
    <title>Abipraya | Session Expired</title>
    <link rel="icon" type="image/png" href="https://ptba-legacy-cdn.s3.ap-southeast-1.amazonaws.com/common/logo/favicon.png">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>
    .bg-custom {
        background-color: #fcfcfc;

    }
    .custom-font {
        font-family: "Inter", sans-serif;
        font-optical-sizing: auto;
        font-weight: 400;
        font-style: normal;
        font-size: 14px;
    }
    .custom-shadow {
        box-shadow:
            0px 0px 3.8px rgba(0, 0, 0, 0.002),
            0px 0px 8.4px rgba(0, 0, 0, 0.003),
            0px 0px 14.4px rgba(0, 0, 0, 0.004),
            0px 0px 22.2px rgba(0, 0, 0, 0.005),
            0px 0px 32.8px rgba(0, 0, 0, 0.005),
            0px 0px 48.2px rgba(0, 0, 0, 0.006),
            0px 0px 72.3px rgba(0, 0, 0, 0.007),
            0px 0px 115.2px rgba(0, 0, 0, 0.008),
            0px 0px 216px rgba(0, 0, 0, 0.01)
            ;
    }
    </style>
</head>
<body class='overflow-hidden custom-font bg-custom'>
    <div class="container p-4">
        <div class="mx-auto">
            <div class="p-2 mx-auto text-center d-flex justify-content-md-center align-items-center vh-100" style='width:400px;'>
                <div class="border border-0 shadow-lg card rounded-4">
                    <div class="p-4 card-body">
                        <img src="{{asset('/assets/media/icons/access_denied.gif')}}" class='w-25'/>
                        <h6 class='mt-2 text-danger'>Access Denied</h6>
                        <p class='mb-3'>
                            You don't have access, please contact application owner.
                        </p>
                        <a href="{{ route('login') }}" class='btn btn-danger w-100'>Login Page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>