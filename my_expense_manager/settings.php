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

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: " . $supabase_key,
        "Authorization: Bearer " . $supabase_key,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ]);

    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        json_encode($data)
    );

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

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: " . $supabase_key,
        "Authorization: Bearer " . $supabase_key,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ]);

    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        json_encode($data)
    );

    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response, true);
}


/* =====================================================
   LOAD USER SETTINGS
===================================================== */

$settingsData = supabase_get(
    "/rest/v1/settings?user_id=eq." . $user_id . "&select=*"
);


/* =====================================================
   DEFAULT SETTINGS
===================================================== */

if (is_array($settingsData) && count($settingsData) > 0) {

    $settings = $settingsData[0];

} else {

    $settings = [

        "user_id" => $user_id,
        "currency" => "INR",
        "theme" => "Light",
        "notification" => true

    ];

}


/* =====================================================
   SAVE SETTINGS
===================================================== */

if (isset($_POST['save_settings'])) {

    $currency = $_POST['currency'] ?? "INR";

    $theme = $_POST['theme'] ?? "Light";

    $notification = isset($_POST['notification']);


    /* ---------------------------------------------
       CHECK IF SETTINGS ALREADY EXIST
    --------------------------------------------- */

    $existing = supabase_get(
        "/rest/v1/settings?user_id=eq." . $user_id . "&select=id"
    );


    /* ---------------------------------------------
       UPDATE EXISTING SETTINGS
    --------------------------------------------- */

    if (is_array($existing) && count($existing) > 0) {

        $setting_id = $existing[0]['id'];

        $updateData = [

            "currency" => $currency,

            "theme" => $theme,

            "notification" => $notification

        ];


        $result = supabase_patch(
            "/rest/v1/settings?id=eq." . $setting_id,
            $updateData
        );


        if (is_array($result)) {

            $message = '
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                Settings updated successfully.
            </div>
            ';

        } else {

            $message = '
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                Failed to update settings.
            </div>
            ';
        }


    }

    /* ---------------------------------------------
       CREATE SETTINGS
    --------------------------------------------- */

    else {

        $newSettings = [

            "user_id" => $user_id,

            "currency" => $currency,

            "theme" => $theme,

            "notification" => $notification

        ];


        $result = supabase_post(
            "/rest/v1/settings",
            $newSettings
        );


        if (is_array($result)) {

            $message = '
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                Settings saved successfully.
            </div>
            ';

        } else {

            $message = '
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                Failed to save settings.
            </div>
            ';
        }

    }


    /* ---------------------------------------------
       LOAD SETTINGS AGAIN
    --------------------------------------------- */

    $settingsData = supabase_get(
        "/rest/v1/settings?user_id=eq." . $user_id . "&select=*"
    );


    if (is_array($settingsData) && count($settingsData) > 0) {

        $settings = $settingsData[0];

    }

}


/* =====================================================
   HEADER
===================================================== */

include("includes/header.php");

include("includes/sidebar.php");

?>


<!-- BOOTSTRAP ICONS -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">


<style>

/* =====================================================
   PAGE
===================================================== */

body {

    background:#f4f7fb;

}


.settings-page {

    padding:25px;

}


/* =====================================================
   HEADER
===================================================== */

.settings-banner {

    background:
    linear-gradient(
        135deg,
        #4f46e5,
        #9333ea
    );

    padding:35px;

    border-radius:25px;

    color:white;

    box-shadow:
    0 15px 35px rgba(79,70,229,.25);

    margin-bottom:25px;

}


.settings-banner h1 {

    font-weight:800;

    margin-bottom:8px;

}


.settings-banner p {

    margin:0;

    opacity:.85;

}


/* =====================================================
   CARD
===================================================== */

.settings-card {

    background:white;

    border-radius:22px;

    overflow:hidden;

    box-shadow:
    0 10px 30px rgba(0,0,0,.08);

    transition:.3s;

}


.settings-card:hover {

    transform:translateY(-4px);

}


/* =====================================================
   CARD HEADER
===================================================== */

