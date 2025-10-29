<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Todo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-info text-white">
            <h4>Detail Todo</h4>
        </div>
        <div class="card-body">
            <p><strong>Judul:</strong> <?= htmlspecialchars($todo['title']) ?></p>
            <p><strong>Deskripsi:</strong> <?= htmlspecialchars($todo['description']) ?></p>
            <p><strong>Status:</strong> 
                <?= $todo['is_completed'] ? '<span class="badge bg-success">Selesai</span>' : '<span class="badge bg-secondary">Belum Selesai</span>' ?>
            </p>
            <a href="?page=index" class="btn btn-secondary mt-3">← Kembali</a>
        </div>
    </div>
</div>
</body>
</html>
