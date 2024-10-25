<?php
// Thông tin đăng nhập cơ sở dữ liệu
$server = "localhost";
$username = "root";
$password = "";
$database = "db_ho_ten";

// Tạo kết nối
$conn = new mysqli($server, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Khởi tạo các biến đếm
$insertedCount = 0;
$failedCount = 0;

if (isset($_POST['submit'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        // Lấy đường dẫn file tạm
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileType = $_FILES['file']['type'];

        // Kiểm tra xem file có phải là file TXT hay không
        if ($fileType == 'text/plain') {
            // Đọc file
            $file = fopen($fileTmpPath, "r");
            if ($file) {
                while (($line = fgets($file)) !== false) {
                    $data = explode("|", $line);
                    $title = trim($data[0]);
                    $description = trim($data[1]);
                    $imageurl = trim($data[2]);

                    // Kiểm tra xem title đã tồn tại chưa
                    $checkQuery = "SELECT id FROM courses WHERE title = '$title'";
                    $result = $conn->query($checkQuery);

                    if ($result->num_rows == 0) {
                        // Chèn dữ liệu vào cơ sở dữ liệu
                        $insertQuery = "INSERT INTO courses (title, description, imageurl) VALUES ('$title', '$description', '$imageurl')";
                        if ($conn->query($insertQuery) === TRUE) {
                            $insertedCount++;
                        } else {
                            $failedCount++;
                        }
                    } else {
                        $failedCount++;
                    }
                }
                fclose($file);

                // Thông báo kết quả
                echo "<div class='alert alert-info mt-2' role='alert'>
                        $insertedCount bản ghi được chèn thành công, $failedCount bản ghi chèn thất bại
                      </div>";
            }
        } else {
            echo '<div class="alert alert-warning" role="alert">Vui lòng tải lên file .txt!</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Lỗi tải file!</div>';
    }
}

// Đóng kết nối
$conn->close();
?>
