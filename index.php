<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeDiliver AI</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, sans-serif;
        }

        body{
            background:#f4f6f9;
        }

        nav{
            background:#1e293b;
            padding:15px 50px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        nav h2{
            color:white;
        }

        nav a{
            color:white;
            text-decoration:none;
            margin-left:20px;
        }

        .hero{
            text-align:center;
            padding:100px 20px;
        }

        .hero h1{
            font-size:50px;
            color:#1e293b;
            
        }

        .hero p{
            font-size:20px;
            margin-top:15px;
            color:#555;
        }

        .btn{
            display:inline-block;
            margin-top:25px;
            padding:12px 25px;
            background:#2563eb;
            color:white;
            text-decoration:none;
            border-radius:5px;
        }

        .features{
            padding:60px 20px;
            text-align:center;
        }

        .features h2{
            margin-bottom:30px;
        }

        .feature-box{
            display:inline-block;
            width:250px;
            background:white;
            padding:20px;
            margin:10px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        footer{
            background:#1e293b;
            color:white;
            text-align:center;
            padding:20px;
            margin-top:50px;
        }
        
    </style>
    <link rel="stylesheet" href="assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<nav>
    <h2>CodeDiliver AI</h2>

    <div>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>
</nav>

<section class="hero">

    <h1>CodeDiliver AI</h1>

    <h2 id="typing-text"></h2>

    <p>
        Upload. Analyze. Improve.
        <br>
        AI-powered project analysis for developers.
    </p>

    <a href="register.php" class="btn">
        Get Started
    </a>

</section>

<section class="features">

    <h2>Features</h2>

    <div class="feature-box">
        <h3>Bug Detection</h3>
        <p>Find coding issues instantly.</p>
    </div>

    <div class="feature-box">
        <h3>Security Analysis</h3>
        <p>Detect common security risks.</p>
    </div>

    <div class="feature-box">
        <h3>Code Quality Score</h3>
        <p>Measure code quality easily.</p>
    </div>

    <div class="feature-box">
        <h3>Project Scanner</h3>
        <p>Understand project structure quickly.</p>
    </div>

</section>
<script src="assets/js/script.js"></script>

<footer>
    © 2026 CodeDiliver AI. All Rights Reserved.
</footer>

</body>
</html>