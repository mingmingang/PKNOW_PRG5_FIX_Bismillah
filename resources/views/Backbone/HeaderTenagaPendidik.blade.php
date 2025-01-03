<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="path/to/font-awesome/css/all.min.css" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <nav>
        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('assets/logoAstratech.png') }}" alt="Logo ASTRAtech" title="Logo ASTRAtech" width="170px" height="40px">
        </div>
        <div class="menu-profile-container">
  <div class="menu">
    <ul class="menu-center">
      <li class="menu-item active">
        <a href="#beranda" class="menu-link">
          <div class="menu-item">
            <span>Beranda</span>
          </div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#menu2" class="menu-link">
          <div class="menu-item">
            <span>Kelompok Keahlian</span>
            <i class="fas fa-chevron-down"></i>
          </div>
        </a>
        <ul class="dropdown-content">
          <li>
            <a onclick="handleKelolaKK()">
            <i class="fas fa-cogs"></i>
              <span>Pengajuan Kelompok Keahlian</span>
            </a>
          </li>
          <li>
            <a href="#sub2">
            <i class="fas fa-users"></i>
              <span>Riwayat Pengajuan</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="menu-item">
        <a href="#menu3" class="menu-link">
          <div class="menu-item">
            <span>Knowledge Database</span>
            <i class="fas fa-chevron-down"></i>
          </div>
        </a>
        <ul class="dropdown-content">
          <li>
            <a href="#sub1">
            <i class="fas fa-book"></i>
              <span>Daftar Pustaka</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="menu-item">
        <a href="#menu3" class="menu-link">
          <div class="menu-item">
            <span>I-Learning</span>
            <i class="fas fa-chevron-down"></i>
          </div>
        </a>
        <ul class="dropdown-content">
        <li>
            <a href="#sub1">
            <i class="fas fa-graduation-cap"></i>
              <span>Kelola Materi</span>
            </a>
          </li>
          <li>
            <a href="#sub1">
            <i class="fas fa-graduation-cap"></i>
              <span>Materi</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</div>

<div class="profile">
  <!-- Conditionally render user info -->
  <div class="pengguna">
  <div class="pengguna">
  <h3 id="role-nama"></h3>
  <h4 id="role-title"></h4>
  <p>Terakhir Masuk: <span id="last-login"></span></p>
</div>

  </div>

  <div class="fotoprofil">
    <div class="profile-photo-container" onmouseenter="showDropdown()" onmouseleave="hideDropdown()">
      <!-- Profile Photo -->
      <img src="{{ asset('assets/ceweVR_beranda.png') }}" alt="Profile" class="profile-photo" />

      <!-- Dropdown Menu -->
      <ul class="profile-dropdown" style="display: none; margin-left:-60px;">
        <li>
          <span onclick="handleNotification()" style="cursor: pointer;">
            <i class="fas fa-bell" style="color: #0A5EA8;"></i>
            <span style="color: #0A5EA8;">
              Notifikasi
              <span style="background: red; border-radius: 50%; padding-left: 5px; padding-right: 5px; color: white;">
                0
              </span>
            </span>
          </span>
        </li>
        <li>
          <a href="{{ route('clearRoleSession') }}">
          <span class="keluar" style="cursor: pointer;">
            <i class="fas fa-sign-out-alt" style="color: red;"></i>
            <span style="color: red;" >Logout</span>
          </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

</div>
    </nav>
</body>
<script>
  function handleKelolaKK(){
    const urlParams = new URLSearchParams(window.location.search);
    const role = urlParams.get('role');
    const name = urlParams.get('pengguna');
    window.location.href = `/kelola_kk/${role}?role=${encodeURIComponent(role)}&pengguna=${encodeURIComponent(name)}`;
  }
  function showDropdown() {
  document.querySelector('.profile-dropdown').style.display = 'block';
}

function hideDropdown() {
  document.querySelector('.profile-dropdown').style.display = 'none';
}

function handleNotification() {
  // Your notification handling logic here
  alert("Notification clicked");
}

function handleLogoutClick() {
  // Your logout handling logic here
  alert("Logout clicked");
}

// Mendapatkan tanggal dan waktu saat ini
function getCurrentDateTime() {
  const now = new Date();
  
  // Format tanggal: YYYY-MM-DD
  const date = now.toISOString().split('T')[0];
  
  // Format waktu: HH:mm:ss
  const time = now.toTimeString().split(' ')[0];
  
  return `${date} ${time}`;
}

// Menampilkan tanggal dan waktu terakhir masuk
document.getElementById('last-login').textContent = getCurrentDateTime();

window.onload = function() {
    // Get 'role' parameter from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const role = urlParams.get('role');
    const name = urlParams.get('pengguna');
    
    if (role) {
        document.getElementById('role-title').textContent = `${role}`;
        document.getElementById('role-nama').textContent = `${name}`;
        console.log("Role yang dipilih: " + role);
        // Lakukan apa pun dengan data role di sini
    } else {
        console.log("Role tidak ditemukan di URL.");
    }
};

</script>
</html>
