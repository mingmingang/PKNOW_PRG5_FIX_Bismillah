<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/favicon.png') }}" type="image/png">
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
          <div class="menu-item" onclick="handleBeranda()">
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
              <span>Kelola Kelompok Keahlian</span>
            </a>
          </li>
          <li>
            <a onclick="handleKelolaAKK()">
            <i class="fas fa-users"></i>
              <span>Kelola Anggota</span>
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
                <a onclick="handleDaftarPustaka()">
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
function handleKelolaKK() {
  const role = "{{ Cookie::get('role') }}";
        window.location.href = `/kelola_kk/${role}`;
}

function handleBeranda() {
  const role = "{{ Cookie::get('role') }}";
        window.location.href = `/dashboard/${role}`;
}

function handleKelolaAKK() {
  const role = "{{ Cookie::get('role') }}";
  if (!role) {
    alert('Role tidak ditemukan!');
    return;
  }
  window.location.href = `/kelola_akk/${role}`;
}


function handleDaftarPustaka() {
    const role = "{{ Cookie::get('role') }}";
    window.location.href = `/daftar_pustaka/${role}`;
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
    const usrId = "{{ Cookie::get('usr_id') }}";
    console.log('usr_id dari cookie:', usrId);
    const role = "{{ Cookie::get('role') }}";
    console.log('role dari cookie:', role);
    const pengguna = "{{ Cookie::get('pengguna') }}";
    console.log('pengguna dari cookie:', pengguna);
    
   
        document.getElementById('role-title').textContent = `${role}`;
        document.getElementById('role-nama').textContent = `${pengguna}`;
        console.log("Role yang dipilih: " + role);
        // Lakukan apa pun dengan data role di sini
   
};

</script>
</html>
