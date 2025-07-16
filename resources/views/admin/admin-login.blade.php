<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CPU Facility Booking - Admin Login</title>
    <style>
      :root {
        --primary-color: #041a4b;
        --primary-transparent: rgba(4, 26, 75, 0.4);
        --primary-hover: #082d77;
        --yellow-color: #f2b023ab;
        --yellow-border: #d1a648;
        --yellow-hover: #ffc341ab;
        --secondary-color: #6c757d;
        --secondary-hover: #5a6268;
        --text-color: #333;
        --light-gray: #f5f5f5;
        --border-radius: 8px;
      }

      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        border-radius: 0 !important;
      }

      /* Exclude admin photo container */
      .profile-img {
        border-radius: 50% !important;
      }

      body {
        font-family: "Arial", sans-serif;
        background-image: url("../cpu-pic1.jpg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
        color: var(--text-color);
      }

      .login-container {
        width: 100%;
        max-width: 500px;
        background-color: rgba(255, 255, 255, 0.95);
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
      }

      .title-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 25px;
      }

      .title-container img {
        width: 80px;
        height: auto;
        margin-bottom: 15px;
      }

      .title-container h1 {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--text-color);
        line-height: 1.3;
      }

      .login-container h2 {
        font-size: 1.1rem;
        margin-bottom: 25px;
        color: #666;
        font-weight: normal;
        text-align: center;
      }

      .form-group {
        width: 100%;
        margin-bottom: 20px;
        text-align: left;
      }

      .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #555;
      }

      .login-container input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        font-size: 1rem;
        transition: border-color 0.3s;
      }

      .login-container input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
      }

      .forgot-password {
        display: block;
        text-align: right;
        margin-top: -10px;
        margin-bottom: 20px;
        color: var(--primary-color);
        text-decoration: none;
        font-size: 0.9rem;
      }

      .forgot-password:hover {
        text-decoration: underline;
      }

      .login-button {
        width: 100%;
        padding: 12px;
        background-color: var(--primary-color);
        color: white;
        border: none;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-bottom: 15px;
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.5);
      }

      .login-button:hover {
        background-color: var(--primary-hover);
      }

      .home-button {
        width: 100%;
        padding: 12px;
        background-color: var(--yellow-color);
        color: var(--text-color);
        border: none;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.5);
        border: solid 1px var(--yellow-border);
      }

      .home-button:hover {
        background-color: var(--yellow-hover);
        border: solid 1px var(--yellow-border);
        color: var(--text-color);
      }

      @media (max-width: 768px) {
        .login-container {
          padding: 25px 20px;
        }

        .title-container h1 {
          font-size: 1.2rem;
        }

        .login-container h2 {
          font-size: 1rem;
        }
      }

      @media (max-width: 480px) {
        body {
          padding: 15px;
        }

        .login-container {
          padding: 20px 15px;
        }

        .title-container h1 {
          font-size: 1.1rem;
        }

        .login-container input {
          padding: 10px 12px;
        }
      }

      /* Invalid Password */
      .shake {
        animation: shake 0.5s linear;
      }

      @keyframes shake {
        0%,
        100% {
          transform: translateX(0);
        }
        20%,
        60% {
          transform: translateX(-5px);
        }
        40%,
        80% {
          transform: translateX(5px);
        }
      }

      .error-box {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
        padding: 10px;
        margin-bottom: 15px;
        display: none;
      }
    </style>
  </head>
  <body>
    <div class="login-container">
      <div class="title-container">
        <img src="../cpu-logo.png" alt="CPU Logo" />
        <h1>
          Central Philippine University<br />Facility and Equipment
          <br />Booking Services
        </h1>
      </div>
      <h2>Admin Login</h2>

      <div id="errorBox" class="error-box"></div>

      <form id="loginForm" onsubmit="return false;">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            required
          />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
          />
        </div>

        <a href="#" class="forgot-password">Forgot your password?</a>

        <button type="submit" id="loginBtn" class="login-button">Login</button>
        <a href="../index.html" class="home-button">Return to Homepage</a>
      </form>
    </div>
    <script>
        document.getElementById('loginBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorBox = document.getElementById('errorBox');
            const loginBtn = this;
        
            // Disable button and show loading state
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Logging in...';
        
            try {
                const response = await fetch('http://127.0.0.1:8000/api/admin/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password }),
                    redirect: 'manual' // Important to prevent automatic redirects
                });
        
                // Handle response
                if (response.status === 0) {
                    throw new Error('Network error or CORS blocked the request');
                }
        
                const data = await response.json();
        
                if (!response.ok) {
                    throw new Error(data.message || 'Login failed');
                }
        
                // Successful login
                localStorage.setItem('adminToken', data.token);
                window.location.href = 'dashboard.html';
        
            } catch (error) {
                errorBox.textContent = error.message;
                errorBox.style.display = 'block';
                console.error('Login error:', error);
            } finally {
                loginBtn.disabled = false;
                loginBtn.textContent = 'Login';
            }
        });
        </script>
  </body>
</html>
