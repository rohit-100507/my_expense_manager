<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

include("database/supabase.php");

$user_id = $_SESSION['user_id'];

$message = "";
$messageType = "";


/* =====================================================
   SUPABASE GET FUNCTION
===================================================== */

function supabase_get($endpoint)
{
    global $supabase_url, $supabase_key;

    $ch = curl_init($supabase_url . $endpoint);

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


/* =====================================================
   SUPABASE POST FUNCTION
===================================================== */

function supabase_post($endpoint, $data)
{
    global $supabase_url, $supabase_key;

    $ch = curl_init($supabase_url . $endpoint);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: " . $supabase_key,
        "Authorization: Bearer " . $supabase_key,
        "Content-Type: application/json",
        "Prefer: return=minimal"
    ]);

    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        json_encode($data)
    );

    $response = curl_exec($ch);

    $httpCode = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    return [
        "code" => $httpCode,
        "response" => $response
    ];
}


/* =====================================================
   ADD EXPENSE
===================================================== */

if (isset($_POST['add_expense'])) {

    $category_id = $_POST['category_id'] ?? "";
    $amount = $_POST['amount'] ?? "";
    $expense_date = $_POST['expense_date'] ?? "";
    $description = $_POST['description'] ?? "";

    if (
        $category_id == "" ||
        $amount == "" ||
        $expense_date == ""
    ) {

        $message = "Please fill all required fields.";
        $messageType = "danger";

    } else {

        $data = [

            "user_id" => (int)$user_id,

            "category_id" => (int)$category_id,

            "amount" => (float)$amount,

            "expense_date" => $expense_date,

            "description" => $description

        ];


        $result = supabase_post(
            "/rest/v1/expenses",
            $data
        );


        if (
            $result["code"] >= 200 &&
            $result["code"] < 300
        ) {

            $message = "Expense Added Successfully.";
            $messageType = "success";

        } else {

            $message =
                "Failed to add expense.<br><small>" .
                htmlspecialchars($result["response"]) .
                "</small>";

            $messageType = "danger";
        }
    }
}


/* =====================================================
   GET EXPENSE CATEGORIES
===================================================== */

$categories = supabase_get(
    "/rest/v1/categories" .
    "?type=eq.Expense" .
    "&select=id,category_name" .
    "&order=category_name.asc"
);

if (!is_array($categories)) {

    $categories = [];

}


/* =====================================================
   GET ALL USER EXPENSES
===================================================== */

$expenseList = supabase_get(
    "/rest/v1/expenses" .
    "?user_id=eq." . urlencode($user_id) .
    "&select=id,amount,expense_date,description,category_id,categories(category_name)" .
    "&order=id.desc"
);

if (!is_array($expenseList)) {

    $expenseList = [];

}


/* =====================================================
   SEARCH
===================================================== */

$search = "";

if (
    isset($_GET['search']) &&
    trim($_GET['search']) != ""
) {

    $search = trim($_GET['search']);

    $filteredExpenses = [];

    foreach ($expenseList as $expense) {

        $description =
            strtolower(
                $expense['description'] ?? ""
            );

        if (
            strpos(
                $description,
                strtolower($search)
            ) !== false
        ) {

            $filteredExpenses[] = $expense;

        }
    }

    $expenseList = $filteredExpenses;
}


/* =====================================================
   TOTAL EXPENSE
===================================================== */

$totalExpenseValue = 0;

foreach ($expenseList as $expense) {

    $totalExpenseValue +=
        (float)($expense['amount'] ?? 0);

}


/* =====================================================
   TOTAL RECORDS
===================================================== */

$totalRecords = count($expenseList);


/* =====================================================
   TODAY'S EXPENSE
===================================================== */

$todayExpense = 0;

$today = date("Y-m-d");

foreach ($expenseList as $expense) {

    if (
        ($expense['expense_date'] ?? "") == $today
    ) {

        $todayExpense +=
            (float)($expense['amount'] ?? 0);

    }
}


/* =====================================================
   HEADER + SIDEBAR
===================================================== */

include("includes/header.php");

include("includes/sidebar.php");

?>


<div class="container-fluid py-4">


<!-- MESSAGE -->

<?php if ($message != "") { ?>

<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">

    <?php echo $message; ?>

    <button
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

<?php } ?>


<!-- PAGE HEADER -->

