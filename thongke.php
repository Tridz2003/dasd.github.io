<?php
    // Kết nối đến cơ sở dữ liệu MySQL
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "exammanagement";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Thực hiện câu truy vấn
    $sql = "SELECT student.StudentID, Name, SUM(Participation) AS Total_of_Participations, ROUND(SUM(Score)/30*100, 2) AS Completion_rate, ROUND(SUM(Score)/3, 2) AS Average FROM student JOIN student_subject on student.StudentID = student_subject.StudentID GROUP BY student.StudentID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // In ra dữ liệu
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr>";
        echo "<th style='border: 1px solid black;'>StudentID</th>";
        echo "<th style='border: 1px solid black;'>Name</th>";
        echo "<th style='border: 1px solid black;'>Total of Participations</th>";
        echo "<th style='border: 1px solid black;'>Completion rate</th>";
        echo "<th style='border: 1px solid black;'>Average</th>";
        echo "</tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='border: 1px solid black;'>" . $row["StudentID"]. "</td>";
            echo "<td style='border: 1px solid black;'>" . $row["Name"]. "</td>";
            echo "<td style='border: 1px solid black;'>" . $row["Total_of_Participations"]. "</td>";
            echo "<td style='border: 1px solid black;'>" . $row["Completion_rate"]. "%</td>";
            echo "<td style='border: 1px solid black;'>" . $row["Average"]. "</td>";
            echo "</tr>";
        }

        echo "</table>";
        $averages = array();
        while($row = $result->fetch_assoc()) {
            $averages[] = $row["Average"];
        }

        // Tạo các mốc cho biểu đồ cột
        $data = array(
            ">=9" => 0,
            "8-8.9" => 0,
            "7-7.9" => 0,
            "5-6.9" => 0,
            "Remain" => 0
        );

        // Phân loại dữ liệu vào các mốc
        foreach ($averages as $average) {
            if ($average >= 9) {
                $data[">=9"]++;
            } elseif ($average >= 8) {
                $data["8-8.9"]++;
            } elseif ($average >= 7) {
                $data["7-7.9"]++;
            } elseif ($average >= 5) {
                $data["5-6.9"]++;
            } else {
                $data["Remain"]++;
            }
        }

        // In ra biểu đồ cột
        echo "<h2>Average Score Distribution</h2>";
        echo "<canvas id='chart' width='400' height='300'></canvas>";

        // JavaScript để vẽ biểu đồ cột
        echo "<script>";
        echo "var ctx = document.getElementById('chart').getContext('2d');";
        echo "var myChart = new Chart(ctx, {";
        echo "type: 'bar',";
        echo "data: {";
        echo "labels: ['>=9', '8-8.9', '7-7.9', '5-6.9', 'Remain'],";
        echo "datasets: [{";
        echo "label: 'Average Score Distribution',";
        echo "data: [" . implode(",", $data) . "],";
        echo "backgroundColor: [";
        echo "'rgba(255, 99, 132, 0.2)',";
        echo "'rgba(54, 162, 235, 0.2)',";
        echo "'rgba(255, 206, 86, 0.2)',";
        echo "'rgba(75, 192, 192, 0.2)',";
        echo "'rgba(153, 102, 255, 0.2)'";
        echo "],";
        echo "borderColor: [";
        echo "'rgba(255, 99, 132, 1)',";
        echo "'rgba(54, 162, 235, 1)',";
        echo "'rgba(255, 206, 86, 1)',";
        echo "'rgba(75, 192, 192, 1)',";
        echo "'rgba(153, 102, 255, 1)'";
        echo "],";
        echo "borderWidth: 1";
        echo "}]";
        echo "},";
        echo "options: {";
        echo "scales: {";
        echo "yAxes: [{";
        echo "ticks: {";
        echo "beginAtZero: true";
        echo "}";
        echo "}]";
        echo "}";
        echo "}";
        echo "});";
        echo "</script>";
    } else {
        echo "0 results";
    }
    $conn->close();
?>
