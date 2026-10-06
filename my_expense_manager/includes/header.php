<!DOCTYPE html>
<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
My Expense Manager
</title>



<!-- Bootstrap -->

<link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
rel="stylesheet">



<!-- Bootstrap Icons -->

<link 
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">



<!-- Google Font -->

<link 
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" 
rel="stylesheet">



<!-- Chart JS -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



<!-- Custom CSS -->

<link rel="stylesheet" href="assets/css/style.css">



<style>


body{

font-family:'Poppins',sans-serif;

}



/* =====================================
   PREMIUM NAVBAR
===================================== */


.premium-navbar{


background:

linear-gradient(
135deg,
#111827,
#312e81
);


padding:15px 25px;


border-radius:0 0 25px 25px;


box-shadow:

0 15px 35px rgba(0,0,0,.2);


}



.navbar-brand{


color:white!important;


}



/* LOGO */


.logo-box{


width:55px;


height:55px;


border-radius:18px;


display:flex;


align-items:center;


justify-content:center;


background:

linear-gradient(
135deg,
#6366f1,
#a855f7
);


font-size:28px;


color:white;


box-shadow:

0 10px 25px rgba(99,102,241,.5);


transition:.3s;


}



.logo-box:hover{


transform:rotate(10deg) scale(1.05);


}




.brand-title{


font-size:20px;


font-weight:800;


color:white;


}



.brand-subtitle{


font-size:12px;


color:#cbd5e1;


}





/* SEARCH */


.search-container{


max-width:450px;


}



.search-box{


background:rgba(255,255,255,.12);


border-radius:15px;


overflow:hidden;


border:1px solid rgba(255,255,255,.2);


}



.search-box input{


background:transparent;


border:none;


color:white;


height:45px;


}



.search-box input::placeholder{


color:#cbd5e1;


}



.search-box input:focus{


box-shadow:none;


background:transparent;


color:white;


}



.search-icon{


background:transparent;


border:none;


color:#c7d2fe;


}





/* NAV LINKS */


.nav-link{


color:#e5e7eb!important;


transition:.3s;


}



.nav-link:hover{


color:white!important;


transform:translateY(-2px);


}




/* NOTIFICATION */


.notification{


position:relative;


font-size:22px;


}



.notification-count{


position:absolute;


top:-8px;


right:-10px;


background:#ef4444;


color:white;


font-size:11px;


width:20px;


height:20px;


border-radius:50%;


display:flex;


align-items:center;


justify-content:center;


font-weight:700;


animation:pulse 1.5s infinite;


}



@keyframes pulse{


0%{

box-shadow:0 0 0 0 rgba(239,68,68,.7);

}


70%{

box-shadow:0 0 0 10px rgba(239,68,68,0);

}


100%{

box-shadow:0 0 0 0 rgba(239,68,68,0);

}


}





/* PROFILE */


.profile-toggle{


background:rgba(255,255,255,.1);


padding:8px 15px;


border-radius:15px;


}



.profile-image{


width:42px;


height:42px;


border-radius:50%;


border:2px solid white;


}



.dropdown-menu{


border:none;


border-radius:18px;


padding:10px;


}



.dropdown-item{


border-radius:12px;


padding:12px;


font-weight:500;


}



.dropdown-item:hover{


background:#eef2ff;


color:#4f46e5;


}





@media(max-width:991px){


.search-container{


margin:20px 0;


}


}



</style>


</head>



<body>




<!-- ================= PREMIUM NAVBAR ================= -->



<nav class="navbar navbar-expand-lg premium-navbar">



<div class="container-fluid">





<!-- BRAND -->


<a 
class="navbar-brand d-flex align-items-center"
href="dashboard.php">


<div class="logo-box">


<i class="bi bi-wallet-fill"></i>


</div>



<div class="ms-3">


<div class="brand-title">

My Expense Manager

</div>


<div class="brand-subtitle">

Smart Money Tracking

</div>


</div>


</a>



<!-- MOBILE BUTTON -->


<button

class="navbar-toggler text-white"

type="button"

data-bs-toggle="collapse"

data-bs-target="#mainNavbar">


<i class="bi bi-list fs-1"></i>


</button>



<div class="collapse navbar-collapse" id="mainNavbar">



<!-- SEARCH -->


<form class="mx-auto search-container">


<div class="input-group search-box">


<span class="input-group-text search-icon">

<i class="bi bi-search"></i>

</span>


<input

type="search"

class="form-control"

placeholder="Search transactions...">


</div>


</form>




<!-- RIGHT SIDE -->


<ul class="navbar-nav align-items-center ms-auto">



<!-- NOTIFICATION -->


<li class="nav-item me-4">


<a href="#" class="nav-link notification">


<i class="bi bi-bell-fill"></i>


<span class="notification-count">

3

</span>


</a>


</li>


<!-- USER -->


<li class="nav-item dropdown">


<?php

$user_name=$_SESSION['user_name'];

?>



<a

class="nav-link dropdown-toggle d-flex align-items-center profile-toggle"

href="#"

data-bs-toggle="dropdown">


<img

src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=6366f1&color=fff"

class="profile-image">


<span class="ms-2 fw-semibold">

<?php echo htmlspecialchars($user_name); ?>

</span>


</a>


<ul class="dropdown-menu dropdown-menu-end shadow">



<li>


<a class="dropdown-item" href="profile.php">

<i class="bi bi-person-circle me-2"></i>

My Profile

</a>


</li>



<li>


<a class="dropdown-item" href="settings.php">

<i class="bi bi-gear me-2"></i>

Settings

</a>


</li>



<li>

<hr class="dropdown-divider">

</li>



<li>


<a class="dropdown-item text-danger"

href="auth/logout.php">


<i class="bi bi-box-arrow-right me-2"></i>

Logout


</a>


</li>



</ul>


</li>



</ul>

</div>



</div>


</nav>

<!-- MAIN CONTENT -->

<div class="container-fluid mt-4">


<div class="row">