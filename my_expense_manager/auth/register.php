<?php

session_start();

include("../database/db.php");

$message = "";

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check passwords
    if ($password !== $confirm_password) {

        $message = "Passwords do not match!";

    } else {

        // ---------------------------------------
        // Check if email already exists
        // ---------------------------------------

        $check_url = $supabase_url . "/rest/v1/users?email=eq." . urlencode($email) . "&select=id";

        $ch = curl_init($check_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $check_response = curl_exec($ch);

        $check_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($check_response === false) {

            $message = "Could not connect to Supabase.";

        } else {

            $existing_users = json_decode($check_response, true);

            if (!empty($existing_users)) {

                $message = "Email already exists!";

            } else {

                // ---------------------------------------
                // Hash password
                // ---------------------------------------

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // ---------------------------------------
                // User data
                // ---------------------------------------

                $data = [
                    "name" => $name,
                    "email" => $email,
                    "password" => $hashed_password
                ];

                // ---------------------------------------
                // Insert user into Supabase
                // ---------------------------------------

                $insert_url = $supabase_url . "/rest/v1/users";

                $ch = curl_init($insert_url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt(
                    $ch,
                    CURLOPT_POSTFIELDS,
                    json_encode($data)
                );

                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array_merge(
                        $headers,
                        [
                            "Prefer: return=minimal"
                        ]
                    )
                );

                $insert_response = curl_exec($ch);

                $http_code = curl_getinfo(
                    $ch,
                    CURLINFO_HTTP_CODE
                );

                curl_close($ch);

                // ---------------------------------------
                // Check Supabase response
                // ---------------------------------------

                if ($http_code >= 200 && $http_code < 300) {

                    header("Location: login.php");
                    exit();

                } else {

                    $message =
                        "Registration failed!<br><br>" .
                        "HTTP Code: " . $http_code .
                        "<br><br>" .
                        "Supabase Response:<br>" .
                        htmlspecialchars($insert_response);
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Create Account</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#3f5efb,#6a11cb);
    font-family:'Poppins',sans-serif;
}

.register-box{
    width:950px;
    background:#fff;
    border-radius:25px;
    overflow:hidden;
    display:flex;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}

.left-side{
    width:40%;
    background:linear-gradient(180deg,#224abe,#4e73df);
    color:white;
    padding:60px 40px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.left-side i{
    font-size:90px;
    margin-bottom:25px;
}

.left-side h1{
    font-size:50px;
    font-weight:bold;
    line-height:1.2;
}

.left-side p{
    margin-top:25px;
    font-size:20px;
}

.right-side{
    width:60%;
    padding:50px;
}

.right-side h2{
    text-align:center;
    color:#2f5bea;
    font-size:45px;
    font-weight:bold;
    margin-bottom:35px;
}

.form-control{
    height:55px;
}

.input-group-text{
    background:#fff;
}

.btn-register{
    height:55px;
    font-size:20px;
    border-radius:10px;
    background:#2f5bea;
    color:#fff;
    border:none;
}

.btn-register:hover{
    background:#224abe;
}

.link{
    text-decoration:none;
    font-weight:600;
}

@media(max-width:900px){

.register-box{
    flex-direction:column;
    width:95%;
}

.left-side,
.right-side{
    width:100%;
}

.left-side{
    text-align:center;
}

}

</style>

</head>

<body>

<div class="register-box">

<div class="left-side">

<i class="fa-solid fa-wallet"></i>

<h1>
Personal<br>
Expense<br>
Tracker
</h1>

<p>
Create your account and start managing your income,
expenses and monthly budget efficiently.
</p>

</div>

<div class="right-side">

<h2>Create Account</h2>

<?php if($message != "") { ?>

<div class="alert alert-danger">

<?php echo $message; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label>Full Name</label>

<div class="input-group">

<span class="input-group-text">

<i class="fa-solid fa-user"></i>

</span>

<input
type="text"
name="name"
class="form-control"
placeholder="Enter Full Name"
required>

</div>

</div>

<div class="mb-3">

<label>Email Address</label>

<div class="input-group">

<span class="input-group-text">

<i class="fa-solid fa-envelope"></i>

</span>

<input
type="email"
name="email"
class="form-control"
placeholder="Enter Email"
required>

</div>

</div>

<div class="mb-3">

<label>Password</label>

<div class="input-group">

<span class="input-group-text">

<i class="fa-solid fa-lock"></i>

</span>

<input
type="password"
name="password"
id="password"
class="form-control"
placeholder="Enter Password"
required>

<button
type="button"
class="btn btn-outline-secondary"
onclick="togglePassword('password')">

<i class="fa-solid fa-eye"></i>

</button>

</div>

</div>

<div class="mb-4">

<label>Confirm Password</label>

<div class="input-group">

<span class="input-group-text">

<i class="fa-solid fa-lock"></i>

</span>

<input
type="password"
name="confirm_password"
id="confirm"
class="form-control"
placeholder="Confirm Password"
required>

<button
type="button"
class="btn btn-outline-secondary"
onclick="togglePassword('confirm')">

<i class="fa-solid fa-eye"></i>

</button>

</div>

</div>

<button
type="submit"
name="register"
class="btn btn-register w-100">

<i class="fa-solid fa-user-plus"></i>

Create Account

</button>

</form>

<div class="text-center mt-4">

Already have an account?

<a href="login.php" class="link">

Login Here

</a>

</div>

</div>

</div>

<script>

function togglePassword(id){

    let x = document.getElementById(id);

    if(x.type === "password"){

        x.type = "text";

    }
    else{

        x.type = "password";

    }

}

</script>

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>