<?php
// Fungsi untuk menavigasi antar halaman
function navigate($path) {
    if ($path) {
        header("Location: $path");  // Redirect ke halaman tujuan
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP Button with Dropdown</title>
  <link rel="stylesheet" href="css/Button.css"> <!-- Link ke CSS -->
</head>
<body>

<?php
// Button data (di sini data ini bisa berasal dari database atau array lain)
$buttons = [
    ['label' => 'Filter', 'icon' => 'filter-icon', 'className' => 'filter-button'],
    ['label' => 'Submit', 'icon' => 'submit-icon', 'className' => 'submit-button', 'path' => '/submit']
];

$filterFields = [
    ['id' => 'field1', 'label' => 'Field 1', 'options' => [['value' => '1', 'label' => 'Option 1'], ['value' => '2', 'label' => 'Option 2']]],
    ['id' => 'field2', 'label' => 'Field 2', 'options' => [['value' => '3', 'label' => 'Option 3'], ['value' => '4', 'label' => 'Option 4']]]
];
?>

<div class="button-container">
  <?php foreach ($buttons as $button): ?>
    <div class="button-wrapper">
      <?php if ($button['label'] === 'Filter'): ?>
        <!-- Tombol Filter dengan dropdown -->
        <button class="custom-button <?= $button['className'] ?>" onclick="toggleDropdown()">
          <i class="<?= $button['icon'] ?> icon"></i> <?= $button['label'] ?>
        </button>

        <!-- Dropdown filter -->
        <div class="dropdown-filter" id="filterDropdown" style="display: none;">
          <label for="sortSelect">Urutkan Berdasarkan:</label>
          <?php foreach ($filterFields as $field): ?>
            <div class="filter-field">
              <label for="<?= $field['id'] ?>" style="font-weight: 600;"><?= $field['label'] ?></label>
              <select id="<?= $field['id'] ?>" class="sort-select">
                <?php foreach ($field['options'] as $option): ?>
                  <option value="<?= $option['value'] ?>"><?= $option['label'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <!-- Tombol biasa tanpa dropdown -->
        <button class="custom-button <?= $button['className'] ?>" onclick="window.location.href='<?= $button['path'] ?>'">
          <i class="<?= $button['icon'] ?> icon"></i> <?= $button['label'] ?>
        </button>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<script>
// JavaScript untuk menampilkan dan menyembunyikan dropdown filter
function toggleDropdown() {
    var dropdown = document.getElementById('filterDropdown');
    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}
</script>

</body>
</html>
