<?php

$currentPage = basename($_SERVER['PHP_SELF']);

/*
|--------------------------------------------------------------------------
| Get User Name
|--------------------------------------------------------------------------
| We already stored the user's name during login,
| so we don't need another database query here.
*/

$userName = $_SESSION['user_name'] ?? "User";


/*
|--------------------------------------------------------------------------
| Create Initials
|--------------------------------------------------------------------------
*/

$nameParts = explode(" ", trim($userName));

$initials = "";

foreach ($nameParts as $part) {

    if (!empty($part)) {
        $initials .= strtoupper(substr($part, 0, 1));
    }

}

$initials = substr($initials, 0, 2);

?>

<!-- SIDEBAR -->

<div class="col-lg-2 col-md-3 sidebar-wrapper p-0">

    <div class="premium-sidebar">


        <!-- USER PROFILE -->

        <div class="sidebar-user">

            <div class="sidebar-avatar">

                <?php echo htmlspecialchars($initials); ?>

            </div>

            <h5>

                <?php echo htmlspecialchars($userName); ?>

            </h5>

            <p>

                <i class="bi bi-circle-fill"></i>

                Online

            </p>

        </div>


        <!-- MENU -->

        <ul class="premium-menu">


            <!-- DASHBOARD -->

            <li>

                <a href="dashboard.php"
                   class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">

                    <span class="side-icon">

                        <i class="bi bi-grid-fill"></i>

                    </span>

                    Dashboard

                </a>

            </li>


            <!-- INCOME -->

            <li>

                <a href="income.php"
                   class="<?php echo ($currentPage == 'income.php') ? 'active' : ''; ?>">

                    <span class="side-icon">

                        <i class="bi bi-wallet-fill"></i>

                    </span>

                    Income

                </a>

            </li>


            <!-- EXPENSES -->

            <li>

                <a href="expenses.php"
                   class="<?php echo ($currentPage == 'expenses.php') ? 'active' : ''; ?>">

                    <span class="side-icon">

                        <i class="bi bi-credit-card-fill"></i>

                    </span>

                    Expenses

                </a>

            </li>


            <!-- CATEGORIES -->

            <li>

                <a href="categories.php"
                   class="<?php echo ($currentPage == 'categories.php') ? 'active' : ''; ?>">

                    <span class="side-icon">

                        <i class="bi bi-tags-fill"></i>

                    </span>

                    Categories

                </a>

            </li>


            <!-- REPORTS -->

            <li>

                <a href="reports.php"
                   class="<?php echo ($currentPage == 'reports.php') ? 'active' : ''; ?>">

                    <span class="side-icon">

                        <i class="bi bi-bar-chart-fill"></i>

                    </span>

                    Reports

                </a>

            </li>


            <!-- PROFILE -->

            <li>

                <a href="profile.php"
                   class="<?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">

                    <span class="side-icon">

                        <i class="bi bi-person-fill"></i>

                    </span>

                    Profile

                </a>

            </li>


            <!-- SETTINGS -->

            <li>

                <a href="settings.php"
                   class="<?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>">

                    <span class="side-icon">

                        <i class="bi bi-gear-fill"></i>

                    </span>

                    Settings

                </a>

            </li>


            <!-- LOGOUT -->

            <li class="logout">

                <a href="auth/logout.php">

                    <span class="side-icon">

                        <i class="bi bi-box-arrow-right"></i>

                    </span>

                    Logout

                </a>

            </li>


        </ul>


        <!-- SECURITY CARD -->

        <div class="sidebar-card">

            <i class="bi bi-shield-check"></i>

            <div>

                <h6>

                    Secure Account

                </h6>

                <p>

                    Your data is protected

                </p>

            </div>

        </div>


    </div>

</div>


<!-- CONTENT AREA -->

<div class="col-lg-10 col-md-9 content-area">


<style>

/* =====================================
   PREMIUM SIDEBAR
===================================== */

.sidebar-wrapper {

    height:100vh;

}


.premium-sidebar {

    height:100vh;

    padding:25px 15px;

    background:
    linear-gradient(
        180deg,
        #111827,
        #312e81
    );

    box-shadow:
    10px 0 30px rgba(0,0,0,.15);

    overflow-y:auto;

}


/* USER */

.sidebar-user {

    text-align:center;

    color:white;

    margin-bottom:35px;

}


.sidebar-avatar {

    height:85px;

    width:85px;

    margin:auto;

    border-radius:50%;

    background:
    linear-gradient(
        135deg,
        #6366f1,
        #a855f7
    );

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:32px;

    font-weight:800;

    box-shadow:
    0 10px 30px rgba(168,85,247,.5);

}


.sidebar-user h5 {

    margin-top:15px;

    font-weight:700;

}


.sidebar-user p {

    font-size:13px;

    color:#cbd5e1;

}


.sidebar-user p i {

    font-size:8px;

    color:#22c55e;

}


/* MENU */

.premium-menu {

    list-style:none;

    padding:0;

}


.premium-menu li {

    margin-bottom:8px;

}


.premium-menu a {

    height:52px;

    padding:10px 14px;

    display:flex;

    align-items:center;

    gap:15px;

    color:#e5e7eb;

    text-decoration:none;

    font-weight:600;

    border-radius:16px;

    transition:.35s;

}


.premium-menu a:hover {

    background:
    rgba(255,255,255,.12);

    color:white;

    transform:translateX(7px);

}


/* ACTIVE */

.premium-menu a.active {

    background:
    linear-gradient(
        135deg,
        #6366f1,
        #a855f7
    );

    color:white;

    box-shadow:
    0 10px 25px rgba(99,102,241,.45);

}


/* ICON */

.side-icon {

    height:38px;

    width:38px;

    border-radius:12px;

    background:
    rgba(255,255,255,.12);

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:18px;

}


/* LOGOUT */

.logout {

    margin-top:25px;

}


.logout a {

    background:
    rgba(239,68,68,.15);

}


.logout a:hover {

    background:#ef4444;

}


/* SECURITY CARD */

.sidebar-card {

    margin-top:35px;

    padding:18px;

    border-radius:18px;

    background:
    rgba(255,255,255,.1);

    display:flex;

    gap:12px;

    align-items:center;

    color:white;

}


.sidebar-card i {

    font-size:30px;

    color:#a5b4fc;

}


.sidebar-card h6 {

    margin:0;

    font-weight:700;

}


.sidebar-card p {

    margin:3px 0 0;

    font-size:12px;

    color:#cbd5e1;

}


@media(max-width:991px) {

    .premium-sidebar {

        height:auto;

    }

}

</style>