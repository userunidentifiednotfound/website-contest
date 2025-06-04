<?php
session_start();
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WebCraft2025 Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=SF+Pro+Text&display=swap');

    body {
      background: radial-gradient(circle at top right, #0f2027, #203a43, #2c5364);
      min-height: 100vh;
      font-family: 'SF Pro Text', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #eee;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .login-container {
      background: rgba(30, 30, 30, 0.5);
      backdrop-filter: saturate(180%) blur(20px);
      border-radius: 2rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow:
        0 0 20px rgba(0, 122, 255, 0.8),
        inset 0 0 15px rgba(0, 122, 255, 0.3);
      width: 100%;
      max-width: 400px;
      padding: 3rem 2.5rem;
      animation: fadeInUp 0.7s ease forwards;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h1 {
      font-weight: 700;
      font-size: 2.5rem;
      text-align: center;
      margin-bottom: 0.3rem;
      letter-spacing: 2px;
      color: #0af;
      text-shadow:
        0 0 8px #0af,
        0 0 15px #0af,
        0 0 20px #0af;
    }

    p.subtitle {
      text-align: center;
      color: #aaccee;
      margin-bottom: 2rem;
      font-weight: 500;
      letter-spacing: 0.05em;
    }

    label {
      color: #cfdfff;
      font-weight: 600;
      letter-spacing: 0.05em;
      margin-bottom: 0.4rem;
      display: block;
      text-shadow: 0 0 3px #0af9ff55;
    }

    .glossy-input {
      width: 100%;
      padding: 0.85rem 1.2rem;
      border-radius: 1rem;
      border: 1.5px solid rgba(10, 150, 255, 0.7);
      background: rgba(10, 150, 255, 0.05);
      color: #d0e8ff;
      font-weight: 600;
      font-size: 1rem;
      box-shadow:
        inset 0 0 10px rgba(10, 150, 255, 0.2),
        0 0 8px rgba(10, 150, 255, 0.3);
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .glossy-input::placeholder {
      color: #84c7ffaa;
      font-weight: 400;
    }

    .glossy-input:focus {
      outline: none;
      border-color: #00f0ff;
      background: rgba(0, 240, 255, 0.12);
      box-shadow:
        0 0 15px #00f0ffaa,
        inset 0 0 12px #00f0ff77;
      color: #e0f7ff;
    }

    .glossy-button {
      width: 100%;
      margin-top: 1.8rem;
      padding: 1rem 0;
      border-radius: 2rem;
      font-weight: 700;
      font-size: 1.1rem;
      background: linear-gradient(135deg, #00eaff, #0066ff);
      color: #fff;
      border: none;
      cursor: pointer;
      box-shadow:
        0 0 20px #00eaff,
        0 6px 15px rgba(0, 106, 255, 0.8);
      transition: all 0.3s ease;
      text-shadow:
        0 0 5px #00eaff;
    }

    .glossy-button:hover {
      background: linear-gradient(135deg, #00ffff, #3399ff);
      box-shadow:
        0 0 25px #00ffff,
        0 8px 25px rgba(0, 180, 255, 0.9);
      transform: translateY(-3px);
    }

    .error-message {
      background-color: #ff0044aa;
      border-radius: 0.75rem;
      padding: 0.8rem 1.2rem;
      color: #fff;
      font-weight: 700;
      letter-spacing: 0.05em;
      margin-bottom: 1.5rem;
      text-align: center;
      text-shadow: 0 0 5px #ff4488;
      user-select: none;
      animation: pulse 1.8s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% {
        text-shadow:
          0 0 6px #ff4466,
          0 0 12px #ff2277;
      }
      50% {
        text-shadow:
          0 0 12px #ff6699,
          0 0 18px #ff4499;
      }
    }

    .footer-text {
      margin-top: 2rem;
      font-size: 0.9rem;
      text-align: center;
      color: #99bbffaa;
      user-select: none;
      font-weight: 500;
      letter-spacing: 0.07em;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h1>WebCraft <span style="color:#33caff;">2025</span></h1>
    <p class="subtitle">Login to continue</p>

    <?php if (isset($_GET['error'])): ?>
      <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="login_process.php" method="POST" autocomplete="off" novalidate>
      <label for="email">Email</label>
      <input
        type="email"
        id="email"
        name="email"
        required
        placeholder="you@example.com"
        class="glossy-input"
        autofocus
      />

      <label for="password" class="mt-6">Password</label>
      <input
        type="password"
        id="password"
        name="password"
        required
        placeholder="••••••••"
        class="glossy-input"
      />

      <button type="submit" class="glossy-button">Login</button>
    </form>

    <p class="footer-text">Don’t have an account? Contact WebCraft Admin.</p>
  </div>

</body>
</html>
