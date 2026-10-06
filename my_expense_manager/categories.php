<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

include("database/supabase.php");

$user_id = $_SESSION['user_id'];

$message = "";


/* =====================================================
   SUPABASE GET FUNCTION
===================================================== */

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


/* =====================================================
   SUPABASE POST FUNCTION
===================================================== */

function supabase_post($endpoint, $data)
{
    global $supabase_url, $supabase_key;

    $url = $supabase_url . $endpoint;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: " . $supabase_key,
        "Authorization: Bearer " . $supabase_key,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response, true);
}


/* =====================================================
   SUPABASE PATCH FUNCTION
===================================================== */

function supabase_patch($endpoint, $data)
{
    global $supabase_url, $supabase_key;

    $url = $supabase_url . $endpoint;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: " . $supabase_key,
        "Authorization: Bearer " . $supabase_key,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response, true);
}


/* =====================================================
   SUPABASE DELETE FUNCTION
===================================================== */

function supabase_delete($endpoint)
{
    global $supabase_url, $supabase_key;

    $url = $supabase_url . $endpoint;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

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
   ADD CATEGORY
===================================================== */

if (isset($_POST['add_category'])) {

    $category_name = trim($_POST['category_name']);
    $type = trim($_POST['category_type']);

    if ($category_name == "" || $type == "") {

        $message = '
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle-fill"></i>
            Please fill all fields.
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>';

    } else {

        /* Check duplicate */

        $checkData = supabase_get(
            "/rest/v1/categories?category_name=eq." .
            urlencode($category_name) .
            "&select=id"
        );

        if (is_array($checkData) && count($checkData) > 0) {

            $message = '
            <div class="alert alert-warning alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Category already exists.
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>';

        } else {

            $insertData = supabase_post(
                "/rest/v1/categories",
                [
                    "category_name" => $category_name,
                    "type" => $type,
                    "category_type" => $type
                ]
            );

            if (is_array($insertData) && isset($insertData[0])) {

                $message = '
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill"></i>
                    Category added successfully.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>';

            } else {

                $message = '
                <div class="alert alert-danger alert-dismissible fade show">
                    Failed to add category.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>';

            }
        }
    }
}


/* =====================================================
   DELETE CATEGORY
===================================================== */

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    supabase_delete(
        "/rest/v1/categories?id=eq." . $id
    );

    header("Location: categories.php");
    exit();
}


/* =====================================================
   EDIT CATEGORY
===================================================== */

if (isset($_POST['update_category'])) {

    $id = (int)$_POST['id'];

    $category_name = trim($_POST['category_name']);
    $type = trim($_POST['category_type']);

    if ($category_name == "" || $type == "") {

        $message = '
        <div class="alert alert-danger alert-dismissible fade show">
            Please fill all fields.
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>';

    } else {

        $updateData = supabase_patch(
            "/rest/v1/categories?id=eq." . $id,
            [
                "category_name" => $category_name,
                "type" => $type,
                "category_type" => $type
            ]
        );

        header("Location: categories.php");
        exit();
    }
}


/* =====================================================
   SEARCH
===================================================== */

$search = "";

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);

}


/* =====================================================
   GET CATEGORIES
===================================================== */

$endpoint = "/rest/v1/categories?select=*&order=id.desc";

if ($search != "") {

    $endpoint .=
        "&category_name=ilike.*" .
        urlencode($search) .
        "*";

}

$categories = supabase_get($endpoint);


/* =====================================================
   TOTAL CATEGORIES
===================================================== */

$totalCategory = 0;

if (is_array($categories)) {

    $totalCategory = count($categories);

}


/* =====================================================
   EDIT DATA
===================================================== */

$editData = null;

if (isset($_GET['edit'])) {

    $editId = (int)$_GET['edit'];

    $editResult = supabase_get(
        "/rest/v1/categories?id=eq." . $editId . "&select=*"
    );

    if (is_array($editResult) && count($editResult) > 0) {

        $editData = $editResult[0];

    }

}


include("includes/header.php");

include("includes/sidebar.php");

?>


