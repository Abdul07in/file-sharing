<?php

namespace App\Controller;

use App\Config\Config;
use App\Model\User;
use PDO;
use RuntimeException;

class AuthController
{
    private $pdo;
    private $userModel;

    public function __construct()
    {
        $dsn = "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME . ";charset=" . Config::$DB_CHARSET;
        $this->pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->userModel = new User($this->pdo);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByUsername($username);

            if ($user && $this->userModel->verifyPassword($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: ./dashboard');
                exit;
            } else {
                header('Location: ./login?error=Invalid credentials');
                exit;
            }
        }

        // Show login view
        $view = 'login';
        require __DIR__ . '/../../views/layout.php';
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($password !== $confirmPassword) {
                header('Location: ./register?error=Passwords do not match');
                exit;
            }

            if (strlen($password) < 6) {
                header('Location: ./register?error=Password must be at least 6 characters');
                exit;
            }

            try {
                $this->userModel->create($username, $password);
                header('Location: ./login?success=Account created. Please login.');
                exit;
            } catch (RuntimeException $e) {
                header('Location: ./register?error=' . urlencode($e->getMessage()));
                exit;
            }
        }

        // Show register view
        $view = 'register';
        require __DIR__ . '/../../views/layout.php';
    }

    public function logout()
    {
        session_destroy();
        header('Location: ./');
        exit;
    }
}
