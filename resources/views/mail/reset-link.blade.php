<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,300;0,700;1,200;1,300;1,400;1,500;1,600&display=swap');

        body {
            background-color: #acacac;
            font-family: "Jost";
        }

        .card {
            font-family: 'Jost', sans-serif !important;
            padding: 50px 60px;
            background-color: #f8f8f8;
            display: block;
            text-align: center;
            /* Center the content inside the card */
        }

        .highlight {
            font-weight: 600;
            font-size: 24px;
            line-height: 110%;
            color: #1e2029;
            margin-bottom: 11px;
            width: 100%;
        }

        .text {
            width: 100%;
            font-weight: 400;
            font-size: 16px;
            line-height: 130%;
            color: #1e2029;
            margin-bottom: 22px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #28a745;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 auto;
        }

        @media only screen and (max-width: 475px) {
            .card {
                border-radius: 0px;
                padding: 50px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="highlight">Reset Password</div>
        <div class="text">Click this button to reset your password:</div>
        <a href="{{ url('password/reset', $token) }}" class="button">Reset Password</a>
    </div>
</body>

</html>
