<?php
session_start();
include 'connections.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            
            if ($user['role'] === 'superadmin') {
                header("Location: superadmin_dashboard.php");
            } elseif ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'editor') {
                header("Location: editor_dashboard.php");
            } else {
                header("Location: homepage.php");
            }
            exit();
        } else {
            $error = "Mali ang username o password!";
        }
    } else {
        $error = "Mali ang username o password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>David's Sari-Sari Store - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Pangolin&display=swap" rel="stylesheet">
    <style>
        :root {
            --store-red: #e74c3c;
            --store-brown: #8B4513;
            --store-yellow: #f1c40f;
            --store-green: #27ae60;
            --store-blue: #3498db;
            --store-cream: #f5e9d4;
            --chicherya-pink: #ff9ff3;
            --chicherya-purple: #5f27cd;
            --chicherya-orange: #ff9f43;
        }
        
        body {
            font-family: 'Pangolin', cursive;
            background-color: var(--store-cream);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
              background-image: url('image/4c39b5dd-f58a-48c7-830a-39cf7f9327d3.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            overflow-x: hidden;
        }
        
        .store-wrapper {
            perspective: 1000px;
            position: relative;
            z-index: 1;
        }
        
        .store-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 380px;
            padding: 30px;
            position: relative;
            border: 5px solid var(--store-brown);
            transform-style: preserve-3d;
            animation: float 3s ease-in-out infinite;
            background-image: linear-gradient(to bottom, #fff 85%, #f9f9f9);
            z-index: 10;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotateY(0deg); }
            50% { transform: translateY(-10px) rotateY(2deg); }
        }
        
        /* Enhanced Roof with Tiles */
        .store-roof {
            position: absolute;
            top: -60px;
            left: -25px;
            width: 430px;
            height: 80px;
            background-color: var(--store-red);
            clip-path: polygon(0% 100%, 5% 0%, 95% 0%, 100% 100%);
            z-index: -1;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background-image: 
                linear-gradient(135deg, 
                    rgba(0,0,0,0.1) 25%, 
                    transparent 25%,
                    transparent 50%, 
                    rgba(0,0,0,0.1) 50%,
                    rgba(0,0,0,0.1) 75%,
                    transparent 75%,
                    transparent);
            background-size: 20px 20px;
        }
        
        .store-roof:before {
            content: '';
            position: absolute;
            top: 5px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 60px;
            background-color: var(--store-yellow);
            border-radius: 50%;
            box-shadow: 0 0 10px gold, inset 0 0 10px rgba(0,0,0,0.2);
            border: 3px solid #f39c12;
        }
        
        /* Hanging Chicherya */
        .hanging-chicherya {
            position: absolute;
            top: -120px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-around;
            z-index: 5;
        }
        
        .chicherya-item {
            width: 40px;
            height: 60px;
            background-color: var(--chicherya-pink);
            border-radius: 5px;
            position: relative;
            transform: rotate(5deg);
            box-shadow: 0 5px 0 rgba(0,0,0,0.2);
            animation: swing 3s infinite alternate;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 20px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .chicherya-item:nth-child(2n) {
            background-color: var(--chicherya-purple);
            transform: rotate(-5deg);
            animation-delay: 0.5s;
        }
        
        .chicherya-item:nth-child(3n) {
            background-color: var(--chicherya-orange);
            transform: rotate(3deg);
            animation-delay: 1s;
        }
        
        .chicherya-item:before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 5px;
            height: 10px;
            background-color: #555;
        }
        
        .chicherya-item:after {
            content: '';
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 30px;
            background-color: #888;
        }
        
        @keyframes swing {
            0% { transform: rotate(5deg); }
            100% { transform: rotate(-5deg); }
        }
        
        /* Store Sign with Neon Effect */
        .store-sign {
            background-color: var(--store-red);
            color: white;
            text-align: center;
            padding: 12px;
            margin: -30px -30px 20px -30px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
            font-size: 27px;
            text-shadow: 3px 3px 0 #c0392b;
            box-shadow: 0 5px 0 #c0392b;
            font-family: 'Pangolin', cursive;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #922;
            animation: neonGlow 2s infinite alternate;
        }
        .store-sign-img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid white;
  position: absolute;
  left: -0px;
  top: 50%;
  transform: translateY(-50%);
  box-shadow: 0 0 5px #fff;
}

        
        @keyframes neonGlow {
            from {
                box-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px var(--store-red), 0 0 20px var(--store-red);
            }
            to {
                box-shadow: 0 0 10px #fff, 0 0 20px #fff, 0 0 30px var(--store-red), 0 0 40px var(--store-red);
            }
        }
        
        .store-sign:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255,255,255,0.8), 
                transparent);
        }
        
        /* Store Window with Wooden Frame */
        .store-window {
            background-color: #eaf2f8;
            border: 3px solid var(--store-brown);
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.1), 
                        0 0 0 5px #d2b48c, 
                        0 0 0 8px #8B4513;
            position: relative;
            overflow: hidden;
            background-image: linear-gradient(to bottom, #eaf2f8, #d4e6f5);
        }
        
        .store-window:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, 
                var(--store-blue), 
                var(--store-green), 
                var(--store-yellow), 
                var(--store-red), 
                var(--store-blue));
        }
        
        /* Additional Filipino Elements */
        .tindahan-basket {
            position: absolute;
            bottom: -30px;
            right: -20px;
            font-size: 40px;
            transform: rotate(-15deg);
            z-index: 5;
            color: #8B4513;
            text-shadow: 2px 2px 0 rgba(0,0,0,0.2);
        }
        
        .banig-pattern {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 20px;
            background: linear-gradient(90deg, 
                #27ae60, #f1c40f, #e74c3c, #f1c40f, #27ae60);
            border-radius: 0 0 10px 10px;
        }
        
        /* Rest of your existing styles... */
        
        /* Enhanced Store Items Section */
        .store-items {
            display: flex;
            justify-content: space-around;
            margin-top: 25px;
            flex-wrap: wrap;
            background-color: rgba(255,255,255,0.7);
            padding: 10px;
            border-radius: 10px;
            border: 2px dashed var(--store-brown);
        }
        
        .item {
            width: 50px;
            height: 50px;
            background-color: var(--store-yellow);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            color: #fff;
            box-shadow: 0 5px 0 #f39c12;
            margin: 5px;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .item:nth-child(2n) {
            background-color: var(--chicherya-pink);
            box-shadow: 0 5px 0 #f368e0;
        }
        
        .item:nth-child(3n) {
            background-color: var(--chicherya-purple);
            box-shadow: 0 5px 0 #341f97;
        }
        
        .item:nth-child(4n) {
            background-color: var(--chicherya-orange);
            box-shadow: 0 5px 0 #e67e22;
        }
        
        .item:nth-child(5n) {
            background-color: var(--store-green);
            box-shadow: 0 5px 0 #27ae60;
        }
        
        /* Sari-sari store curtain effect */
        .store-curtain {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 20px;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(231, 76, 60, 0.3), 
                rgba(241, 196, 15, 0.3), 
                rgba(46, 204, 113, 0.3), 
                rgba(52, 152, 219, 0.3),
                transparent);
            z-index: -1;
        }
        
        /* Filipino jeepney-inspired design elements */
        .jeepney-decoration {
            position: absolute;
            bottom: -40px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 10px;
            background: linear-gradient(90deg, 
                var(--store-blue), 
                var(--store-green), 
                var(--store-yellow), 
                var(--store-red), 
                var(--store-blue));
            border-radius: 0 0 10px 10px;
        }
        
        .jeepney-decoration:before,
        .jeepney-decoration:after {
            content: '';
            position: absolute;
            top: -20px;
            width: 20px;
            height: 20px;
            background-color: var(--store-yellow);
            border-radius: 50%;
            border: 3px solid var(--store-red);
        }
        
        .jeepney-decoration:before {
            left: 10%;
        }
        
        .jeepney-decoration:after {
            right: 10%;
        }
        
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="store-curtain"></div>
    <div class="store-wrapper">
        <div class="filipino-flag"></div>
        
        <!-- Hanging Chicherya -->
        <div class="hanging-chicherya">
            <div class="chicherya-item">🍬</div>
            <div class="chicherya-item">🍫</div>
            <div class="chicherya-item">🍪</div>
            <div class="chicherya-item">🍭</div>
            <div class="chicherya-item">🥜</div>
            <div class="chicherya-item">🍩</div>
        </div>
        
        <div class="store-container">
            <div class="store-roof"></div>
            <div class="store-sign">
  <img src="image/d0f7b7ba-bbd2-45d8-860f-8a5b99643a3f.jpg" alt="My Picture" class="store-sign-img">
  DAVID'S SARI-SARI STORE
</div>

            
            <div class="store-window">
                <h2>Mag-Login</h2>
                
                <?php if (isset($error)) echo "<p class='error'><i class='fas fa-exclamation-circle'></i> $error</p>"; ?>
                <form method="POST">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <br>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <br>
                    <button type="submit">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                <div class="register-link">
                    <p>Wala pang account? <a href="register.php">Mag-register dito!</a></p>
                </div>
            </div>
            
            <div class="store-details">
                <span class="store-open"><i class="fas fa-clock"></i> Bukas Hanggang: 6AM-10PM</span>
                <br><span class="store-owner"><i class="fas fa-store"></i> May-ari: David Rosanes</span>
            </div>
            
            <div class="store-items">
                <div class="item" data-price="₱1">🍬</div>
                <div class="item" data-price="₱15">🍪</div>
                <div class="item" data-price="₱15">🥤</div>
                <div class="item" data-price="₱5">🍫</div>
                <div class="item" data-price="₱15">🍜</div>
            </div>
            
            <div class="banig-pattern"></div>
            <div class="jeepney-decoration"></div>
        </div>
        
        <div class="tindahan-basket">
            🧺
        </div>
    </div>
</body>
</html>