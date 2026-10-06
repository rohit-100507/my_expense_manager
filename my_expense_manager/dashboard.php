<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: auth/login.php");
    exit();

}

include("database/supabase.php");

$user_id = $_SESSION['user_id'];


/* ======================================================
   SUPABASE GET FUNCTION
====================================================== */

function supabase_get($endpoint)
{
    global $supabase_url, $supabase_key;

    $url = $supabase_url . $endpoint;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [

        "apikey: " . $supabase_key,

        "Authorization: Bearer " . $supabase_key,

        "Content-Type: application/json"

    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response, true);
}


/* ======================================================
   GET TOTAL INCOME
====================================================== */

$incomeData = supabase_get(
    "/rest/v1/income?select=amount&user_id=eq."
    . urlencode($user_id)
);

$totalIncome = 0;

if (is_array($incomeData)) {

    foreach ($incomeData as $income) {

        $totalIncome += (float)($income['amount'] ?? 0);

    }

}


/* ======================================================
   GET TOTAL EXPENSE
====================================================== */

$expenseData = supabase_get(
    "/rest/v1/expenses?select=amount&user_id=eq."
    . urlencode($user_id)
);

$totalExpense = 0;

if (is_array($expenseData)) {

    foreach ($expenseData as $expense) {

        $totalExpense += (float)($expense['amount'] ?? 0);

    }

}


/* ======================================================
   BALANCE
====================================================== */

$balance = $totalIncome - $totalExpense;


/* ======================================================
   USER NAME
====================================================== */

$userName = $_SESSION['user_name'] ?? "User";


/* ======================================================
   INCLUDE HEADER
====================================================== */

include("includes/header.php");

include("includes/sidebar.php");

?>


<style>

/* ==========================================
   PAGE
========================================== */

body{

    background:#eef2f7;

}


/* ==========================================
   WELCOME CARD
========================================== */

.welcome-card{

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #4f46e5
    );

    color:#fff;

    border-radius:20px;

    padding:30px;

    margin-bottom:30px;

    box-shadow:
    0 8px 20px rgba(0,0,0,.12);

}


/* ==========================================
   STAT CARDS
========================================== */

.stat-card{

    border:none;

    border-radius:18px;

    color:#fff;

    transition:.3s;

    box-shadow:
    0 8px 18px rgba(0,0,0,.12);

}


.stat-card:hover{

    transform:translateY(-6px);

}


.income{

    background:#16a34a;

}


.expense{

    background:#dc2626;

}


.balance{

    background:#2563eb;

}


/* ==========================================
   QUICK ACTION
========================================== */

.quick-btn{

    border-radius:12px;

    padding:15px;

    font-weight:600;

    transition:.3s;

}


.quick-btn:hover{

    transform:translateY(-3px);

}


/* ==========================================
   CARD
========================================== */

.quick-card{

    border:none;

    border-radius:18px;

}


/* ==========================================
   ICON
========================================== */

.stat-icon{

    font-size:35px;

    opacity:.8;

}


/* ==========================================
   RESPONSIVE
========================================== */

@media(max-width:768px){

    .welcome-card{

        padding:22px;

    }

    .welcome-card h2{

        font-size:24px;

    }

}

</style>


<div class="container-fluid py-4">


<!-- ======================================================
     WELCOME
====================================================== -->

<div class="welcome-card">

    <h2 class="mb-2">

        Welcome,

        <?php echo htmlspecialchars($userName); ?>

        👋

    </h2>

    <p class="mb-0">

        Manage your income and expenses efficiently.

    </p>

</div>



<!-- ======================================================
     STAT CARDS
====================================================== -->

<div class="row">


<!-- ======================================================
     TOTAL INCOME
====================================================== -->

<div class="col-md-4 mb-4">

    <div class="card stat-card income h-100">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5>

                        Total Income

                    </h5>

                    <h2 class="fw-bold">

                        ₹ <?php

                        echo number_format(
                            $totalIncome,
                            2
                        );

                        ?>

                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-arrow-down-circle-fill"></i>

                </div>

            </div>

        </div>

    </div>

</div>



<!-- ======================================================
     TOTAL EXPENSE
====================================================== -->

<div class="col-md-4 mb-4">

    <div class="card stat-card expense h-100">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5>

                        Total Expense

                    </h5>

                    <h2 class="fw-bold">

                        ₹ <?php

                        echo number_format(
                            $totalExpense,
                            2
                        );

                        ?>

                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-arrow-up-circle-fill"></i>

                </div>

            </div>

        </div>

    </div>

</div>



<!-- ======================================================
     AVAILABLE BALANCE
====================================================== -->

<div class="col-md-4 mb-4">

    <div class="card stat-card balance h-100">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5>

                        Available Balance

                    </h5>

                    <h2 class="fw-bold">

                        ₹ <?php

                        echo number_format(
                            $balance,
                            2
                        );

                        ?>

                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-wallet2"></i>

                </div>

            </div>

        </div>

    </div>

</div>


</div>



<!-- ======================================================
     QUICK ACTIONS
====================================================== -->

<div class="card shadow quick-card">

    <div class="card-header bg-white">

        <h4 class="mb-0">

            Quick Actions

        </h4>

    </div>


    <div class="card-body">

        <div class="row g-3">


            <!-- ADD INCOME -->

            <div class="col-md-4">

                <a
                    href="income.php"
                    class="btn btn-success w-100 quick-btn">

                    <i class="bi bi-plus-circle"></i>

                    Add Income

                </a>

            </div>


            <!-- ADD EXPENSE -->

            <div class="col-md-4">

                <a
                    href="expenses.php"
                    class="btn btn-danger w-100 quick-btn">

                    <i class="bi bi-dash-circle"></i>

                    Add Expense

                </a>

            </div>


            <!-- REPORTS -->

            <div class="col-md-4">

                <a
                    href="reports.php"
                    class="btn btn-primary w-100 quick-btn">

                    <i class="bi bi-bar-chart"></i>

                    Reports

                </a>

            </div>


            <!-- PROFILE -->

            <div class="col-md-6">

                <a
                    href="profile.php"
                    class="btn btn-secondary w-100 quick-btn">

                    <i class="bi bi-person"></i>

                    Profile

                </a>

            </div>


            <!-- SETTINGS -->

            <div class="col-md-6">

                <a
                    href="settings.php"
                    class="btn btn-dark w-100 quick-btn">

                    <i class="bi bi-gear"></i>

                    Settings

                </a>

            </div>


        </div>

    </div>

</div>


</div>


<?php

include("includes/footer.php");

?>