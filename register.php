<?php
// ai-saturdays-form/register.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "your_username", "your_password", "your_database");
if ($conn->connect_error) {
    echo json_encode(["status" => false, "message" => "Database connection failed"]);
    exit;
}

$targetDir = "uploads/";
if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $hasSkills = $_POST["hasSkills"];
    $skillLevel = $_POST["skillLevel"];
    $interest = $_POST["interest"];
    $hours = $_POST["hours"];
    $hasComputer = $_POST["hasComputer"];
    $rules = $_POST["rules"];

    $imagePath = "";
    if (isset($_FILES["profilePicture"])) {
        $file = $_FILES["profilePicture"];
        $fileName = uniqid("profile_") . "_" . basename($file["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO registrations (first_name, last_name, email, phone, has_skills, skill_level, interest, hours, has_computer, rules, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $firstName, $lastName, $email, $phone, $hasSkills, $skillLevel, $interest, $hours, $hasComputer, $rules, $imagePath);

    if ($stmt->execute()) {
        echo json_encode(["status" => true, "message" => "Registration successful"]);
    } else {
        echo json_encode(["status" => false, "message" => "Database insert failed"]);
    }

    $stmt->close();
    $conn->close();
}
?>
