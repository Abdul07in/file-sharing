# 📂 Secure File & Text Sharing App

A lightweight, secure, and user-friendly web application for sharing files and text snippets anonymously. Built with vanilla PHP and MySQL.

**[Live Demo](https://abdulkanoor.orgfree.com/file-sharing)**

## ✨ Features

- **File Sharing**: Upload files and generate a unique 4-digit PIN.
- **Text Sharing**: Securely share code snippets or messages with a PIN.
- **Secure Retrieval**: Access shared content instantly using the generated PIN.
- **Encryption**: Files and text are secured using encryption mechanisms (implied by IV storage).
- **Auto Cleanup**: Includes an automated cleanup script to manage storage.
- **Responsive Design**: optimized for mobile and desktop usage.

## 🚀 Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server (or PHP built-in server for testing)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Abdul07in/file-sharing.git
   cd file-sharing
   ```

2. **Configure Database**
   - Create a new MySQL database.
   - Import the `database.sql` file located in the root directory.
   - Update your database connection credentials in `src/config.php` (or equivalent configuration file).

3. **Directory Permissions**
   Ensure the `uploads/` directory is writable:
   ```bash
   chmod 777 uploads
   ```

4. **Run the Application**
   You can use the built-in PHP server for local development:
   ```bash
   php -S localhost:8000
   ```
   Visit `http://localhost:8000` in your browser.

## 🛠️ Tech Stack

- **Backend**: PHP (Vanilla)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Custom CSS

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is licensed under the [MIT License](LICENSE).
