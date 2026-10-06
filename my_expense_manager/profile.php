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

        "Content-Type: application/json",

        "Accept: application/json"

    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response, true);

}


/* =====================================================
   SUPABASE PATCH FUNCTION
===================================================== */

function supabase_update($endpoint, $data)
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
   GET USER DETAILS
===================================================== */

$userDataArray = supabase_get(
    "/rest/v1/users?select=*&id=eq." . $user_id
);


if (
    !is_array($userDataArray) ||
    count($userDataArray) == 0
) {

    die("User not found.");

}


$userData = $userDataArray[0];


/* =====================================================
   UPDATE PROFILE
===================================================== */

if (isset($_POST['update_profile'])) {

    $name = trim($_POST['name']);

    $email = trim($_POST['email']);


    if ($name == "" || $email == "") {

        $message = '

        <div class="alert alert-danger">

            All fields are required.

        </div>

        ';

    }

    else {

        /*
        Check if email already exists
        */

        $existingUser = supabase_get(

            "/rest/v1/users?select=id&email=eq."
            . urlencode($email)
            . "&id=neq."
            . $user_id

        );


        if (
            is_array($existingUser) &&
            count($existingUser) > 0
        ) {

            $message = '

            <div class="alert alert-warning">

                Email already exists.

            </div>

            ';

        }

        else {

            $updatedUser = supabase_update(

                "/rest/v1/users?id=eq." . $user_id,

                [

                    "name" => $name,

                    "email" => $email

                ]

            );


            if (
                is_array($updatedUser) &&
                count($updatedUser) > 0
            ) {

                $_SESSION['user_name'] = $name;


                $message = '

                <div class="alert alert-success">

                    Profile updated successfully.

                </div>

                ';


                /*
                Refresh user data
                */

                $userDataArray = supabase_get(

                    "/rest/v1/users?select=*&id=eq."
                    . $user_id

                );


                if (
                    is_array($userDataArray) &&
                    count($userDataArray) > 0
                ) {

                    $userData = $userDataArray[0];

                }

            }

            else {

                $message = '

                <div class="alert alert-danger">

                    Failed to update profile.

                </div>

                ';

            }

        }

    }

}


/* =====================================================
   CHANGE PASSWORD
===================================================== */

if (isset($_POST['change_password'])) {

    $current_password =
        $_POST['current_password'];

    $new_password =
        $_POST['new_password'];

    $confirm_password =
        $_POST['confirm_password'];


    /*
    Check current password
    */

    if (
        !isset($userData['password']) ||
        !password_verify(
            $current_password,
            $userData['password']
        )
    ) {

        $message = '

        <div class="alert alert-danger">

            Current password is incorrect.

        </div>

        ';

    }

    elseif (
        $new_password != $confirm_password
    ) {

        $message = '

        <div class="alert alert-warning">

            Passwords do not match.

        </div>

        ';

    }

    elseif (
        strlen($new_password) < 6
    ) {

        $message = '

        <div class="alert alert-warning">

            Password must contain minimum 6 characters.

        </div>

        ';

    }

    else {

        $hash = password_hash(

            $new_password,

            PASSWORD_DEFAULT

        );


        $updatedPassword = supabase_update(

            "/rest/v1/users?id=eq." . $user_id,

            [

                "password" => $hash

            ]

        );


        if (
            is_array($updatedPassword) &&
            count($updatedPassword) > 0
        ) {

            $message = '

            <div class="alert alert-success">

                Password changed successfully.

            </div>

            ';


            /*
            Refresh user data
            */

            $userDataArray = supabase_get(

                "/rest/v1/users?select=*&id=eq."
                . $user_id

            );


            if (
                is_array($userDataArray) &&
                count($userDataArray) > 0
            ) {

                $userData = $userDataArray[0];

            }

        }

        else {

            $message = '

            <div class="alert alert-danger">

                Failed to change password.

            </div>

            ';

        }

    }

}


/* =====================================================
   HEADER
===================================================== */

include("includes/header.php");

