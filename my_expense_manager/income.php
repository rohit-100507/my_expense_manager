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

/* ==========================================
   ADD INCOME
========================================== */

if (isset($_POST['add_income'])) {

    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];
    $income_date = $_POST['income_date'];
    $description = $_POST['description'];

    if ($category_id == "" || $amount == "" || $income_date == "") {

        $message = "Please fill all required fields.";
        $messageType = "danger";

    } else {

        $data = [
            "user_id" => (int)$user_id,
            "category_id" => (int)$category_id,
            "amount" => (float)$amount,
            "income_date" => $income_date,
            "description" => $description
        ];

        $ch = curl_init($supabase_url . "/rest/v1/income");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: " . $supabase_key,
            "Authorization: Bearer " . $supabase_key,
            "Content-Type: application/json",
            "Prefer: return=minimal"
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {

            $message = "Income added successfully!";
            $messageType = "success";

        } else {

            $message = "Failed to add income.<br><small>"
                     . htmlspecialchars($response)
                     . "</small>";

            $messageType = "danger";
        }
    }
}


/* ==========================================
   GET INCOME CATEGORIES
========================================== */

$ch = curl_init(
    $supabase_url .
    "/rest/v1/categories?type=eq.Income&select=id,category_name&order=category_name.asc"
);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: " . $supabase_key,
    "Authorization: Bearer " . $supabase_key
]);

$categoryResponse = curl_exec($ch);
curl_close($ch);

$categories = json_decode($categoryResponse, true);

if (!is_array($categories)) {
    $categories = [];
}


/* ==========================================
   GET USER INCOME
========================================== */

$incomeUrl =
    $supabase_url .
    "/rest/v1/income" .
    "?user_id=eq." . urlencode($user_id) .
    "&select=id,amount,income_date,description,category_id,categories(category_name)" .
    "&order=income_date.desc,id.desc";

$ch = curl_init($incomeUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: " . $supabase_key,
    "Authorization: Bearer " . $supabase_key
]);

$incomeResponse = curl_exec($ch);

$incomeHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

$incomeList = json_decode($incomeResponse, true);

if (!is_array($incomeList)) {
    $incomeList = [];
}


/* ==========================================
   TOTAL INCOME
========================================== */

$totalIncome = 0;

foreach ($incomeList as $income) {

    $totalIncome += (float)($income['amount'] ?? 0);
}


include("includes/header.php");
include("includes/sidebar.php");

?>

<div class="container-fluid py-4">

    <!-- PAGE TITLE -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold">
                Income
            </h2>

            <p class="text-muted mb-0">
                Add and manage your income
            </p>

        </div>

        <div class="income-total">

            <small>Total Income</small>

            <h3>
                ₹ <?php echo number_format($totalIncome, 2); ?>
            </h3>

        </div>

    </div>


    <!-- MESSAGE -->

    <?php if ($message != "") { ?>

        <div class="alert alert-<?php echo $messageType; ?>">

            <?php echo $message; ?>

        </div>

    <?php } ?>


    <div class="row">


        <!-- ADD INCOME -->

        <div class="col-lg-4 mb-4">

            <div class="card shadow border-0 rounded-4">

                <div class="card-header bg-success text-white rounded-top-4">

                    <h5 class="mb-0">

                        <i class="bi bi-plus-circle"></i>

                        Add Income

                    </h5>

                </div>


                <div class="card-body p-4">

                    <form method="POST">


                        <!-- CATEGORY -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Income Category

                            </label>

                            <select
                                name="category_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Category
                                </option>

                                <?php foreach ($categories as $category) { ?>

                                    <option value="<?php echo $category['id']; ?>">

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

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Amount

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    name="amount"
                                    class="form-control"
                                    placeholder="Enter amount"
                                    min="0.01"
                                    step="0.01"
                                    required
                                >

                            </div>

                        </div>


                        <!-- DATE -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Income Date

                            </label>

                            <input
                                type="date"
                                name="income_date"
                                class="form-control"
                                value="<?php echo date('Y-m-d'); ?>"
                                required
                            >

                        </div>


                        <!-- DESCRIPTION -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                Description

                            </label>

                            <textarea
                                name="description"
                                class="form-control"
                                rows="3"
                                placeholder="Optional description"
                            ></textarea>

                        </div>


                        <!-- BUTTON -->

                        <button
                            type="submit"
                            name="add_income"
                            class="btn btn-success w-100"
                        >

                            <i class="bi bi-plus-circle"></i>

                            Add Income

                        </button>


                    </form>

                </div>

            </div>

        </div>


        <!-- INCOME HISTORY -->

        <div class="col-lg-8 mb-4">

            <div class="card shadow border-0 rounded-4">

                <div class="card-header bg-white">

                    <h5 class="mb-0">

                        <i class="bi bi-clock-history"></i>

                        Income History

                    </h5>

                </div>


                <div class="card-body">


                    <?php if (count($incomeList) > 0) { ?>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead>

                                    <tr>

                                        <th>#</th>

                                        <th>Category</th>

                                        <th>Amount</th>

                                        <th>Date</th>

                                        <th>Description</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php

                                    $number = 1;

                                    foreach ($incomeList as $income) {

                                    ?>

                                        <tr>

                                            <td>
                                                <?php echo $number++; ?>
                                            </td>


                                            <td>

                                                <?php

                                                echo htmlspecialchars(
                                                    $income['categories']['category_name']
                                                    ?? 'Unknown'
                                                );

                                                ?>

                                            </td>


                                            <td>

                                                <strong class="text-success">

                                                    ₹
                                                    <?php
                                                    echo number_format(
                                                        (float)$income['amount'],
                                                        2
                                                    );
                                                    ?>

                                                </strong>

                                            </td>


                                            <td>

                                                <?php

                                                echo date(
                                                    "d M Y",
                                                    strtotime($income['income_date'])
                                                );

                                                ?>

                                            </td>


                                            <td>

                                                <?php

                                                echo htmlspecialchars(
                                                    $income['description']
                                                    ?? '-'
                                                );

                                                ?>

                                            </td>

                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    <?php } else { ?>

                        <div class="text-center py-5">

                            <i
                                class="bi bi-wallet2"
                                style="font-size:50px;color:#adb5bd;"
                            ></i>

                            <h5 class="mt-3">
                                No income found
                            </h5>

                            <p class="text-muted">

                                Add your first income using the form.

                            </p>

                        </div>

                    <?php } ?>


                </div>

            </div>

        </div>

    </div>

</div>


<style>

.income-total {

    background:linear-gradient(
        135deg,
        #16a34a,
        #22c55e
    );

    color:white;

    padding:15px 25px;

    border-radius:15px;

    text-align:right;

    box-shadow:0 8px 20px rgba(22,163,74,.25);

}

.income-total small {

    opacity:.9;

}

.income-total h3 {

    margin:3px 0 0;

    font-weight:700;

}

.card {

    overflow:hidden;

}

.table th {

    white-space:nowrap;

}

</style>