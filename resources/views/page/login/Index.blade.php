<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P-KNOW Login</title>
    <link rel="stylesheet" href="{{ asset('css/Login.css') }}">
    <link rel="icon" href="{{ asset('assets/favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapi.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>
@include('backbone.header', [
    'showMenu' => false,
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
    'menuItems' => [],
    'konfirmasi' => 'Konfirmasi',
    'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
    'kelolaProgram' => false,
    'showConfirmation' => false,
])

<main>
    <section class="login-background">
        <div class="login-container">
        <div style="width: 600px; margin-right:100px; text-align:center;">
              <h3 style="font-weight: bold;color:#0A5EA8;" >Mulai langkah awal pembelajaranmu dengan P-KNOW</h3>
              <img src="{{ asset('assets/loginMaskotTMS.png') }}" alt="" width="600px"/>
            </div>
            <div class="login-box">
                <img src="{{ asset('assets/pknow.png') }}" class="pknow" alt="Logo P-KNOW" title="Logo P-KNOW" width="300px">
                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf
                    <input type="text" class="login-input" name="username" placeholder="Nama Pengguna" required>
                    <input type="password" class="login-input" name="password" placeholder="Kata Sandi" required>
                    <div class="d-flex justify-content-between mt-3">
                        <img src="{{ route('captcha.generate') }}?rand={{ rand() }}" id="captcha_image">
                        <div class="mt-0">
                            <input type="text" name="captcha" class="login-input ml-3 mt-2" required placeholder="Masukan Captcha">
                        </div>
                        <p class="mt-3">
                            <a href="javascript: refreshCaptcha();"><i class="fas fa-sync-alt mr-3"></i></a>
                        </p>
                    </div>
                    
                    
                    

                    @if (session('error'))
                        <p class="error-text">{{ session('error') }}</p>
                    @endif

                    <button type="submit" class="login-button">Masuk</button>
                </form>

                <!-- Modal for Role Selection -->
                <div id="roleModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" style="border-radius: 100px;">
                        <div class="modal-content" style="border-radius: 10px;">
                            <div class="modal-header">
                                <h5 class="modal-title">Pilih Peran</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <ul id="roleList" class="list-group" style="text-align: left;border:none;">
                                    <!-- Roles will be inserted dynamically -->
                                </ul>
                            </div>
                            <div class="modal-footer">
                            <button type="button" data-dismiss="modal" aria-label="Close" style="font-size: 16px; color:white; background:#0A5EA8; border:none; padding:5px 15px; border-radius:10px;font-weight:600;">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
</div>
            </div>
        </div>
    </section>
</main>

@include('backbone.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const usrId = "{{ Cookie::get('usr_id') }}";
        console.log('usr_id dari cookie:', usrId);

        @if(session('roles') && session('showRoleSelectionModal'))
        const roles = @json(session('roles'));
        if (roles && roles.length > 0) {
            let roleList = document.getElementById('roleList');
            roles.forEach(role => {
                const listItem = document.createElement('li');
                listItem.classList.add('list-group-item');
                listItem.innerHTML = `Masuk sebagai ${role.name}`;
                listItem.addEventListener('click', function() {
                    // window.location.href = `/dashboard/${role.name}?role=${encodeURIComponent(role.name)}&pengguna=${encodeURIComponent(role.pengguna)}`;
                    window.location.href = `/dashboard/${role.name}`;
                });
                roleList.appendChild(listItem);
            });

            jQuery('#roleModal').modal('show');

            jQuery('#roleModal').on('shown.bs.modal', function () {
                fetch("{{ route('clearRoleSession') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                  .then(data => console.log("Session cleared:", data))
                  .catch(error => console.error("Error clearing session:", error));
            });
        }
        @endif
    });
</script>

<script>
//Refresh Captcha
function refreshCaptcha() {
        var img = document.images['captcha_image'];
        img.src = img.src.split('?')[0] + "?rand=" + Math.random() * 1000;
    }
</script>



</body>
</html>