include("includes/sidebar.php");

?>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">


<style>

/* =====================================
   BODY
===================================== */

body{

    background:#f4f7fb;

}


/* =====================================
   PROFILE CONTAINER
===================================== */

.profile-container{

    padding:25px;

}


/* =====================================
   PROFILE HEADER
===================================== */

.profile-header{

    background:linear-gradient(
        135deg,
        #4f46e5,
        #9333ea
    );

    border-radius:25px;

    padding:40px;

    color:white;

    display:flex;

    align-items:center;

    gap:25px;

    box-shadow:
    0 15px 35px rgba(79,70,229,.3);

    margin-bottom:30px;

}


.profile-avatar{

    width:100px;

    height:100px;

    background:white;

    color:#4f46e5;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:45px;

    font-weight:800;

}


.profile-header h1{

    font-size:32px;

    font-weight:700;

    margin:0;

}


.profile-header p{

    margin-top:10px;

    opacity:.85;

}


/* =====================================
   CARDS
===================================== */

.profile-card{

    background:white;

    border-radius:22px;

    overflow:hidden;

    box-shadow:

    0 10px 30px rgba(0,0,0,.08);

    transition:.3s;

}


.profile-card:hover{

    transform:translateY(-6px);

    box-shadow:

    0 20px 40px rgba(0,0,0,.15);

}


.card-header-custom{

    padding:20px;

    color:white;

    font-size:20px;

    font-weight:700;

    background:

    linear-gradient(
        135deg,
        #4f46e5,
        #6366f1
    );

}


.password-header{

    background:

    linear-gradient(
        135deg,
        #f59e0b,
        #f97316
    );

}


.summary-header{

    background:

    linear-gradient(
        135deg,
        #0891b2,
        #2563eb
    );

}


.card-content{

    padding:30px;

}


/* =====================================
   FORM
===================================== */

.form-label{

    font-weight:600;

    color:#374151;

}


.form-control{

    height:50px;

    border-radius:14px;

    border:1px solid #e5e7eb;

    padding:12px;

}


.form-control:focus{

    border-color:#6366f1;

    box-shadow:

    0 0 0 4px rgba(99,102,241,.15);

}


/* =====================================
   BUTTONS
===================================== */

.btn-custom{

    height:50px;

    border-radius:14px;

    font-weight:700;

    border:none;

}


.btn-save{

    background:

    linear-gradient(
        135deg,
        #4f46e5,
        #7c3aed
    );

    color:white;

}


.btn-password{

    background:

    linear-gradient(
        135deg,
        #f59e0b,
        #ea580c
    );

    color:white;

}


/* =====================================
   SUMMARY
===================================== */

.summary-box{

    background:#f8fafc;

    border-radius:18px;

    padding:25px;

    text-align:center;

    transition:.3s;

}


.summary-box:hover{

    background:#eef2ff;

    transform:translateY(-5px);

}


.summary-box i{

    font-size:35px;

    color:#4f46e5;

}


.summary-box h6{

    margin-top:15px;

    color:#6b7280;

}


.summary-box h5{

    font-weight:700;

}


/* =====================================
   SECURITY
===================================== */

.security-card{

    background:

    linear-gradient(
        135deg,
        #dbeafe,
        #ede9fe
    );

    padding:20px;

    border-radius:18px;

    margin-top:25px;

}


.alert{

    border-radius:15px;

    border:none;

    font-weight:600;

}


/* =====================================
   MOBILE
===================================== */

@media(max-width:768px){

    .profile-header{

        flex-direction:column;

        text-align:center;

    }


    .profile-header h1{

        font-size:25px;

    }

}

</style>


<div class="container-fluid profile-container">


<!-- =====================================
     PROFILE HEADER
===================================== -->

<div class="profile-header">


<div class="profile-avatar">

<?php

echo strtoupper(

    substr(

        $userData['name'],

        0,

        1

    )

);

?>

</div>


<div>

<h1>

<?php

echo htmlspecialchars(

    $userData['name']

);

?>