.settings-card-header {

    padding:20px;

    background:
    linear-gradient(
        135deg,
        #4f46e5,
        #6366f1
    );

    color:white;

    font-size:20px;

    font-weight:700;

}


.settings-card-header.purple {

    background:
    linear-gradient(
        135deg,
        #9333ea,
        #ec4899
    );

}


.settings-card-header.blue {

    background:
    linear-gradient(
        135deg,
        #0891b2,
        #2563eb
    );

}


/* =====================================================
   BODY
===================================================== */

.settings-card-body {

    padding:30px;

}


/* =====================================================
   SETTING ROW
===================================================== */

.setting-row {

    display:flex;

    align-items:center;

    gap:18px;

    padding:18px;

    background:#f8fafc;

    border-radius:18px;

    margin-bottom:18px;

}


.setting-icon {

    width:48px;

    height:48px;

    border-radius:14px;

    display:flex;

    align-items:center;

    justify-content:center;

    background:#eef2ff;

    color:#4f46e5;

    font-size:21px;

    flex-shrink:0;

}


.setting-row label {

    display:block;

    font-weight:700;

    margin-bottom:6px;

}


.setting-row p {

    margin:0;

    color:#6b7280;

    font-size:14px;

}


/* =====================================================
   SELECT
===================================================== */

.form-select {

    border-radius:12px;

    padding:11px;

    border:1px solid #e5e7eb;

}


.form-select:focus {

    border-color:#6366f1;

    box-shadow:
    0 0 0 4px rgba(99,102,241,.12);

}


/* =====================================================
   SAVE BUTTON
===================================================== */

.save-button {

    height:52px;

    border:none;

    border-radius:15px;

    background:
    linear-gradient(
        135deg,
        #4f46e5,
        #9333ea
    );

    color:white;

    font-weight:700;

    font-size:16px;

    transition:.3s;

}


.save-button:hover {

    color:white;

    transform:translateY(-3px);

}


/* =====================================================
   QUICK LINKS
===================================================== */

.quick-link {

    display:flex;

    align-items:center;

    gap:15px;

    padding:16px;

    background:#f8fafc;

    border-radius:15px;

    margin-bottom:12px;

    text-decoration:none;

    color:#374151;

    font-weight:600;

    transition:.3s;

}


.quick-link:hover {

    background:#eef2ff;

    color:#374151;

    transform:translateX(5px);

}


.quick-link i {

    font-size:22px;

    color:#4f46e5;

}


.quick-link.logout i {

    color:#ef4444;

}


/* =====================================================
   ABOUT
===================================================== */

.about-icon {

    width:60px;

    height:60px;

    border-radius:18px;

    display:flex;

    align-items:center;

    justify-content:center;

    background:#eef2ff;

    color:#4f46e5;

    font-size:28px;

    margin-bottom:15px;

}


.version {

    display:inline-block;

    background:#eef2ff;

    color:#4f46e5;

    padding:8px 15px;

    border-radius:20px;

    font-weight:700;

    margin-top:10px;

}


/* =====================================================
   ALERT
===================================================== */

.alert {

    border:none;

    border-radius:15px;

    font-weight:600;

}


/* =====================================================
   MOBILE
===================================================== */

@media(max-width:768px) {

    .settings-page {

        padding:15px;

    }

    .settings-banner {

        padding:25px;

        border-radius:20px;

    }

    .settings-banner h1 {

        font-size:26px;

    }

    .settings-card-body {

        padding:20px;

    }

    .setting-row {

        align-items:flex-start;

    }

}

</style>


<div class="container-fluid settings-page">


<!-- =====================================================
     SETTINGS HEADER
===================================================== -->

<div class="settings-banner">

    <h1>

        <i class="bi bi-gear-fill"></i>

        Settings

    </h1>

    <p>

        Manage your expense manager preferences.

    </p>

</div>


<!-- MESSAGE -->

<?php echo $message; ?>


<div class="row g-4 mt-1">


<!-- =====================================================
     PREFERENCES
===================================================== -->

