<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Example</title>
    <link rel="stylesheet" href="css/Alert.css"> <!-- Pastikan path benar -->
</head>
<body>
    <?php
    function showAlert($type, $message) {
        // Menentukan kelas alert langsung berdasarkan tipe
        $alertClass = "alert-$type custom-alert"; // Gabungkan kelas alert langsung

        // Tampilkan alert
        echo "<div class='alert $alertClass' role='alert'>
                $message
              </div>";
    }
    ?>
</body>
</html>
