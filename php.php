RESULT.php 
<?php 
include('../db/connection.php'); 
$studentData = null;
$error = ''; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
 $studentID = $_POST['student_id']; 
 $stmt = $conn->prepare("SELECT * FROM Results WHERE StudentID = ?");  $stmt->bind_param("s", $studentID); 
 $stmt->execute(); 
 $result = $stmt->get_result(); 
 if ($result->num_rows > 0) { 
 $studentData = $result->fetch_assoc(); 
 } else { 
 $error = "No result found for Student ID: $studentID"; 
 } 
} 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
 <meta charset="UTF-8"> 
 <meta name="viewport" content="width=device-width, initial-scale=1.0">  <title>Student Result</title> 
 <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> 
 <style> 
 body { 
 font-family: Arial, sans-serif; 
 margin: 30px; 
 background-color: #f4f7fa; 
 } 
 .container { 
 background-color: #fff; 
 padding: 30px; 
 border-radius: 10px; 
 box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
 } 
 h2 { 
 text-align: center; 
 margin-bottom: 20px; 
 color: #007bff; 
 } 
 .form-box { 
 margin-bottom: 20px; 
 } 
 .form-box label { 
 font-weight: bold; 
 color: #333; 
 } 
 .form-box input[type="text"] {
 padding: 10px; 
 font-size: 16px; 
 width: 100%; 
 border: 1px solid #ccc;  border-radius: 5px; 
 margin-bottom: 10px;  } 
 .form-box button { 
 background-color: #007bff;  color: #fff; 
 border: none; 
 padding: 10px 20px;  font-size: 16px; 
 border-radius: 5px; 
 cursor: pointer; 
 width: 100%; 
 } 
 .form-box button:hover {  background-color: #0056b3;  } 
 table { 
 width: 100%; 
 margin-top: 20px; 
 border-collapse: collapse;  text-align: left; 
 } 
 table th, table td { 
 padding: 12px; 
 border: 1px solid #ddd;  font-size: 16px; 
 } 
 table th { 
 background-color: #007bff;  color: #fff; 
 } 
 table td { 
 background-color: #f9f9f9;  } 
 .error-message { 
 color: red; 
 font-weight: bold; 
 text-align: center; 
 margin-top: 20px; 
 } 
 .download-btn { 
 margin-top: 20px; 
 text-align: center; 
 } 
 .download-btn button {  background-color: #28a745;  color: white;
 padding: 10px 20px; 
 border: none; 
 font-size: 16px; 
 border-radius: 5px; 
 } 
 .download-btn button:hover { 
 background-color: #218838; 
 } 
 </style> 
</head> 
<body> 
 <div class="container"> 
 <h2>View Your Result</h2> 
 <div class="form-box"> 
 <form method="POST"> 
 <label>Enter Student ID:</label> 
 <input type="text" name="student_id" required> 
 <button type="submit">View Result</button> 
 </form> 
 </div> 
 <?php if ($error): ?> 
 <p class="error-message"><?= $error ?></p> 
 <?php endif; ?> 
 <?php if ($studentData): ?> 
 <h3>Result for <?= htmlspecialchars($studentData['Name']) ?> (<?=  $studentData['StudentID'] ?>)</h3> 
 <table> 
 <tr><th>Semester</th><td><?= $studentData['Semester'] ?></td></tr>  <tr><th>Web Tech</th><td><?= $studentData['WebTech'] ?></td></tr>  <tr><th>AdvJava</th><td><?= $studentData['AdvJava'] ?></td></tr>  <tr><th>AI</th><td><?= $studentData['AI'] ?></td></tr> 
 <tr><th>Python</th><td><?= $studentData['Python'] ?></td></tr>  <tr><th>SSMDA</th><td><?= $studentData['SSMDA'] ?></td></tr>  <tr><th>Total Marks</th><td><?= $studentData['TotalMarks'] ?></td></tr>  <tr><th>Percentage</th><td><?= $studentData['Percentage'] ?>%</td></tr>  <tr><th>Class</th><td><?= $studentData['Class'] ?></td></tr> 
 </table> 
 <div class="download-btn"> 
 <form method="POST" action="download_result.php"> 
 <input type="hidden" name="student_id" value="<?= $studentData['StudentID'] ?>">  <button type="submit">Download Result as PDF</button> 
 </form> 
 </div> 
 <?php endif; ?> 
 </div> 
</body> 
</html>

UPLOAD.php 
<?php 
require '../db/connection.php'; 
require '../vendor/autoload.php'; // This will work after we install PhpSpreadsheet use PhpOffice\PhpSpreadsheet\IOFactory; 
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
 $file = $_FILES['excel']['tmp_name']; 
 $spreadsheet = IOFactory::load($file); 
 $sheet = $spreadsheet->getActiveSheet(); 
 $rows = $sheet->toArray(); 
 for ($i = 1; $i < count($rows); $i++) { 
 $data = $rows[$i]; 
 $stmt = $conn->prepare("INSERT INTO Results (StudentID, Name, Semester, WebTech,  AdvJava, Ai, Python, SSMDA, TotalMarks, Percentage, Class) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");  $stmt->bind_param("sssiiiiidis", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10]); 
 $stmt->execute(); 
 } 
 echo "<div class='alert alert-success mt-3'>Excel uploaded successfully.</div>"; } 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
 <meta charset="UTF-8"> 
 <meta name="viewport" content="width=device-width, initial-scale=1.0">  <title>Upload Result Excel</title> 
 <!-- Bootstrap CDN --> 
 <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> 
 <!-- Custom CSS --> 
 <style> 
 body { 
 background: linear-gradient(to right, #6a11cb, #2575fc); 
 color: white; 
 font-family: Arial, sans-serif; 
 display: flex; 
 justify-content: center; 
 align-items: center; 
 min-height: 100vh; 
 } 
 .container { 
 background: rgba(0, 0, 0, 0.7); 
 border-radius: 15px; 
 padding: 40px;
 box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);  width: 80%; 
 max-width: 500px; 
 text-align: center; 
 } 
 h2 { 
 margin-bottom: 20px; 
 color: #ff7f50; 
 text-transform: uppercase; 
 } 
 form { 
 margin-top: 30px; 
 } 
 input[type="file"] { 
 margin: 15px 0; 
 padding: 12px; 
 font-size: 1rem; 
 border: none; 
 border-radius: 5px; 
 background-color: #ff7f50; 
 color: white; 
 cursor: pointer; 
 transition: transform 0.3s ease;  } 
 input[type="file"]:hover { 
 transform: scale(1.05); 
 } 
 button { 
 padding: 12px 20px; 
 font-size: 1.1rem; 
 background-color: #ff7f50; 
 color: white; 
 border: none; 
 border-radius: 5px; 
 cursor: pointer; 
 transition: transform 0.3s ease;  } 
 button:hover { 
 transform: scale(1.05); 
 } 
 .alert { 
 font-size: 1.1rem; 
 border-radius: 5px; 
 } 
 .footer { 
 position: fixed; 
 bottom: 10px; 
 width: 100%; 
 text-align: center; 
 color: #fff; 
 font-size: 0.9rem;
 } 
 </style> 
</head> 
<body> 
 <div class="container"> 
 <h2>Upload Result Excel</h2> 
 <form method="POST" enctype="multipart/form-data"> 
 <input type="file" name="excel" required> 
 <button type="submit">Upload</button> 
 </form> 
 </div> 
 <!-- Footer --> 
 <div class="footer"> 
 <p>&copy; 2025 Result Management System. All rights reserved.</p> 
 </div> 
 <!-- Bootstrap JS --> 
 <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> 
 <script  
src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>  <script  
src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> </body> 
</html> 
