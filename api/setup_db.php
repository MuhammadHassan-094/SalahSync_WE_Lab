<?php
// Setup script to create the database and tables if they don't exist
header("Content-Type: text/html");
echo "<h2>Setting up Prayer Tracker Database</h2>";

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$db_name = "prayer_tracker";

try {
    // Connect to MySQL server without specifying a database
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to MySQL server successfully.</p>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Database '$db_name' created or already exists.</p>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Connect to the database
    $conn->select_db($db_name);
    
    // Create prayers table
    $sql = "CREATE TABLE IF NOT EXISTS prayers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(50) NOT NULL,
        prayer_id VARCHAR(20) NOT NULL,
        status ENUM('pending', 'completed') NOT NULL DEFAULT 'pending',
        date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_prayer (user_id, prayer_id, date)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Table 'prayers' created or already exists.</p>";
    } else {
        throw new Exception("Error creating table: " . $conn->error);
    }
    
    // Insert some sample data if the table is empty
    $result = $conn->query("SELECT COUNT(*) as count FROM prayers");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        echo "<p>Adding sample prayer data...</p>";
        
        $user_id = 'sample_user';
        $today = date('Y-m-d');
        $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
        $statuses = ['completed', 'completed', 'completed', 'pending', 'pending'];
        
        $stmt = $conn->prepare("INSERT INTO prayers (user_id, prayer_id, status, date) VALUES (?, ?, ?, ?)");
        
        for ($i = 0; $i < count($prayers); $i++) {
            $stmt->bind_param('ssss', $user_id, $prayers[$i], $statuses[$i], $today);
            $stmt->execute();
        }
        
        echo "<p>Sample data added successfully.</p>";
    } else {
        echo "<p>Table already contains data. Skipping sample data insertion.</p>";
    }
    
    // Success message
    echo "<div style='background-color: #dff0d8; color: #3c763d; padding: 15px; border-radius: 4px; margin-top: 20px;'>";
    echo "<h3>Setup Completed Successfully</h3>";
    echo "<p>Your database is now ready to use with the Prayer Tracker application.</p>";
    echo "</div>";
    
    // Display database info
    echo "<h3>Database Information:</h3>";
    echo "<ul>";
    echo "<li>Host: $host</li>";
    echo "<li>Database: $db_name</li>";
    echo "<li>Tables: prayers</li>";
    echo "</ul>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div style='background-color: #f2dede; color: #a94442; padding: 15px; border-radius: 4px; margin-top: 20px;'>";
    echo "<h3>Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
    
    echo "<h3>Troubleshooting Tips:</h3>";
    echo "<ol>";
    echo "<li>Make sure your MySQL server is running.</li>";
    echo "<li>Check that the username and password are correct.</li>";
    echo "<li>Ensure the MySQL user has rights to create databases and tables.</li>";
    echo "<li>If using XAMPP, make sure MySQL service is started in the XAMPP Control Panel.</li>";
    echo "</ol>";
}
?> 