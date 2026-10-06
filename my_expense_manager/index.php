<?php

session_start();

/* ==========================================
   CHECK IF USER IS LOGGED IN
========================================== */

if (isset($_SESSION['user_id'])) {

    header("Location: dashboard.php");
    exit();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Expense Manager</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">


    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
        }

        .hero {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #4f46e5,
                    #7c3aed
                );

            padding: 30px;
        }

        .hero-card {
            background: white;

            max-width: 850px;
            width: 100%;

            border-radius: 25px;

            padding: 60px 40px;

            text-align: center;

            box-shadow:
                0 25px 60px rgba(0,0,0,.2);
        }

        .logo-icon {
            width: 90px;
            height: 90px;

            margin: auto;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 25px;

            background:
                linear-gradient(
                    135deg,
                    #4f46e5,
                    #9333ea
                );

            color: white;

            font-size: 45px;

            margin-bottom: 25px;
        }

        .hero-card h1 {
            font-size: 42px;
            font-weight: 800;
            color: #1f2937;
        }

        .hero-card p {
            color: #6b7280;
            font-size: 18px;
            max-width: 650px;
            margin: 20px auto;
            line-height: 1.7;
        }

        .btn-custom {
            padding: 14px 30px;

            border-radius: 12px;

            font-weight: 700;

            font-size: 16px;

            margin: 8px;

            text-decoration: none;
        }

        .btn-login {
            background: #4f46e5;
            color: white;
        }

        .btn-login:hover {
            background: #3730a3;
            color: white;
        }

        .btn-register {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-register:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .features {
            margin-top: 45px;
        }

        .feature {
            padding: 20px;
        }

        .feature i {
            font-size: 35px;
            color: #4f46e5;
        }

        .feature h5 {
            margin-top: 15px;
            font-weight: 700;
        }

        .feature p {
            font-size: 14px;
            margin: 10px auto;
        }

        @media(max-width:768px) {

            .hero-card {
                padding: 40px 20px;
            }

            .hero-card h1 {
                font-size: 32px;
            }

            .hero-card p {
                font-size: 16px;
            }

        }

    </style>

</head>


<body>


<div class="hero">


    <div class="hero-card">


        <!-- LOGO -->

        <div class="logo-icon">

            <i class="bi bi-wallet2"></i>

        </div>


        <!-- TITLE -->

        <h1>

            My Expense Manager

        </h1>


        <!-- DESCRIPTION -->

        <p>

            Manage your money smartly.
            Track every income and expense,
            view detailed reports, and stay
            financially organized.

        </p>


        <!-- BUTTONS -->

        <div>

            <a
                href="auth/login.php"
                class="btn btn-custom btn-login">

                <i class="bi bi-box-arrow-in-right"></i>

                Login

            </a>


            <a
                href="auth/register.php"
                class="btn btn-custom btn-register">

                <i class="bi bi-person-plus-fill"></i>

                Create Account

            </a>

        </div>


        <!-- FEATURES -->

        <div class="row features">


            <!-- INCOME -->

            <div class="col-md-4 feature">

                <i class="bi bi-cash-stack"></i>

                <h5>

                    Track Income

                </h5>

                <p>

                    Easily record and manage
                    all your income.

                </p>

            </div>


            <!-- EXPENSE -->

            <div class="col-md-4 feature">

                <i class="bi bi-credit-card"></i>

                <h5>

                    Track Expenses

                </h5>

                <p>

                    Keep your daily expenses
                    organized.

                </p>

            </div>


            <!-- REPORT -->

            <div class="col-md-4 feature">

                <i class="bi bi-bar-chart-fill"></i>

                <h5>

                    View Reports

                </h5>

                <p>

                    Understand your financial
                    performance easily.

                </p>

            </div>


        </div>


        <hr class="my-4">


        <small class="text-muted">

            © <?php echo date("Y"); ?> My Expense Manager

        </small>


    </div>


</div>


</body>

</html>