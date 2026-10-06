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
   REPORT FILTER
====================================================== */

$from = isset($_GET['from']) && $_GET['from'] != ""
    ? $_GET['from']
    : date("Y-m-01");

$to = isset($_GET['to']) && $_GET['to'] != ""
    ? $_GET['to']
    : date("Y-m-d");


/* ======================================================
   GET INCOME
====================================================== */

$incomeData = supabase_get(
    "/rest/v1/income?select=*&user_id=eq." .
    urlencode($user_id) .
    "&income_date=gte." . urlencode($from) .
    "&income_date=lte." . urlencode($to) .
    "&order=income_date.desc"
);

$totalIncome = 0;

if (is_array($incomeData)) {

    foreach ($incomeData as $income) {

        $totalIncome += (float)($income['amount'] ?? 0);

    }

}


/* ======================================================
   GET EXPENSE
====================================================== */

$expenseData = supabase_get(
    "/rest/v1/expenses?select=*&user_id=eq." .
    urlencode($user_id) .
    "&expense_date=gte." . urlencode($from) .
    "&expense_date=lte." . urlencode($to) .
    "&order=expense_date.desc"
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
   SAVING RATE
====================================================== */

if ($totalIncome > 0) {

    $savingRate = ($balance / $totalIncome) * 100;

} else {

    $savingRate = 0;

}


/* ======================================================
   HIGHEST INCOME
====================================================== */

$highestIncomeAmount = 0;

if (is_array($incomeData) && count($incomeData) > 0) {

    foreach ($incomeData as $income) {

        $amount = (float)($income['amount'] ?? 0);

        if ($amount > $highestIncomeAmount) {

            $highestIncomeAmount = $amount;

        }

    }

}


/* ======================================================
   HIGHEST EXPENSE
====================================================== */

$highestExpenseAmount = 0;

if (is_array($expenseData) && count($expenseData) > 0) {

    foreach ($expenseData as $expense) {

        $amount = (float)($expense['amount'] ?? 0);

        if ($amount > $highestExpenseAmount) {

            $highestExpenseAmount = $amount;

        }

    }

}


/* ======================================================
   TRANSACTIONS
====================================================== */

$totalIncomeTransactions = is_array($incomeData)
    ? count($incomeData)
    : 0;

$totalExpenseTransactions = is_array($expenseData)
    ? count($expenseData)
    : 0;

$totalTransactions =
    $totalIncomeTransactions +
    $totalExpenseTransactions;


/* ======================================================
   FINANCIAL STATUS
====================================================== */

if ($balance > 0) {

    $status = "Profit";
    $statusColor = "success";

}
elseif ($balance < 0) {

    $status = "Loss";
    $statusColor = "danger";

}
else {

    $status = "No Profit / No Loss";
    $statusColor = "secondary";

}


/* ======================================================
   CATEGORY DATA
====================================================== */

$categoryData = supabase_get(
    "/rest/v1/categories?select=id,category_name"
);

$categories = [];

if (is_array($categoryData)) {

    foreach ($categoryData as $category) {

        $categories[$category['id']] =
            $category['category_name'];

    }

}


/* ======================================================
   HEADER
====================================================== */

include("includes/header.php");

include("includes/sidebar.php");

?>


<style>

body{
    background:#f4f6f9;
}

.report-title{
    font-size:30px;
    font-weight:700;
    color:#2c3e50;
}

.filter-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
}

