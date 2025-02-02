<?php
// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root"; // اسم المستخدم الخاص بقاعدة البيانات
$password = ""; // كلمة المرور الخاصة بقاعدة البيانات
$dbname = "printing_db"; // اسم قاعدة البيانات

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استرجاع البيانات من النموذج
$paper_size = $_POST['paper_size'];
$quantity = $_POST['quantity'];

// استعلام SQL لاسترجاع السعر والخصم
$sql = "SELECT price_per_sheet, discount_threshold, discount_percentage 
        FROM PrintingPrices 
        WHERE paper_size = '$paper_size'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $price_per_sheet = $row['price_per_sheet'];
    $discount_threshold = $row['discount_threshold'];
    $discount_percentage = $row['discount_percentage'];

    // حساب السعر الإجمالي
    $total_price = $price_per_sheet * $quantity;

    // تطبيق الخصم إذا كانت الكمية أكبر من الحد المطلوب
    if (($paper_size == 'A4' && $quantity > $discount_threshold) || 
        ($paper_size == 'A5' && $quantity > $discount_threshold)) {
        $total_price = $total_price * (100 - $discount_percentage) / 100;
    }

    // إعادة توجيه النتيجة إلى الصفحة الرئيسية
    header("Location: index.php?total_price=$total_price");
} else {
    echo "لا توجد بيانات متاحة.";
}

// إغلاق الاتصال
$conn->close();
?>