<div
class="card border-0 shadow-lg mb-4"
style="
border-radius:20px;
background:linear-gradient(135deg,#dc3545,#ff758c);
">

<div class="card-body p-4 text-white">

<div class="row align-items-center">

<div class="col-md-8">

<h2 class="fw-bold mb-2">

<i class="bi bi-wallet2"></i>

Expense Management

</h2>

<p class="mb-0">

Track every rupee you spend.

</p>

</div>

<div class="col-md-4 text-end">

<i
class="bi bi-cash-stack"
style="font-size:70px;opacity:.35;">
</i>

</div>

</div>

</div>

</div>


<!-- SUMMARY CARDS -->

<div class="row mb-4">


<!-- TOTAL EXPENSE -->

<div class="col-lg-4">

<div class="card border-0 shadow h-100">

<div class="card-body">

<h6 class="text-muted">

Total Expense

</h6>

<h2 class="text-danger fw-bold">

₹ <?php

echo number_format(
    $totalExpenseValue,
    2
);

?>

</h2>

</div>

</div>

</div>


<!-- TRANSACTIONS -->

<div class="col-lg-4">

<div class="card border-0 shadow h-100">

<div class="card-body">

<h6 class="text-muted">

Transactions

</h6>

<h2 class="fw-bold">

<?php echo $totalRecords; ?>

</h2>

</div>

</div>

</div>


<!-- TODAY -->

<div class="col-lg-4">

<div class="card border-0 shadow h-100">

<div class="card-body">

<h6 class="text-muted">

Today's Date

</h6>

<h2 class="fw-bold">

<?php echo date("d M Y"); ?>

</h2>

</div>

</div>

</div>


</div>


<!-- ADD EXPENSE -->

<div
class="card border-0 shadow-lg mb-4"
style="border-radius:20px;">

<div
class="card-header text-white"
style="
background:linear-gradient(135deg,#ff512f,#dd2476);
border-radius:20px 20px 0 0;
">

<h4 class="mb-0">

<i class="bi bi-plus-circle-fill"></i>

Add New Expense

</h4>

</div>


<div class="card-body p-4">


<form method="POST">


<div class="row">


<!-- CATEGORY -->

<div class="col-md-6 mb-4">

<label class="form-label fw-semibold">

Category

</label>


<select
name="category_id"
class="form-select form-select-lg"
required>

<option value="">

Choose Category

</option>


<?php foreach ($categories as $category) { ?>

<option
value="<?php echo $category['id']; ?>">

<?php

echo htmlspecialchars(
    $category['category_name']
);

?>

</option>

<?php } ?>


</select>

</div>


<!-- AMOUNT -->

<div class="col-md-6 mb-4">

<label class="form-label fw-semibold">

Amount

</label>


<div class="input-group input-group-lg">

<span class="input-group-text">

₹

</span>


<input
type="number"
step="0.01"
min="0.01"
name="amount"
class="form-control"
placeholder="Enter Amount"
required>

</div>

</div>


<!-- DATE -->

<div class="col-md-6 mb-4">

<label class="form-label fw-semibold">

Expense Date

</label>


<input
type="date"
name="expense_date"
class="form-control form-control-lg"
value="<?php echo date('Y-m-d'); ?>"
required>

</div>


<!-- DESCRIPTION -->

<div class="col-md-6 mb-4">

<label class="form-label fw-semibold">

Description

</label>


<input
type="text"
name="description"
class="form-control form-control-lg"
placeholder="Example: Lunch, Petrol, Shopping">

</div>


</div>


<div class="text-end">


<button
type="reset"
class="btn btn-outline-secondary rounded-pill px-4">

Reset

</button>


<button
type="submit"
name="add_expense"
class="btn btn-danger rounded-pill px-5">

<i class="bi bi-plus-circle"></i>

Add Expense

</button>


</div>


</form>


</div>

</div>


<!-- SEARCH + TODAY -->

<div class="row mb-4">


<div class="col-lg-8">

<form method="GET">

<div class="input-group input-group-lg shadow-sm">

<span class="input-group-text bg-white">

<i class="bi bi-search"></i>

</span>


<input
type="text"
class="form-control"
name="search"
placeholder="Search by description..."
value="<?php echo htmlspecialchars($search); ?>">


<button
class="btn btn-primary"
type="submit">

Search

</button>


</div>

</form>

</div>


<div class="col-lg-4">

<div class="card border-0 shadow-sm">

<div class="card-body">

<small class="text-muted">

Today's Expense

</small>


<h3 class="text-danger fw-bold mb-0">

₹ <?php

echo number_format(
    $todayExpense,
    2
);

?>

</h3>

</div>

</div>

</div>


</div>


<!-- EXPENSE TABLE -->

<div class="card border-0 shadow-lg">


<div
class="card-header bg-dark text-white d-flex justify-content-between align-items-center">


<h4 class="mb-0">

<i class="bi bi-table"></i>

Expense Records

</h4>


<span class="badge bg-danger">

<?php echo $totalRecords; ?>

Records

</span>


</div>


<div class="table-responsive">


<table class="table table-hover align-middle mb-0">


<thead class="table-light">

<tr>

<th>ID</th>

<th>Category</th>

<th>Amount</th>

<th>Date</th>

<th>Description</th>

</tr>

</thead>


<tbody>


<?php if (count($expenseList) > 0) { ?>


<?php foreach ($expenseList as $row) { ?>


<tr>


<td>

<?php echo htmlspecialchars($row['id']); ?>

</td>


<td>

<span class="badge bg-primary">

<?php

echo htmlspecialchars(
    $row['categories']['category_name']
    ?? 'Unknown'
);

?>

</span>

</td>


<td class="fw-bold text-danger">

₹ <?php

echo number_format(
    (float)$row['amount'],
    2
);

?>

</td>


<td>

<?php

echo date(
    "d M Y",
    strtotime($row['expense_date'])
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['description']
    ?? '-'
);

?>

</td>


</tr>


<?php } ?>


<?php } else { ?>


<tr>

<td
colspan="5"
class="text-center p-5">


<i
class="bi bi-wallet2 text-secondary"
style="font-size:60px;">
</i>


<h4 class="mt-3">

No Expense Found

</h4>


<p class="text-muted">

Start by adding your first expense.

</p>


</td>

</tr>


<?php } ?>


</tbody>


</table>


</div>


</div>


</div>


<style>

.card {

    overflow:hidden;

}

.table th {

    white-space:nowrap;

}

</style>