<div class="container-fluid mt-4">


    <!-- =================================================
         PAGE HEADER
    ================================================= -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body bg-primary text-white rounded">

            <h2 class="mb-1">

                <i class="bi bi-tags-fill"></i>

                Category Management

            </h2>

            <p class="mb-0">

                Add, Search and Manage Income & Expense Categories

            </p>

        </div>

    </div>


    <!-- MESSAGE -->

    <?php echo $message; ?>


    <!-- =================================================
         ADD CATEGORY
    ================================================= -->

    <div class="card shadow mb-4">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">

                <i class="bi bi-plus-circle"></i>

                Add New Category

            </h5>

        </div>


        <div class="card-body">

            <form method="POST">

                <div class="row">


                    <div class="col-md-5">

                        <label class="form-label fw-bold">

                            Category Name

                        </label>

                        <input
                            type="text"
                            name="category_name"
                            class="form-control"
                            placeholder="Enter Category Name"
                            required>

                    </div>


                    <div class="col-md-5">

                        <label class="form-label fw-bold">

                            Category Type

                        </label>

                        <select
                            name="category_type"
                            class="form-select"
                            required>

                            <option value="">

                                Select Type

                            </option>

                            <option value="Income">

                                Income

                            </option>

                            <option value="Expense">

                                Expense

                            </option>

                        </select>

                    </div>


                    <div class="col-md-2 d-grid">

                        <label>&nbsp;</label>

                        <button
                            type="submit"
                            name="add_category"
                            class="btn btn-success">

                            <i class="bi bi-plus-lg"></i>

                            Add

                        </button>

                    </div>


                </div>

            </form>

        </div>

    </div>


    <!-- =================================================
         SEARCH + TOTAL
    ================================================= -->

    <div class="row mb-4">


        <div class="col-md-8">

            <form method="GET">

                <div class="input-group">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search Category"
                        value="<?php echo htmlspecialchars($search); ?>">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-search"></i>

                        Search

                    </button>

                </div>

            </form>

        </div>


        <div class="col-md-4">

            <div class="alert alert-info text-center">

                <h3>

                    <?php echo $totalCategory; ?>

                </h3>

                <strong>

                    Total Categories

                </strong>

            </div>

        </div>


    </div>


    <!-- =================================================
         CATEGORY TABLE
    ================================================= -->

    <div class="card shadow">

        <div class="card-header bg-dark text-white">

            <h5 class="mb-0">

                <i class="bi bi-list"></i>

                Category List

            </h5>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>ID</th>

                            <th>Category Name</th>

                            <th>Type</th>

                            <th width="170">Action</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    if (is_array($categories) && count($categories) > 0) {

                        foreach ($categories as $row) {

                    ?>

                        <tr>

                            <td>

                                <?php echo htmlspecialchars($row['id']); ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row['category_name']
                                );
                                ?>

                            </td>


                            <td>

                                <?php

                                if ($row['type'] == "Income") {

                                    echo '
                                    <span class="badge bg-success">
                                        Income
                                    </span>';

                                } else {

                                    echo '
                                    <span class="badge bg-danger">
                                        Expense
                                    </span>';

                                }

                                ?>

                            </td>


                            <td>

                                <a
                                    href="categories.php?edit=<?php echo $row['id']; ?>"
                                    class="btn btn-warning btn-sm">

                                    <i class="bi bi-pencil-fill"></i>

                                </a>


                                <a
                                    href="categories.php?delete=<?php echo $row['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this category?');">

                                    <i class="bi bi-trash-fill"></i>

                                </a>

                            </td>

                        </tr>


                    <?php

                        }

                    } else {

                    ?>


                        <tr>

                            <td
                                colspan="4"
                                class="text-center py-4">

                                <i class="bi bi-inbox fs-2 text-muted"></i>

                                <br>

                                No Categories Found

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


</div>


<!-- =====================================================
     EDIT MODAL
===================================================== -->

<?php if ($editData != null) { ?>


<div class="modal fade show"
     style="display:block;background:rgba(0,0,0,.5);">

    <div class="modal-dialog">

        <div class="modal-content">


            <form method="POST">


                <div class="modal-header bg-warning">

                    <h5 class="modal-title">

                        <i class="bi bi-pencil-fill"></i>

                        Edit Category

                    </h5>

                    <a
                        href="categories.php"
                        class="btn-close">
                    </a>

                </div>


                <div class="modal-body">


                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $editData['id']; ?>">


                    <label class="fw-bold">

                        Category Name

                    </label>

                    <input
                        type="text"
                        name="category_name"
                        class="form-control mb-3"
                        value="<?php echo htmlspecialchars($editData['category_name']); ?>"
                        required>


                    <label class="fw-bold">

                        Category Type

                    </label>


                    <select
                        name="category_type"
                        class="form-select">

                        <option
                            value="Income"
                            <?php
                            if ($editData['type'] == "Income") {
                                echo "selected";
                            }
                            ?>>

                            Income

                        </option>


                        <option
                            value="Expense"
                            <?php
                            if ($editData['type'] == "Expense") {
                                echo "selected";
                            }
                            ?>>

                            Expense

                        </option>

                    </select>


                </div>


                <div class="modal-footer">

                    <button
                        type="submit"
                        name="update_category"
                        class="btn btn-success">

                        <i class="bi bi-check-circle"></i>

                        Update

                    </button>


                    <a
                        href="categories.php"
                        class="btn btn-secondary">

                        Cancel

                    </a>

                </div>


            </form>

        </div>

    </div>

</div>


<?php } ?>


<style>

body{

    background:#f4f6f9;

}

.card{

    border-radius:15px;

}

.table td,
.table th{

    vertical-align:middle;

}

.badge{

    font-size:14px;

    padding:8px 12px;

}

.btn{

    border-radius:8px;

}

.modal-content{

    border-radius:15px;

}

</style>


<?php

include("includes/footer.php");

?>