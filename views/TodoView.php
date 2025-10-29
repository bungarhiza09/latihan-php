<!DOCTYPE html>
<html>
<head>
    <title>PHP - Aplikasi Todolist</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        body {
            background-color: #f5f7fa;
        }
        .todo-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .todo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .description {
            min-height: 60px;
            color: #6c757d;
        }
        .status-badge {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
<div class="container py-5">
        <!-- 🔔 Panel Notifikasi -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong>❌ Gagal!</strong> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <strong>✅ Berhasil!</strong> <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="fw-bold text-primary mb-0">📋 Todo List</h1>
        <div class="d-flex gap-2 align-items-center">

            <!-- 🔍 Form Pencarian -->
            <form method="GET" class="d-flex" role="search">
                <!-- Pertahankan filter aktif -->
                <?php if (isset($_GET['filter'])): ?>
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($_GET['filter']) ?>">
                <?php endif; ?>
                <input class="form-control me-2" type="search" name="search" placeholder="Cari todo..." 
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" aria-label="Search">
                <button class="btn btn-outline-secondary" type="submit">🔍</button>
            </form>

            <!-- 🔽 Dropdown Filter -->
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php
                    if (isset($_GET['filter']) && $_GET['filter'] === '0') echo 'Belum Selesai';
                    elseif (isset($_GET['filter']) && $_GET['filter'] === '1') echo 'Selesai';
                    else echo 'Semua Todo';
                    ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item <?= empty($_GET['filter']) ? 'active' : '' ?>" href="index.php">Semua Todo</a></li>
                    <li><a class="dropdown-item <?= (isset($_GET['filter']) && $_GET['filter'] === '0') ? 'active' : '' ?>" href="index.php?filter=0">Belum Selesai</a></li>
                    <li><a class="dropdown-item <?= (isset($_GET['filter']) && $_GET['filter'] === '1') ? 'active' : '' ?>" href="index.php?filter=1">Selesai</a></li>
                </ul>
            </div>

            <!-- ➕ Tombol Tambah -->
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTodo">
                + Tambah Todo
            </button>
        </div>
    </div>

    <?php if (!empty($todos)): ?>
        <div class="row g-4" id="todo-list">
            <?php foreach ($todos as $todo): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card todo-card h-100 shadow-sm"
                        data-todo-id="<?= $todo['id'] ?>"
                        data-description="<?= htmlspecialchars($todo['description'] ?? '-') ?>"
                        data-created-at="<?= date('d M Y H:i', strtotime($todo['created_at'])) ?>"
                        data-updated-at="<?= date('d M Y H:i', strtotime($todo['updated_at'])) ?>"
                        onclick="showModalDetailTodo(
                            <?= $todo['id'] ?>,
                            '<?= htmlspecialchars(addslashes($todo['activity'])) ?>',
                            <?= $todo['status'] ?>
                        )">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title text-primary fw-semibold mb-0">
                                    <?= htmlspecialchars($todo['activity']) ?>
                                </h5>
                                <?php if ($todo['status']): ?>
                                    <span class="badge bg-success status-badge">Selesai</span>
                                <?php else: ?>
                                    <span class="badge bg-danger status-badge">Belum</span>
                                <?php endif; ?>
                            </div>
                            <p class="card-text description">
                                <?= htmlspecialchars($todo['description'] ?? '-') ?>
                            </p>
                            <div class="mt-auto">
                                <p class="small text-muted mb-1">📅 Dibuat: <?= date('d M Y', strtotime($todo['created_at'])) ?></p>
                                <p class="small text-muted mb-2">🕒 Update: <?= date('d M Y', strtotime($todo['updated_at'])) ?></p>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-sm btn-info text-white me-2"
                                        onclick="event.stopPropagation(); showModalDetailTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['activity'])) ?>', <?= $todo['status'] ?>)">
                                        👁️ Detail
                                    </button>
                                    <button class="btn btn-sm btn-warning me-2"
                                        onclick="event.stopPropagation(); showModalEditTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['activity'])) ?>', <?= $todo['status'] ?>)">
                                        ✏️
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="event.stopPropagation(); showModalDeleteTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['activity'])) ?>')">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Belum ada todo yang ditambahkan!
        </div>
    <?php endif; ?>
</div>

<!-- MODAL DETAIL TODO -->
<div class="modal fade" id="detailTodo" tabindex="-1" aria-labelledby="detailTodoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailTodoLabel">Detail Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h5 id="detailTitle" class="fw-bold text-primary"></h5>
                <p id="detailDescription" class="mt-2 text-secondary"></p>
                <hr>
                <p>Status: <span id="detailStatus"></span></p>
                <p>Dibuat: <span id="detailCreatedAt"></span></p>
                <p>Diperbarui: <span id="detailUpdatedAt"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADD TODO -->
