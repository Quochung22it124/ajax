<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chức năng Live Search bằng PHP và AJAX</title>
<style type="text/css">
 body{
 font-family: Arail, sans-serif;
 }
 /* CSS cho search box */
 .search-box{
 width: 300px;
 position: relative;
 display: inline-block;
 font-size: 14px;
 }
 .search-box input[type="text"]{
 height: 32px;
 padding: 5px 10px;
 border: 1px solid #CCCCCC;
 font-size: 14px;
 }
 .result{
 position: absolute; 
 z-index: 999;
 top: 100%;
 left: 0;
 }
 .search-box input[type="text"], .result{
 width: 100%;
 box-sizing: border-box;
 }
 /* CSS cho kết quả */
 .result p{
 margin: 0;
 padding: 7px 10px;
 border: 1px solid #CCCCCC;
 border-top: none;
 cursor: pointer;
 }
 .result p:hover{
 background: #f2f2f2;
 }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.search-box input[type="text"]').on("keyup input", function(){
            var inputVal = $(this).val();
            var resultDropdown = $(this).siblings(".result");
            if(inputVal.length){
                $.get("backend-search.php", {term: inputVal}).done(function(data){
                    resultDropdown.html(data);
                });
            }else{
                resultDropdown.empty();
            }
        });
        $(document).on("click", ".result p", function(){
            $(this).parents(".search-box").find('input[type="text"]').val($(this).text())
            $(this).parent(".result").empty();
        });
    });
</script>
</head>
<body>
 <div class="search-box">
 <input type="text" autocomplete="off" placeholder="Search tên học phần..." />
 <div class="result"></div>
 </div>
</body>
</html>
<?php
/* Cố gắng kết nối đến MySQL server. Giả sử bạn đang chạy MySQL server mặc đinh (user là 'root' và 
không có mật khẩu */
$link = mysqli_connect("localhost", "root", "", "udn");
// Kiểm tra kết nối
if($link === false){
 die("ERROR: Không thể kết nối. " . mysqli_connect_error());
}
if(isset($_REQUEST["term"])){
 // Chuẩn bị câu lệnh SQL SELECT
 $sql = "SELECT * FROM hocphan WHERE tenhp LIKE ?";
 
 if($stmt = mysqli_prepare($link, $sql)){
 // Liên kết biến đến câu lệnh đã chuẩn bị như là tham số
 mysqli_stmt_bind_param($stmt, "s", $param_term);
 
 // Thiết lập các tham số
 $param_term = $_REQUEST["term"] . '%';
 
 // Cố gắng thực thi câu lệnh đã chuẩn bị
 if(mysqli_stmt_execute($stmt)){
 $result = mysqli_stmt_get_result($stmt);
 
 // Kiểm tra số lượng row trong kết quả
 if(mysqli_num_rows($result) > 0){
 // Tìm nạp các hàng kết quả dưới dạng mảng kết hợp
 while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
 echo "<p>" . $row["tenhp"] . "</p>";
 }
 } else{
 echo "<p>Không tìm thấy kết quả nào</p>";
 }
 } else{
 echo "ERROR: Không thể thực thi câu lệnh $sql. " . mysqli_error($link);
 }
 }
 
 // Đóng câu lệnh
 mysqli_stmt_close($stmt);
}
// Đóng kết nối
mysqli_close($link);
?>