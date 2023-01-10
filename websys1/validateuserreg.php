<?php
include_once("php/db/connect.php");


if(isset($_GET['id']) && isset($_GET['secret']))
{
$userid = $_GET['id'];
$secret = $_GET['secret'];

$sql = "SELECT link_secret,validuntil,status FROM users WHERE id=:userid";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":userid",$userid);
if($stmt->execute()) {
    $row = $stmt->fetch();
    $db_secret = $row['link_secret'];
    $db_validuntil = $row['validuntil'];
    $db_status = $row['status'];
    
    if($db_status==0)
    {
     
    if($db_validuntil>time())
    {
       
       if($db_secret==$secret)
       {
            $sql2 = "UPDATE users SET status=1 WHERE id=:userid";
            $stmt2 = $dbh->prepare($sql2);
            $stmt2->bindParam(":userid",$userid);
            if($stmt2->execute())
            {
                $title = " نجاح ";
                $mess = " لقد تم تفعيل حسابك بنجاح سيتم تحويلك الى صفحة الدخول ";
                $url = "login.html";
            }
            else
            {
                  $title = " مشكلة ";
                  $mess = "مشكلة في السيرفر -لم يتم تفعيل الحساب";
                  $url = "index.html";
            }
            
       }
       else
       {
                          $title = " مشكلة ";
        $mess = " هذا الرابط معطوب بسبب التلاعب به الرجاء الاتصال بالادارة لتفعيل حسابك ";
        $url = "index.html";
       }
        
    }
    else
    {
                          $title = " مشكلة ";
        $mess = " هذا الرابط منتهي الصلاحية الرجاء الاتصال بالادارة لتفعيل حسابك او التسجيل ببريد الكتروني اخر ";
        $url = "index.html";
    }
    }
    else
    {
                          $title = " مشكلة ";
        $mess = " لقد تم تفعيل هذاالحساب من قبل ";
        $url = "index.html";
    }
    
}
else
{
                      $title = " مشكلة ";
    $mess = "مشكلة في السيرفر - لم يتم تفعيل الحساب";
    $url = "index.html";
}
}
else
{
    $title = " مشكلة ";
    $mess = "مشكلة في السيرفر - لم يتم تفعيل الحساب";
    $url = "index.html";    
}


?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <title>نظام استعارة الكتب الالكتروني - الصفحة الرئيسية</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

 <link href="css/style.css" rel="stylesheet">

 <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
 
 <script>

$("document").ready(function() {
    
    setTimeout(function() {
        
        
        Swal.fire({
  title: '<?php echo $title; ?>',
  text: "<?php echo $mess; ?>",
  icon: "<?php if($url=='index.html') { echo 'warning'; } else { echo 'success';  } ?>",
  confirmButtonColor: '#3085d6',
  confirmButtonText: 'نعم',
  allowOutsideClick: false
}).then((result) => {
  if (result.isConfirmed) {
window.location = "<?php echo $url; ?>";
  }
})
          
    }, 3000);
    
})

</script>




</head>
<body>
<h1>الرجاء عدم الخروج و الانتظار</h1>
</body>
</html>