<div class="modal fade" id="addTodo" tabindex="-1" aria-labelledby="addTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-sm">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addTodoLabel">Tambah Todo Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputActivity" class="form-label">Judul Aktivitas</label>
                        <input type="text" name="activity" class="form-control" id="inputActivity"
                            placeholder="Contoh: Belajar PHP MVC" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputDescription" class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" id="inputDescription"
                            placeholder="Tuliskan detail aktivitas..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">💾 Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT & DELETE (sama seperti sebelumnya) -->
<div class="modal fade" id="editTodo" tabindex="-1" aria-labelledby="editTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-sm">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editTodoLabel">Edit Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=update" method="POST">
                <input name="id" type="hidden" id="inputEditTodoId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputEditActivity" class="form-label">Judul Aktivitas</label>
                        <input type="text" name="activity" class="form-control" id="inputEditActivity" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputEditDescription" class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" id="inputEditDescription"
                            placeholder="Edit deskripsi aktivitas..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="selectEditStatus" class="form-label">Status</label>
                        <select class="form-select" name="status" id="selectEditStatus">
                            <option value="0">Belum Selesai</option>
                            <option value="1">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">💾 Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteTodo" tabindex="-1" aria-labelledby="deleteTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-sm">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteTodoLabel">Hapus Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Kamu yakin ingin menghapus todo <strong class="text-danger" id="deleteTodoActivity"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="btnDeleteTodo" class="btn btn-danger">🗑️ Hapus</a>
            </div>
        </div>
    </div>
</div>

<script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script>
function showModalDetailTodo(id, title, status) {
    // Ambil elemen-elemen modal
    const detailTitle = document.getElementById("detailTitle");
    const detailDescription = document.getElementById("detailDescription");
    const detailStatus = document.getElementById("detailStatus");
    const detailCreatedAt = document.getElementById("detailCreatedAt");
    const detailUpdatedAt = document.getElementById("detailUpdatedAt");

    // Cari elemen todo di halaman berdasarkan data ID
    const todoCard = document.querySelector(`[data-todo-id='${id}']`);
    if (!todoCard) return;

    // Ambil data tambahan dari atribut card
    const description = todoCard.getAttribute("data-description") || "-";
    const createdAt = todoCard.getAttribute("data-created-at") || "-";
    const updatedAt = todoCard.getAttribute("data-updated-at") || "-";

    // Isi modal
    detailTitle.innerText = title;
    detailDescription.innerText = description;
    detailStatus.innerHTML = status
        ? '<span class="badge bg-success">Selesai</span>'
        : '<span class="badge bg-danger">Belum Selesai</span>';
    detailCreatedAt.innerText = createdAt;
    detailUpdatedAt.innerText = updatedAt;

    // Tampilkan modal
    new bootstrap.Modal(document.getElementById("detailTodo")).show();
}

function showModalDeleteTodo(todoId, activity) {
    document.getElementById("deleteTodoActivity").innerText = activity;
    document.getElementById("btnDeleteTodo").setAttribute("href", `?page=delete&id=${todoId}`);
    new bootstrap.Modal(document.getElementById("deleteTodo")).show();
}

function showDetailModal(title, description, status, createdAt, updatedAt) {
    document.getElementById("detailTitle").innerText = title;
    document.getElementById("detailDescription").innerText = description;
    document.getElementById("detailStatus").innerHTML = status
        ? '<span class="badge bg-success">Selesai</span>'
        : '<span class="badge bg-danger">Belum Selesai</span>';
    document.getElementById("detailCreatedAt").innerText = createdAt;
    document.getElementById("detailUpdatedAt").innerText = updatedAt;
    new bootstrap.Modal(document.getElementById("detailTodo")).show();
}

function showModalEditTodo(id, title, status) {
    // Isi nilai-nilai ke dalam form modal edit
    document.getElementById("inputEditTodoId").value = id;
    document.getElementById("inputEditActivity").value = title;
    document.getElementById("selectEditStatus").value = status;

    // Buka modal edit
    const editModal = new bootstrap.Modal(document.getElementById("editTodo"));
    editModal.show();
}

document.addEventListener("DOMContentLoaded", function () {
    const todoList = document.getElementById("todo-list");

    if (todoList) {
        const sortable = new Sortable(todoList, {
            animation: 150,
            onEnd: function () {
                let order = [];
                document.querySelectorAll("#todo-list .todo-card").forEach((card, index) => {
                    order.push(card.dataset.todoId); // ← gunakan dataset.todoId
                });

                const formBody = order.map(id => `order[]=${id}`).join('&');

                fetch("index.php?page=sort", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: formBody
                })
                .then(res => res.json())
                .then(data => {
                    console.log("Hasil simpan urutan:", data);
                })
                .catch(err => console.error("Error:", err));
            }
        });
    }
});


</script>
</body>
</html>
