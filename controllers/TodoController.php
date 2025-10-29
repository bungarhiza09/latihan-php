<?php
require_once (__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        $todoModel = new TodoModel();

        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        if (!empty($search)) {
            // Jika user melakukan pencarian
            $todos = $todoModel->searchTodos($search, $filter);
        } elseif ($filter === '0' || $filter === '1') {
            // Jika hanya filter aktif tanpa pencarian
            $todos = $todoModel->getTodosByStatus($filter);
        } else {
            // Default: tampilkan semua
            $todos = $todoModel->getAllTodos();
        }

        include '../views/TodoView.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activity = trim($_POST['activity']);
            $description = trim($_POST['description'] ?? '');

            $todoModel = new TodoModel();

            // ✅ Cek duplikat
            if ($todoModel->isActivityExists($activity)) {
                $error = "Judul todo \"$activity\" sudah ada. Gunakan judul lain.";
                $todos = $todoModel->getAllTodos();
                include '../views/TodoView.php';
                return;
            }

            $todoModel->createTodo($activity, $description);
            $success = "Todo \"$activity\" berhasil ditambahkan.";
        }

        header('Location: index.php');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $activity = trim($_POST['activity']);
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'];

            $todoModel = new TodoModel();

            // ✅ Cek duplikat (kecuali dirinya sendiri)
            if ($todoModel->isActivityExists($activity, $id)) {
                $error = "Judul todo \"$activity\" sudah digunakan oleh todo lain.";
                $todos = $todoModel->getAllTodos();
                include '../views/TodoView.php';
                return;
            }

            $todoModel->updateTodo($id, $activity, $description, $status);
            $success = "Todo \"$activity\" berhasil diperbarui.";
        }

        header('Location: index.php');
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $todoModel->deleteTodo($id);
        }
        header('Location: index.php');
    }

    public function show()
    {
        if (!isset($_GET['id'])) {
            $error = "Todo tidak ditemukan.";
            $todos = $this->model->getAll();
            include '../views/TodoView.php';
            return;
        }

        $id = $_GET['id'];
        $todo = $this->model->getById($id);

        if (!$todo) {
            $error = "Todo dengan ID tersebut tidak ada.";
            $todos = $this->model->getAll();
            include '../views/TodoView.php';
            return;
        }

        include '../views/TodoDetailView.php';
    }

    public function sort()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
            $todoModel = new TodoModel();
            $order = $_POST['order']; // array id urutan terbaru

            foreach ($order as $position => $id) {
                $todoModel->updatePosition($id, $position);
            }

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

}
