<!-- ======================================
     MODERN FOOTER
====================================== -->


<footer class="modern-footer">


<div class="container-fluid">



<div class="row gy-4">



<!-- BRAND SECTION -->

<div class="col-lg-4 col-md-6">


<div class="footer-brand">


<div class="footer-logo">

<i class="bi bi-wallet2"></i>

</div>



<div>

<h3>

My Expense Manager

</h3>


<p>

Track your income, control expenses,
and achieve your financial goals smarter.

</p>


</div>


</div>



</div>







<!-- QUICK LINKS -->


<div class="col-lg-4 col-md-6">


<h5 class="footer-title">

Quick Links

</h5>



<div class="footer-links">


<a href="dashboard.php">

<i class="bi bi-speedometer2"></i>

Dashboard

</a>


<a href="income.php">

<i class="bi bi-cash-stack"></i>

Income

</a>


<a href="expenses.php">

<i class="bi bi-credit-card"></i>

Expenses

</a>


<a href="reports.php">

<i class="bi bi-bar-chart"></i>

Reports

</a>


</div>



</div>








<!-- FEATURES -->


<div class="col-lg-4 col-md-12">


<h5 class="footer-title">

Why Use Us?

</h5>


<div class="footer-feature">


<div>

<i class="bi bi-shield-check"></i>

</div>


<p>

Secure financial tracking

</p>


</div>




<div class="footer-feature">


<div>

<i class="bi bi-graph-up-arrow"></i>

</div>


<p>

Better money management

</p>


</div>




<div class="footer-feature">


<div>

<i class="bi bi-clock-history"></i>

</div>


<p>

Save time with reports

</p>


</div>



</div>



</div>





<hr class="footer-divider">






<div class="row align-items-center">


<div class="col-md-6 text-center text-md-start">


<p class="copyright">


©

<span id="year"></span>


<strong>
My Expense Manager
</strong>


</p>


</div>





<div class="col-md-6 text-center text-md-end">


<p class="designer">


Crafted with

<span>
❤️
</span>

by

<strong>
Rohit Malge
</strong>


</p>



</div>



</div>



</div>


</footer>





<style>


/* ======================================
   MODERN FOOTER STYLE
====================================== */


.modern-footer{


background:

linear-gradient(
135deg,
#111827,
#1e1b4b
);


color:white;


padding:50px 25px 25px;


margin-top:50px;


border-radius:25px 25px 0 0;


}





.footer-brand{


display:flex;


align-items:center;


gap:18px;


}



.footer-logo{


height:65px;


width:65px;


border-radius:18px;


background:

linear-gradient(
135deg,
#4f46e5,
#9333ea
);


display:flex;


justify-content:center;


align-items:center;


font-size:32px;


box-shadow:

0 10px 25px rgba(0,0,0,.3);


}



.footer-brand h3{


font-weight:800;


margin-bottom:10px;


}



.footer-brand p{


color:#cbd5e1;


line-height:1.6;


}





.footer-title{


font-size:18px;


font-weight:700;


margin-bottom:20px;


color:white;


}



.footer-links{


display:flex;


flex-direction:column;


gap:12px;


}



.footer-links a{


color:#cbd5e1;


text-decoration:none;


font-weight:500;


transition:.3s;


}



.footer-links a i{


margin-right:10px;


color:#818cf8;


}



.footer-links a:hover{


color:white;


transform:translateX(6px);


}






.footer-feature{


display:flex;


align-items:center;


gap:15px;


margin-bottom:15px;


}



.footer-feature div{


height:40px;


width:40px;


border-radius:12px;


background:rgba(255,255,255,.1);


display:flex;


align-items:center;


justify-content:center;


}



.footer-feature i{


font-size:20px;


color:#a5b4fc;


}



.footer-feature p{


margin:0;


color:#cbd5e1;


}





.footer-divider{


border-color:rgba(255,255,255,.2);


margin:35px 0 20px;


}




.copyright,
.designer{


color:#cbd5e1;


margin:0;


}



.designer span{


font-size:20px;


}




@media(max-width:768px){


.footer-brand{


flex-direction:column;


text-align:center;


}



}



</style>





<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<!-- Custom JS -->

<script src="assets/js/script.js"></script>



<script>

document.getElementById("year").innerHTML =
new Date().getFullYear();

</script>


</body>

</html>