<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP Button</title>
  <!-- Link ke file Button.css -->
  <link rel="stylesheet" href="css/Button.css">
</head>
<body>

<?php
function Button($classType = '', $iconName = '', $label = '', $title = '', $type = 'button', $isDisabled = false, $additionalProps = []) {
    // Menyiapkan class tambahan berdasarkan parameter classType
    $class = "custom-button add-button " . $classType;

    // Menyiapkan status disabled
    $disabled = $isDisabled ? 'disabled' : '';

    // Menyiapkan atribut tambahan
    $props = '';
    foreach ($additionalProps as $key => $value) {
        $props .= " $key=\"$value\"";
    }

    // Membuka tag button
    echo "<button type=\"$type\" class=\"$class\" title=\"$title\" $disabled $props>";

    // Menampilkan ikon jika ada
    if ($iconName) {
        echo "<i class=\"icon-$iconName\" style=\"margin-top: 5px;\"";
        if ($label !== '') {
            echo " class=\"pe-2\""; // Menambahkan class jika label tidak kosong
        }
        echo "></i>";
    }

    // Menampilkan label jika ada
    echo $label;

    // Menutup tag button
    echo "</button>";
}
?>

<!-- Penggunaan fungsi Button di halaman PHP -->
<!-- <?php
Button('primary', 'plus', 'Tambah', 'Tambah Data', 'submit', false, ['id' => 'btnSubmit', 'data-toggle' => 'tooltip']);
?> -->

</body>
</html>
