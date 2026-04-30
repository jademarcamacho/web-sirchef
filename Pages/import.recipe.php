<?php
session_start();
include 'db.php';

$message = '';
$message_type = '';
$imported = 0;
$skipped = 0;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            $handle = fopen($file['tmp_name'], 'r');
            $header = fgetcsv($handle); // skip header row

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 11) {
                    $skipped++;
                    continue;
                }

                list($title, $image, $time, $difficulty, $servings,
                     $cuisine, $category, $description,
                     $ing_preview, $ingredients, $instructions) = $row;

                // Skip empty rows
                if (empty(trim($title))) {
                    $skipped++;
                    continue;
                }

                // Validate difficulty
                $valid_diff = ['Easy', 'Medium', 'Hard'];
                if (!in_array(trim($difficulty), $valid_diff)) {
                    $errors[] = "Row skipped — \"$title\" has invalid difficulty: \"$difficulty\". Use Easy, Medium, or Hard.";
                    $skipped++;
                    continue;
                }

                // Sanitize
                $title        = mysqli_real_escape_string($recipe_conn, trim($title));
                $image        = mysqli_real_escape_string($recipe_conn, trim($image));
                $time         = mysqli_real_escape_string($recipe_conn, trim($time));
                $difficulty   = mysqli_real_escape_string($recipe_conn, trim($difficulty));
                $servings     = mysqli_real_escape_string($recipe_conn, trim($servings));
                $cuisine      = mysqli_real_escape_string($recipe_conn, trim($cuisine));
                $category     = mysqli_real_escape_string($recipe_conn, trim($category));
                $description  = mysqli_real_escape_string($recipe_conn, trim($description));
                $ing_preview  = mysqli_real_escape_string($recipe_conn, trim($ing_preview));
                $ingredients  = mysqli_real_escape_string($recipe_conn, trim($ingredients));
                $instructions = mysqli_real_escape_string($recipe_conn, trim($instructions));

                $sql = "INSERT INTO recipes 
                        (title, image, time, difficulty, servings, cuisine, category, description, ing_preview, ingredients, instructions)
                        VALUES 
                        ('$title','$image','$time','$difficulty','$servings','$cuisine','$category','$description','$ing_preview','$ingredients','$instructions')";

                if (mysqli_query($recipe_conn, $sql)) {
                    $imported++;
                } else {
                    $errors[] = "Failed to save \"$title\": " . mysqli_error($recipe_conn);
                    $skipped++;
                }
            }

            fclose($handle);
            $message_type = $imported > 0 ? 'success' : 'error';
            $message = $imported > 0
                ? "$imported recipe(s) imported successfully!"
                : "No recipes were imported.";
        } else {
            $message_type = 'error';
            $message = 'Please upload a .csv file only.';
        }
    } else {
        $message_type = 'error';
        $message = 'File upload failed. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Import Recipes | SirChef Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
    .admin-wrap { max-width: 700px; margin: 60px auto; padding: 0 16px; }
    .admin-card { background: #fff; border-radius: 16px; border: 1px solid #e5e7eb; padding: 36px; }
    .admin-header { margin-bottom: 28px; }
    .admin-header h1 { font-size: 22px; font-weight: 700; color: #111; margin: 0 0 4px; }
    .admin-header p { font-size: 14px; color: #6b7280; margin: 0; }
    .upload-zone { border: 2px dashed #d1d5db; border-radius: 12px; padding: 40px 20px;
                   text-align: center; cursor: pointer; transition: all .2s; background: #fafafa; }
    .upload-zone:hover, .upload-zone.dragover { border-color: #3b82f6; background: #eff6ff; }
    .upload-zone i { font-size: 36px; color: #9ca3af; margin-bottom: 12px; display: block; }
    .upload-zone .uz-title { font-size: 15px; font-weight: 600; color: #374151; }
    .upload-zone .uz-sub { font-size: 13px; color: #9ca3af; margin-top: 4px; }
    .file-name { font-size: 13px; color: #3b82f6; margin-top: 10px; font-weight: 500; }
    #csv_file { display: none; }
    .btn-import { width: 100%; padding: 12px; font-size: 15px; font-weight: 600;
                  background: #1d4ed8; border: none; border-radius: 10px; color: #fff;
                  cursor: pointer; transition: background .2s; margin-top: 20px; }
    .btn-import:hover { background: #1e40af; }
    .btn-import:disabled { background: #93c5fd; cursor: not-allowed; }
    .alert-box { border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 14px; }
    .alert-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
    .alert-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
    .alert-box i { margin-right: 6px; }
    .err-list { margin-top: 10px; padding-left: 18px; font-size: 13px; }
    .err-list li { margin-bottom: 4px; }
    .steps { background: #f8fafc; border-radius: 10px; padding: 18px 20px; margin-top: 24px; }
    .steps h6 { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 12px; text-transform: uppercase; letter-spacing: .04em; }
    .step-item { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 10px; }
    .step-num { width: 22px; height: 22px; border-radius: 50%; background: #1d4ed8; color: #fff;
                font-size: 11px; font-weight: 700; display: flex; align-items: center;
                justify-content: center; flex-shrink: 0; margin-top: 1px; }
    .step-text { font-size: 13px; color: #4b5563; line-height: 1.5; }
    .step-text b { color: #111; }
    .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px;
                 color: #6b7280; text-decoration: none; margin-bottom: 20px; }
    .back-link:hover { color: #1d4ed8; }
    .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px; }
    .stat-box { border-radius: 10px; padding: 14px; text-align: center; }
    .stat-box .num { font-size: 28px; font-weight: 700; }
    .stat-box .lbl { font-size: 12px; margin-top: 2px; }
    .stat-green { background: #f0fdf4; }
    .stat-green .num { color: #16a34a; }
    .stat-green .lbl { color: #4ade80; }
    .stat-gray  { background: #f9fafb; }
    .stat-gray  .num { color: #6b7280; }
    .stat-gray  .lbl { color: #9ca3af; }
    .progress-wrap { display: none; text-align: center; padding: 20px 0; }
    .spinner { width: 36px; height: 36px; border: 3px solid #e5e7eb;
               border-top-color: #1d4ed8; border-radius: 50%;
               animation: spin .7s linear infinite; margin: 0 auto 12px; }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>
<div class="admin-wrap">
  <a href="recipe.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to recipes</a>

  <div class="admin-card">
    <div class="admin-header">
      <h1><i class="fas fa-file-csv" style="color:#1d4ed8;margin-right:8px"></i>Bulk Import Recipes</h1>
      <p>Upload a CSV file to add hundreds of recipes at once to your database.</p>
    </div>

    <?php if ($message): ?>
      <div class="alert-box alert-<?= $message_type ?>">
        <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <strong><?= htmlspecialchars($message) ?></strong>
        <?php if ($skipped > 0): ?>
          <br><small><?= $skipped ?> row(s) were skipped.</small>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
          <ul class="err-list">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <?php if ($imported > 0): ?>
        <div class="stats">
          <div class="stat-box stat-green">
            <div class="num"><?= $imported ?></div>
            <div class="lbl">Recipes imported</div>
          </div>
          <div class="stat-box stat-gray">
            <div class="num"><?= $skipped ?></div>
            <div class="lbl">Rows skipped</div>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="importForm">
      <div class="upload-zone" id="uploadZone" onclick="document.getElementById('csv_file').click()">
        <i class="fas fa-cloud-upload-alt"></i>
        <div class="uz-title">Click to choose your CSV file</div>
        <div class="uz-sub">or drag and drop it here</div>
        <div class="file-name" id="fileName"></div>
      </div>
      <input type="file" name="csv_file" id="csv_file" accept=".csv" />

      <div class="progress-wrap" id="progressWrap">
        <div class="spinner"></div>
        <div style="font-size:14px;color:#6b7280">Importing recipes, please wait...</div>
      </div>

      <button type="submit" class="btn-import" id="importBtn" disabled>
        <i class="fas fa-upload"></i> Import Recipes
      </button>
    </form>

    <div class="steps">
      <h6>How to use</h6>
      <div class="step-item">
        <div class="step-num">1</div>
        <div class="step-text">Fill your <b>Excel template</b> with recipes — one recipe per row</div>
      </div>
      <div class="step-item">
        <div class="step-num">2</div>
        <div class="step-text">In Excel: <b>File → Save As → CSV (Comma delimited) .csv</b></div>
      </div>
      <div class="step-item">
        <div class="step-num">3</div>
        <div class="step-text">Click above, select your <b>.csv file</b>, then click Import</div>
      </div>
      <div class="step-item">
        <div class="step-num">4</div>
        <div class="step-text">All recipes appear on your <b>recipe page instantly!</b></div>
      </div>
    </div>
  </div>
</div>

<script>
const zone = document.getElementById('uploadZone');
const input = document.getElementById('csv_file');
const btn = document.getElementById('importBtn');
const fileName = document.getElementById('fileName');
const form = document.getElementById('importForm');
const progressWrap = document.getElementById('progressWrap');

input.addEventListener('change', function() {
  if (this.files.length > 0) {
    fileName.textContent = this.files[0].name;
    btn.disabled = false;
  }
});

zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
zone.addEventListener('drop', e => {
  e.preventDefault();
  zone.classList.remove('dragover');
  const file = e.dataTransfer.files[0];
  if (file && file.name.endsWith('.csv')) {
    input.files = e.dataTransfer.files;
    fileName.textContent = file.name;
    btn.disabled = false;
  }
});

form.addEventListener('submit', function() {
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
  progressWrap.style.display = 'block';
});
</script>
</body>
</html>