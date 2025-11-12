<?php
session_start();
include '../config/database.php'; // path sudah benar dari folder auth ke config

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek email di database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Jika email ditemukan
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();

            // Cocokkan password (karena disimpan dalam kolom password_hash)
            if (password_verify($password, $data['password_hash'])) {
                // Simpan data ke session
                $_SESSION['user_id'] = $data['id'];
                $_SESSION['user_name'] = $data['name'];
                $_SESSION['user_role'] = $data['role'];

                // Arahkan ke dashboard sesuai role
                if ($data['role'] == 'admin') {
                    header("Location: ../../frontend/admin/dashboard_admin.php");
                } else {
                    header("Location: ../../frontend/user/dashboard_user.php");
                }
                exit;
            } else {
                echo "<script>alert('Password salah!'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Query gagal diproses!');</script>";
    }
}
?>