<div class="col-lg-7">

    <div class="settings-card">


        <div class="settings-card-header">

            <i class="bi bi-sliders"></i>

            Preferences

        </div>


        <div class="settings-card-body">


            <form method="POST">


                <!-- CURRENCY -->

                <div class="setting-row">

                    <div class="setting-icon">

                        <i class="bi bi-currency-rupee"></i>

                    </div>


                    <div class="flex-grow-1">

                        <label>
                            Currency
                        </label>


                        <select
                            name="currency"
                            class="form-select"
                        >

                            <option
                                value="INR"
                                <?php
                                if (
                                    ($settings['currency'] ?? 'INR')
                                    == "INR"
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                Indian Rupee (₹)
                            </option>


                            <option
                                value="USD"
                                <?php
                                if (
                                    ($settings['currency'] ?? '')
                                    == "USD"
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                US Dollar ($)
                            </option>


                            <option
                                value="EUR"
                                <?php
                                if (
                                    ($settings['currency'] ?? '')
                                    == "EUR"
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                Euro (€)
                            </option>

                        </select>

                    </div>

                </div>


                <!-- THEME -->

                <div class="setting-row">

                    <div class="setting-icon">

                        <i class="bi bi-palette-fill"></i>

                    </div>


                    <div class="flex-grow-1">

                        <label>
                            Theme
                        </label>


                        <select
                            name="theme"
                            class="form-select"
                        >

                            <option
                                value="Light"
                                <?php
                                if (
                                    ($settings['theme'] ?? 'Light')
                                    == "Light"
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                Light Mode
                            </option>


                            <option
                                value="Dark"
                                <?php
                                if (
                                    ($settings['theme'] ?? '')
                                    == "Dark"
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                Dark Mode
                            </option>

                        </select>

                    </div>

                </div>


                <!-- NOTIFICATIONS -->

                <div class="setting-row">

                    <div class="setting-icon">

                        <i class="bi bi-bell-fill"></i>

                    </div>


                    <div class="flex-grow-1">

                        <label>
                            Notifications
                        </label>


                        <p>
                            Receive reminders and updates.
                        </p>

                    </div>


                    <div class="form-check form-switch">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="notification"
                            style="width:45px;height:22px;"
                            <?php

                            if (
                                !empty($settings['notification'])
                                &&
                                (
                                    $settings['notification'] == 1
                                    ||
                                    $settings['notification'] === true
                                )
                            ) {

                                echo "checked";

                            }

                            ?>
                        >

                    </div>

                </div>


                <!-- SAVE -->

                <button
                    type="submit"
                    name="save_settings"
                    class="save-button w-100"
                >

                    <i class="bi bi-save-fill"></i>

                    Save Settings

                </button>


            </form>


        </div>

    </div>

</div>


<!-- =====================================================
     RIGHT SIDE
===================================================== -->

<div class="col-lg-5">


    <!-- ACCOUNT -->

    <div class="settings-card mb-4">


        <div class="settings-card-header purple">

            <i class="bi bi-person-circle"></i>

            Account

        </div>


        <div class="settings-card-body">


            <a
                href="profile.php"
                class="quick-link"
            >

                <i class="bi bi-person-fill"></i>

                Edit Profile

            </a>


            <a
                href="profile.php"
                class="quick-link"
            >

                <i class="bi bi-lock-fill"></i>

                Change Password

            </a>


            <a
                href="auth/logout.php"
                class="quick-link logout"
            >

                <i class="bi bi-box-arrow-right"></i>

                Logout

            </a>


        </div>

    </div>



    <!-- ABOUT -->

    <div class="settings-card">


        <div class="settings-card-header blue">

            <i class="bi bi-info-circle-fill"></i>

            About

        </div>


        <div class="settings-card-body">


            <div class="about-icon">

                <i class="bi bi-wallet2"></i>

            </div>


            <h4>

                My Expense Manager

            </h4>


            <p class="text-muted">

                Track your income, expenses,
                balance and savings from one
                simple dashboard.

            </p>


            <span class="version">

                Version 1.0

            </span>


        </div>

    </div>


</div>


</div>


</div>


<?php

include("includes/footer.php");

?>