<?php

session_start();
include("../database/supabase.php");
$message = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Supabase users API
    $url = $supabase_url . "/rest/v1/users?email=eq." . urlencode($email) . "&select=*";

    // Create cURL request
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    // Check cURL error
    if ($response === false) {

        $message = "Unable to connect to Supabase.";

    } else {

        // Convert JSON response to PHP array
        $users = json_decode($response, true);

        if (!empty($users) && is_array($users)) {

            $user = $users[0];

            // Check password
            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                curl_close($ch);

                header("Location: ../index.php");
                exit();

            } else {

                $message = "Incorrect Password!";

            }

        } else {

            $message = "Email not found!";

        }
    }

    curl_close($ch);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Personal Expense Tracker</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{

height:100vh;

background:linear-gradient(135deg,#2563eb,#7c3aed);

display:flex;

justify-content:center;

align-items:center;

overflow:hidden;

}

body::before{

content:"";

position:absolute;

width:600px;

height:600px;

background:rgba(255,255,255,.08);

border-radius:50%;

top:-250px;

left:-200px;

}

body::after{

content:"";

position:absolute;

width:500px;

height:500px;

background:rgba(255,255,255,.08);

border-radius:50%;

bottom:-220px;

right:-150px;

}

.main{

width:1100px;

height:650px;

display:flex;

border-radius:25px;

overflow:hidden;

background:#fff;

box-shadow:0 30px 70px rgba(0,0,0,.25);

position:relative;

z-index:10;

}

.left{

width:45%;

background:linear-gradient(180deg,#1d4ed8,#2563eb);

padding:60px;

color:#fff;

display:flex;

flex-direction:column;

justify-content:center;

}

.left i{

font-size:80px;

margin-bottom:30px;

}

.left h1{

font-size:45px;

font-weight:700;

line-height:55px;

margin-bottom:20px;

}

.left p{

font-size:18px;

line-height:32px;

opacity:.9;

}

.right{

width:55%;

padding:60px;

display:flex;

align-items:center;

justify-content:center;

background:#f8fafc;

}

.card-box{

width:100%;

max-width:420px;

}

.card-box h2{

font-weight:700;

color:#2563eb;

margin-bottom:30px;

text-align:center;

}

.form-control{

height:55px;

border-radius:12px;

}

.input-group-text{

border-radius:12px 0 0 12px;

}

.btn-login{

height:55px;

border-radius:12px;

background:#2563eb;

color:white;

font-weight:600;

transition:.3s;

}

.btn-login:hover{

background:#1d4ed8;

transform:translateY(-2px);

}

.link{

text-decoration:none;

font-weight:600;

}

@media(max-width:900px){

.main{

width:95%;

height:auto;

flex-direction:column;

}

.left{

width:100%;

text-align:center;

}

.right{

width:100%;

}

}

</style>

</head>

<body>

<div class="main">

<div class="left">

<i class="fa-solid fa-wallet"></i>

<h1>Personal Expense Tracker</h1>

<p>

Manage your money smartly.

Track every income and expense,

view reports,

and stay financially organized.

</p>

</div>

<div class="right">

<div class="card-box">

<h2>Welcome Back</h2>

<?php if($message != "") { ?>

<div class="alert alert-danger">

<?php echo htmlspecialchars($message); ?>

</div>

<?php } ?>

<form method="POST">

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

<div class="mb-4">

<label>Password</label>

<div class="input-group">

<span class="input-group-text">

<i class="fa-solid fa-lock"></i>

</span>

<input
type="password"
id="password"
name="password"
class="form-control"
placeholder="Enter Password"
required>

<button
class="btn btn-outline-secondary"
type="button"
onclick="togglePassword()">

<i class="fa-solid fa-eye"></i>

</button>

</div>

</div>

<div class="d-flex justify-content-between align-items-center mb-4">

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
id="remember">

<label
class="form-check-label"
for="remember">

Remember Me

</label>

</div>

<a href="#" class="link">

Forgot Password?

</a>

</div>

<button
type="submit"
name="login"
class="btn btn-login w-100">

<i class="fa-solid fa-right-to-bracket"></i>

Login

</button>

</form>

<div class="text-center mt-4">

<p>

Don't have an account?

<a href="register.php" class="link">

Create Account

</a>

</p>

</div>

</div>

</div>

</div>

<script>

function togglePassword(){

    let password = document.getElementById("password");

    if(password.type === "password"){

        password.type = "text";

    }else{

        password.type = "password";

    }

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>