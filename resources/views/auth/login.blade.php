<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login | YAYASAN DARUSSALAM</title>

  <!-- Meta -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Sistem Informasi Yayasan Darussalam">
  <meta name="keywords" content="Laravel, HRM, Admin Template, Bootstrap 5">
  <meta name="author" content="Tim PBL 221">

  <!-- Favicon -->
  <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">

  <!-- Fonts & Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
  <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
  <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
</head>

<style>
    body {
    background-color: #f8f9fa;
    font-family: 'Public Sans', sans-serif;
  }

  .login-card {
    max-width: 400px;
    margin: 100px auto;
    padding: 30px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }

  .header-logo {
    position: absolute;
    top: 20px;
    left: 20px;
    display: flex;
    align-items: center;
  }

  .header-logo img {
    height: 50px;
    margin-right: 10px;
  }

  .header-logo h5 {
    margin: 0;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.1;
  }



  .btn-primary {
    background-color: #0056b3;
    border-color: #0056b3;
  }

  .btn-primary:hover {
    background-color: #004799;
    border-color: #004799;
  }

  .link-primary {
    color: #ffcc00 !important;
  }

  .link-primary:hover {
    color: #e6b800 !important;
    text-decoration: underline;
  }

  .form-check-input:checked {
    background-color: #0056b3;
    border-color: #0056b3;
  }

  .form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 86, 179, 0.25);
  }
  </style>
</head>
<body>

  <!-- Logo di pojok kiri atas -->
  <div class="header-logo">
    <img src="assets/images/logo.png" alt="Logo">
    <div class="vr me-3" style="height: 50px;"></div>
    <div>
      <h5>HR YAYASAN</h5>
      <h5>DARUSSALAM</h5>
    </div>
  </div>

<!-- Card login -->
<div class="login-card">
  <div class="d-flex justify-content-between align-items-end mb-5">
    <h4 class="mb-0 fw-bold">Login</h4>
  </div>

  <!-- Select Role -->
  <div class="mb-3">
    <label for="role" class="form-label">Login Sebagai</label>
    <select class="form-select" id="role" required>
      <option value="" selected disabled>Pilih Role</option>
      <option value="hrd">HRD</option>
      <option value="kepala_yayasan">Kepala Yayasan</option>
      <option value="pegawai">Pegawai</option>
    </select>
  </div>

  <!-- Email -->
  <div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" class="form-control" id="email" placeholder="Email Address" required>
  </div>

  <!-- Password -->
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password" placeholder="Password" required>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="#" class="small text-decoration-none">Forgot Password?</a>
  </div>

  <button class="btn btn-primary w-100">Login</button>


  <p class="text-center mt-4 text-muted small mb-0">© 2025 PBL 221</p>
</div>
  <!-- Scripts -->
  <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
  <script src="{{ asset('assets/js/pcoded.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

  <!-- Layout Scripts -->
  <script>layout_change('light');</script>
  <script>change_box_container('false');</script>
  <script>layout_rtl_change('false');</script>
  <script>preset_change("preset-1");</script>
  <script>font_change("Public-Sans");</script>
</body>
</html>