</h1>


<p>

<i class="bi bi-envelope-fill"></i>

<?php

echo htmlspecialchars(

    $userData['email']

);

?>

</p>

</div>


</div>


<!-- MESSAGE -->

<?php echo $message; ?>


<div class="row g-4">


<!-- =====================================
     EDIT PROFILE
===================================== -->

<div class="col-lg-6">

<div class="profile-card">


<div class="card-header-custom">

<i class="bi bi-person-circle"></i>

Edit Profile

</div>


<div class="card-content">


<form method="POST">


<label class="form-label">

<i class="bi bi-person"></i>

Full Name

</label>


<input

type="text"

name="name"

class="form-control"

value="<?php

echo htmlspecialchars(

    $userData['name']

);

?>"

required>


<label class="form-label mt-3">

<i class="bi bi-envelope"></i>

Email Address

</label>


<input

type="email"

name="email"

class="form-control"

value="<?php

echo htmlspecialchars(

    $userData['email']

);

?>"

required>


<button

type="submit"

name="update_profile"

class="btn btn-custom btn-save w-100 mt-4"

>

<i class="bi bi-check-circle"></i>

Save Profile

</button>


</form>


</div>

</div>

</div>


<!-- =====================================
     CHANGE PASSWORD
===================================== -->

<div class="col-lg-6">

<div class="profile-card">


<div class="card-header-custom password-header">

<i class="bi bi-shield-lock"></i>

Change Password

</div>


<div class="card-content">


<form method="POST">


<label class="form-label">

Current Password

</label>


<input

type="password"

name="current_password"

class="form-control"

required>


<label class="form-label mt-3">

New Password

</label>


<input

type="password"

name="new_password"

class="form-control"

required>


<label class="form-label mt-3">

Confirm Password

</label>


<input

type="password"

name="confirm_password"

class="form-control"

required>


<button

type="submit"

name="change_password"

class="btn btn-custom btn-password w-100 mt-4"

>

<i class="bi bi-key-fill"></i>

Update Password

</button>


</form>


</div>

</div>

</div>


</div>


<!-- =====================================
     ACCOUNT OVERVIEW
===================================== -->

<div class="profile-card mt-4">


<div class="card-header-custom summary-header">

<i class="bi bi-bar-chart-fill"></i>

Account Overview

</div>


<div class="card-content">


<div class="row g-4">


<!-- USER ID -->

<div class="col-md-3">

<div class="summary-box">

<i class="bi bi-person-badge-fill"></i>

<h6>

User ID

</h6>

<h5>

#<?php

echo htmlspecialchars(

    $userData['id']

);

?>

</h5>

</div>

</div>


<!-- NAME -->

<div class="col-md-3">

<div class="summary-box">

<i class="bi bi-person-fill"></i>

<h6>

Full Name

</h6>

<h5>

<?php

echo htmlspecialchars(

    $userData['name']

);

?>

</h5>

</div>

</div>


<!-- EMAIL -->

<div class="col-md-3">

<div class="summary-box">

<i class="bi bi-envelope-fill"></i>

<h6>

Email

</h6>

<h6>

<?php

echo htmlspecialchars(

    $userData['email']

);

?>

</h6>

</div>

</div>


<!-- JOIN DATE -->

<div class="col-md-3">

<div class="summary-box">

<i class="bi bi-calendar-check-fill"></i>

<h6>

Registered

</h6>

<h5>

<?php

if(

    !empty(

        $userData['created_at']

    )

){

    echo date(

        "d M Y",

        strtotime(

            $userData['created_at']

        )

    );

}

else{

    echo "N/A";

}

?>

</h5>

</div>

</div>


</div>


<!-- SECURITY TIP -->

<div class="security-card">

<h5>

<i class="bi bi-shield-check text-primary"></i>

Account Security

</h5>

<p class="mb-0">

Keep your password private. Use a strong password
and update it regularly to keep your account safe.

</p>

</div>


</div>

</div>


</div>


<?php

include("includes/footer.php");

?>