.filter-header{
    background:linear-gradient(135deg,#4e73df,#224abe);
    color:#fff;
    padding:18px;
}

.filter-body{
    padding:25px;
}

.form-control{
    border-radius:10px;
}

.btn-report{
    background:#4e73df;
    color:white;
    border:none;
    border-radius:10px;
    padding:10px;
    transition:.3s;
}

.btn-report:hover{
    background:#224abe;
    color:white;
}

.icon-box{

    width:60px;
    height:60px;
    border-radius:15px;

    display:flex;
    justify-content:center;
    align-items:center;

    color:white;
    font-size:28px;

}

.card{

    transition:.35s;
    border-radius:18px;

}

.card:hover{

    transform:translateY(-6px);

}

.card-income{

    border-left:6px solid #198754;

}

.card-expense{

    border-left:6px solid #dc3545;

}

.card-balance{

    border-left:6px solid #0d6efd;

}

.progress{

    border-radius:20px;

}

.progress-bar{

    border-radius:20px;

}

.table td,
.table th{

    vertical-align:middle;

}

.table tbody tr{

    transition:.25s;

}

.table tbody tr:hover{

    background:#f8f9fa;

    transform:scale(1.005);

}

.table thead th{

    font-weight:700;

}

.btn-lg{

    padding:12px 25px;

    border-radius:12px;

    font-weight:600;

    transition:.3s;

}

.btn-lg:hover{

    transform:translateY(-3px);

}


/* ==========================================
   PRINT
========================================== */

@media print{

    .sidebar,
    .navbar,
    .btn,
    .card-header,
    footer{

        display:none !important;

    }

    body{

        background:#fff !important;

    }

    .card{

        border:none !important;

        box-shadow:none !important;

        margin-bottom:20px;

    }

    .container-fluid{

        width:100%;

        margin:0;

        padding:0;

    }

}


/* ==========================================
   ANIMATION
========================================== */

.card{

    animation:fadeUp .5s ease;

}

@keyframes fadeUp{

    from{

        opacity:0;
        transform:translateY(15px);

    }

    to{

        opacity:1;
        transform:translateY(0);

    }

}


/* ==========================================
   SCROLLBAR
========================================== */

::-webkit-scrollbar{

    width:8px;

}

::-webkit-scrollbar-thumb{

    background:#4e73df;
    border-radius:20px;

}

::-webkit-scrollbar-track{

    background:#f2f2f2;

}

</style>


<div class="container-fluid py-4">


<!-- ======================================================
     PAGE TITLE
====================================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="report-title">

            Financial Report Dashboard

        </h2>

        <small class="text-muted">

            View your income, expenses and financial performance.

        </small>

    </div>

</div>


<!-- ======================================================
     FILTER
====================================================== -->

<div class="card shadow filter-card mb-4">

    <div class="filter-header">

        <h5 class="mb-0">

            <i class="bi bi-funnel-fill"></i>

            Report Filter

        </h5>

    </div>


    <div class="filter-body">

        <form method="GET">

            <div class="row g-3">

                <div class="col-md-5">

                    <label class="form-label fw-bold">

                        From Date

                    </label>

                    <input
                        type="date"
                        name="from"
                        class="form-control"
                        value="<?php echo htmlspecialchars($from); ?>"
                        required>

                </div>


                <div class="col-md-5">

                    <label class="form-label fw-bold">

                        To Date

                    </label>

                    <input
                        type="date"
                        name="to"
                        class="form-control"
                        value="<?php echo htmlspecialchars($to); ?>"
                        required>

                </div>


                <div class="col-md-2 d-grid">

                    <label>&nbsp;</label>

                    <button
                        type="submit"
                        class="btn btn-report">

                        <i class="bi bi-search"></i>

                        Generate

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


<!-- ======================================================
     SUMMARY CARDS
====================================================== -->

<div class="row g-4 mb-4">


<!-- INCOME -->

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0 h-100 card-income">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<small class="text-muted fw-bold">

TOTAL INCOME

</small>

<h3 class="mt-2 text-success fw-bold">

₹ <?php echo number_format($totalIncome,2); ?>

</h3>

</div>

<div class="icon-box bg-success">

<i class="bi bi-arrow-down-circle-fill"></i>

</div>

</div>

</div>

</div>

</div>


<!-- EXPENSE -->

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0 h-100 card-expense">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<small class="text-muted fw-bold">

TOTAL EXPENSE

</small>

<h3 class="mt-2 text-danger fw-bold">

₹ <?php echo number_format($totalExpense,2); ?>

</h3>

</div>

<div class="icon-box bg-danger">

<i class="bi bi-arrow-up-circle-fill"></i>

</div>

</div>

</div>

</div>

</div>


<!-- BALANCE -->

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0 h-100 card-balance">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<small class="text-muted fw-bold">

CURRENT BALANCE

</small>

<h3 class="mt-2 text-primary fw-bold">

₹ <?php echo number_format($balance,2); ?>

</h3>

</div>

<div class="icon-box bg-primary">

<i class="bi bi-wallet2"></i>

</div>

</div>

</div>

</div>

</div>


<!-- SAVING RATE -->

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0 h-100">

<div class="card-body">

<small class="text-muted fw-bold">

SAVING RATE

</small>

<h3 class="text-warning fw-bold mt-2">

<?php echo number_format($savingRate,1); ?>%

</h3>

<div class="progress mt-3" style="height:8px;">

<div
class="progress-bar bg-warning"
style="width:<?php echo min(max($savingRate,0),100); ?>%">

</div>

</div>

</div>

</div>

</div>

</div>


<!-- ======================================================
     ANALYTICS
====================================================== -->

<div class="row g-4 mb-5">


<!-- HIGHEST INCOME -->

<div class="col-lg-3 col-md-6">

<div class="card border-0 shadow h-100">

<div class="card-body text-center">

<i class="bi bi-trophy-fill text-success fs-1"></i>

<h6 class="mt-3">

Highest Income

</h6>

<h4 class="fw-bold text-success">

₹ <?php echo number_format($highestIncomeAmount,2); ?>

</h4>

</div>

</div>

</div>


<!-- HIGHEST EXPENSE -->

<div class="col-lg-3 col-md-6">

<div class="card border-0 shadow h-100">

<div class="card-body text-center">

<i class="bi bi-cash-stack text-danger fs-1"></i>

<h6 class="mt-3">

Highest Expense

</h6>

<h4 class="fw-bold text-danger">

₹ <?php echo number_format($highestExpenseAmount,2); ?>

</h4>

</div>

</div>

</div>


<!-- TRANSACTIONS -->

<div class="col-lg-3 col-md-6">

<div class="card border-0 shadow h-100">

<div class="card-body text-center">

<i class="bi bi-receipt-cutoff text-primary fs-1"></i>

<h6 class="mt-3">

Transactions

</h6>

<h3 class="fw-bold">

<?php echo $totalTransactions; ?>

</h3>

<small class="text-muted">

Income :
<?php echo $totalIncomeTransactions; ?>

|

Expense :
<?php echo $totalExpenseTransactions; ?>

</small>

</div>

</div>

</div>


<!-- STATUS -->

<div class="col-lg-3 col-md-6">

<div class="card border-0 shadow h-100">

<div class="card-body text-center">

<i class="bi bi-graph-up-arrow text-<?php echo $statusColor; ?> fs-1"></i>

<h6 class="mt-3">

Financial Status

</h6>

<h3 class="text-<?php echo $statusColor; ?> fw-bold">

<?php echo $status; ?>

</h3>

</div>

</div>

</div>

</div>


<!-- ======================================================
     INCOME REPORT
====================================================== -->

<div class="card shadow border-0 mb-4">

<div class="card-header bg-success text-white py-3">

<div class="d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="bi bi-cash-stack"></i>

Income Report

</h5>

<span class="badge bg-light text-success fs-6">

<?php echo $totalIncomeTransactions; ?>

Records

</span>

</div>

</div>


<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-success">

<tr>

<th>ID</th>

<th>Date</th>

<th>Category</th>

<th>Description</th>

<th class="text-end">

Amount

</th>

</tr>

</thead>


<tbody>

<?php

if(
is_array($incomeData) &&
count($incomeData) > 0
){

foreach($incomeData as $row){

$categoryName =
$categories[$row['category_id'] ?? '']
?? "Unknown";

?>


<tr>

<td>

<span class="badge bg-primary">

#<?php echo htmlspecialchars($row['id'] ?? ''); ?>

</span>

</td>


<td>

<span class="badge bg-light text-dark">

<?php

echo !empty($row['income_date'])
? date(
    "d M Y",
    strtotime($row['income_date'])
)
: "-";

?>

</span>

</td>


<td>

<span class="badge bg-success">

<?php echo htmlspecialchars($categoryName); ?>

</span>

</td>


<td>

<?php

if(empty($row['description'])){

echo "<span class='text-muted'>
No Description
</span>";

}else{

echo htmlspecialchars(
$row['description']
);

}

?>

</td>


<td class="text-end">

<h6 class="text-success fw-bold mb-0">

₹ <?php

echo number_format(
(float)($row['amount'] ?? 0),
2
);

?>

</h6>

</td>

</tr>


<?php

}

}else{

?>

<tr>

<td colspan="5" class="text-center py-5">

<i class="bi bi-inbox fs-1 text-success"></i>

<h5 class="mt-3">

No Income Records Found

</h5>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>


<!-- ======================================================
     EXPENSE REPORT
====================================================== -->

<div class="card shadow border-0 mb-5">

<div class="card-header bg-danger text-white py-3">

<div class="d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="bi bi-wallet2"></i>

Expense Report

</h5>

<span class="badge bg-light text-danger fs-6">

<?php echo $totalExpenseTransactions; ?>

Records

</span>

</div>

</div>


<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-danger">

<tr>

<th>ID</th>

<th>Date</th>

<th>Category</th>

<th>Description</th>

<th class="text-end">

Amount

</th>

</tr>

</thead>


<tbody>

<?php

if(
is_array($expenseData) &&
count($expenseData) > 0
){

foreach($expenseData as $row){

$categoryName =
$categories[$row['category_id'] ?? '']
?? "Unknown";

?>


<tr>

<td>

<span class="badge bg-primary">

#<?php echo htmlspecialchars($row['id'] ?? ''); ?>

</span>

</td>


<td>

<span class="badge bg-light text-dark">

<?php

echo !empty($row['expense_date'])
? date(
    "d M Y",
    strtotime($row['expense_date'])
)
: "-";

?>

</span>

</td>


<td>

<span class="badge bg-danger">

<?php echo htmlspecialchars($categoryName); ?>

</span>

</td>


<td>

<?php

if(empty($row['description'])){

echo "<span class='text-muted'>
No Description
</span>";

}else{

echo htmlspecialchars(
$row['description']
);

}

?>

</td>


<td class="text-end">

<h6 class="text-danger fw-bold mb-0">

₹ <?php

echo number_format(
(float)($row['amount'] ?? 0),
2
);

?>

</h6>

</td>

</tr>


<?php

}

}else{

?>

<tr>

<td colspan="5" class="text-center py-5">

<i class="bi bi-inbox fs-1 text-danger"></i>

<h5 class="mt-3">

No Expense Records Found

</h5>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>


<!-- ======================================================
     CHARTS
====================================================== -->

<div class="row">


<!-- INCOME VS EXPENSE -->

<div class="col-lg-6 mb-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="bi bi-bar-chart-fill"></i>

Income vs Expense

</h5>

</div>

<div class="card-body">

<canvas
id="incomeExpenseChart"
height="250">
</canvas>

</div>

</div>

</div>


<!-- BALANCE -->

<div class="col-lg-6 mb-4">

<div class="card shadow border-0">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="bi bi-pie-chart-fill"></i>

Balance Overview

</h5>

</div>

<div class="card-body">

<canvas
id="balanceChart"
height="250">
</canvas>

</div>

</div>

</div>

</div>


<!-- ======================================================
     EXPORT
====================================================== -->

<div class="row mb-4">

<div class="col-md-12">

<div class="card shadow border-0">

<div class="card-body d-flex flex-wrap justify-content-center gap-3">


<a
href="export_excel.php?from=<?php echo urlencode($from); ?>&to=<?php echo urlencode($to); ?>"
class="btn btn-success btn-lg">

<i class="bi bi-file-earmark-excel-fill"></i>

Export Excel

</a>


<button
onclick="window.print();"
class="btn btn-danger btn-lg">

<i class="bi bi-printer-fill"></i>

Print Report

</button>


</div>

</div>

</div>

</div>


<!-- ======================================================
     FOOTER SUMMARY
====================================================== -->

<div class="card border-0 shadow-sm mb-4">

<div class="card-body text-center">

<h6 class="text-muted mb-2">

Financial Report Summary

</h6>


<small class="text-secondary">

Report Period :

<strong>

<?php echo date(
"d M Y",
strtotime($from)
); ?>

</strong>

-

<strong>

<?php echo date(
"d M Y",
strtotime($to)
); ?>

</strong>

</small>


<br>
<br>


<small class="text-muted">

Generated on

<?php echo date(
"d M Y h:i A"
); ?>

</small>

</div>

</div>


</div>


<!-- ======================================================
     CHART.JS
====================================================== -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

/* ==========================================
   INCOME VS EXPENSE
========================================== */

new Chart(
document.getElementById(
'incomeExpenseChart'
),
{

type:'bar',

data:{

labels:[
'Income',
'Expense'
],

datasets:[{

data:[

<?php echo (float)$totalIncome; ?>,

<?php echo (float)$totalExpense; ?>

],

backgroundColor:[

'#198754',

'#dc3545'

],

borderRadius:10

}]

},

options:{

responsive:true,

plugins:{

legend:{

display:false

}

},

scales:{

y:{

beginAtZero:true

}

}

}

}
);


/* ==========================================
   BALANCE CHART
========================================== */

new Chart(
document.getElementById(
'balanceChart'
),
{

type:'doughnut',

data:{

labels:[

'Expense',

'Balance'

],

datasets:[{

data:[

<?php echo (float)$totalExpense; ?>,

<?php echo max((float)$balance,0); ?>

],

backgroundColor:[

'#dc3545',

'#0d6efd'

],

hoverOffset:10

}]

},

options:{

responsive:true,

plugins:{

legend:{

position:'bottom'

}

}

}

}
);

</script>


<?php

include("includes/footer.php");

?>