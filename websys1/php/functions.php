<?php
session_start();
include_once("db/connect.php");

//$tag = test_input($_POST['tag']);

$tag = test_input($_REQUEST['tag']);

switch($tag)
{

    case "getRegisterStatus":
    getRegisterStatus($dbh);
    break;
    case "registeruser":
    registeruser($dbh);
    break;
    case "loginuser":
    loginuser($dbh);
    break;
    case "checklogin":
    checklogin($dbh);
    break;
    case "logout":
    logout($dbh);
    break;
    case "searchbooks":
    searchbooks($dbh);
    break;
    case "borrowbook":
    borrowbook($dbh);
    break;

}



function borrowbook($dbh)
{
    $res = array();

    if(checkloginboolean($dbh))
    {

        $sql = "SELECT value FROM settings WHERE name='user_borrow'";
        $stmt = $dbh->prepare($sql);
        if($stmt->execute())
        {
            $row = $stmt->fetch();
            $borrow_status = $row['value'];


            if($borrow_status==1)
            {
                $book_id = test_input($_POST['bookid']);


                $sql2 = "SELECT copynov FROM books WHERE id=:book_id";
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":book_id",$book_id);
                if($stmt2->execute()) {
                    $row2 = $stmt2->fetch();
                    $copynov = (int)$row2['copynov'];


                    if($copynov>0)
                    {

                        $sarray = $_SESSION["userlogin_visitor"];
                        $user_id = $sarray['userid'];



                        $sql6 = "SELECT COUNT(*) AS count FROM borrowing WHERE user_id=:user_id AND status IN(1,2)";
                        $stmt6 = $dbh->prepare($sql6);
                        $stmt6->bindParam(":user_id",$user_id);
                        if($stmt6->execute())
                        {

                            $row6 = $stmt6->fetch();
                            $count_all_borrow = $row6['count'];
                            if($count_all_borrow<4)
                            {

                        $sql5 = "SELECT COUNT(*) AS count FROM borrowing WHERE book_id=:book_id AND user_id=:user_id AND status IN(1,2)";
                        $stmt5 = $dbh->prepare($sql5);
                        $stmt5->bindParam(":book_id",$book_id);
                        $stmt5->bindParam(":user_id",$user_id);
                        if($stmt5->execute())
                        {

                            $row5 = $stmt5->fetch();
                            $countv = $row5['count'];


                            if($countv==0)
                            {

                        $now = time();
                        $secret = getRandomString(10);

                        $newcopynov = $copynov - 1;
                        $sql3 = "UPDATE books SET copynov=:newcopynov WHERE id=:book_id";
                        $stmt3 = $dbh->prepare($sql3);
                        $stmt3->bindParam(":newcopynov",$newcopynov);
                        $stmt3->bindParam(":book_id",$book_id);
                        if($stmt3->execute()) {
                        
                            $sql4 = "INSERT INTO borrowing(book_id,user_id,secret,create_date,warning_email_status,status) VALUES(:book_id,:user_id,:secret,:now,0,1)";
                            $stmt4 = $dbh->prepare($sql4);
                            $stmt4->bindParam(":book_id",$book_id);
                            $stmt4->bindParam(":user_id",$user_id);
                            $stmt4->bindParam(":secret",$secret);
                            $stmt4->bindParam(":now",$now);

                            if($stmt4->execute()) {
                                    $borrowing_id = $dbh->lastInsertId();
                                    
                                    $sql5 = "SELECT email,name FROM users WHERE id=:user_id AND type=2";
                                    $stmt5 = $dbh->prepare($sql5);
                                    $stmt5->bindParam(":user_id",$user_id);
                                    if($stmt5->execute()) {
                                        $row5 = $stmt5->fetch();
                                        $user_email = $row5['email'];
                                        $user_full_name = $row5['name'];
                                        
                                        
                                        $sql6 = "SELECT title,bookid,pagesno,author FROM books WHERE id=:book_id";
                                        $stmt6 = $dbh->prepare($sql6);
                                        $stmt6->bindParam(":book_id",$book_id);
                                        if($stmt6->execute())
                                        {
                                            $row6 = $stmt6->fetch();
                                            $book_title = $row6['title'];
                                            $book_full_id = $row6['bookid'];
                                            $book_pages_no = $row6['pagesno'];
                                            $book_author = $row6['author'];
                                            
                                            $to = $user_email;
                                            $subject = "تأكيد حجز الكتاب بعنوان ".$book_title." لاستلافه";

                                            $message = "
                                    <html>
                                        <head>
                                            <title>HTML email</title>
                                                </head>
                                                <body>
                                                <h2>مكتبة جامعة العلوم الاسلامية</h2>
                                                <h2>تأكيد حجز كتاب</h4>
                                                <br />
                                                <br />
                                                <h4>معلومات الكتاب</h2>
                                                <p> عنوان الكتاب: ".$book_title."</p>
                                                <p> رقم الكتاب: ".$book_full_id ."</p>
                                                <p> عدد الصفحات: ".$book_pages_no."</p>
                                                <p> مؤلف الكتاب: ".$book_author." </p>
                                                <br />
                                                <br />
                                                <h4>معلومات المستلف</h4>
                                                <p>الاسم: ".$user_full_name."</p>
                                                <p>الايميل: ".$user_email."</p>
                                                <br />
                                                <br />
                                                <h4>معلومات عملية الاستلاف</h4>
                                                <p>رقم عملية الاستلاف: ".$borrowing_id."</p>
                                                <p>الرقم السري: ".$secret."</p>
                                                
                                                <br>
                                                <br>
                                                <p style='color:red'>الرجاء استلاف الكتاب من المكتبة خلال 24 ساعة من عملية الحجز و الا فان الحجز سيلغى</p>
                                                
                                                <p style='color:red'>الرجاء اظهار رقم عملية الاستلاف و الرقم السري لعامل المكتبة حين الذهاب لالتقاط الكتاب</p>

                                                </body>
                                                    </html>
                                                ";

                                    // Always set content-type when sending HTML email
                                $headers = "MIME-Version: 1.0" . "\r\n";
                                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                                // More headers
                                    $headers .= 'From: <noreply@standardsi.com>' . "\r\n";
                                    mail($to,$subject,$message,$headers);
                                    
                                                                    $res['mess'] = "success";
                                $res['info'] = " لقد تم حجز الكتاب بنجاح يرجى الحفاظ على الرقم التالي و ابرازه حين اخذ الكتاب لعامل المكتبة  ".$secret." - الرجاء ملاحظة انه تم ارسال بريد الكتروني بكافة تفاصيل الحجز و يمكنك ايجاد هذا الرقم هناك ايضا";


                                    
                                        }
                                        else
                                        {
                                                                           $res['mess'] = "fail";
                                $res['info'] = implode(" - ",$stmt6->errorInfo());     
                                        }
                                        
                                        


                                    }
                                    else
                                    {
                                                                      $res['mess'] = "fail";
                                $res['info'] = implode(" - ",$stmt5->errorInfo());      
                                    }






                            }
                            else
                            {
                                $res['mess'] = "fail";
                                $res['info'] = implode(" - ",$stmt4->errorInfo());    
                            }
                        
                        }
                        else
                        {
                            $res['mess'] = "fail";
                            $res['info'] = implode(" - ",$stmt3->errorInfo());   
                        }
                    }
                    else
                    {
                        $res['mess'] = "fail";
                        $res['info'] = "  هذا الكتاب محجوز او مستلف من قبلك حاليا و لا يمكنك استعارة نسخة اخرى منه ";   
                    }

                    }
                    else
                    {
                        $res['mess'] = "fail";
                        $res['info'] = implode(" - ",$stmt5->errorInfo());  
                    }


                }
                else
                {
                    $res['mess'] = "fail";
                    $res['info'] = " لا يمكنك استلاف اكثر من 4 كتب في نفس الوقت ";   
                
                }
            }
            else
            {
                $res['mess'] = "fail";
                $res['info'] = implode(" - ",$stmt6->errorInfo());  
            }



                    }
                    else
                    {
                        $res['mess'] = "fail";
                        $res['info'] = " لا يمكنك استلاف هذا الكتاب - لان جميع النسخ مستلفة حاليا من مستخدمين اخرين ";
                    }


                }
                else
                {

                    $res['mess'] = "fail";
                    $res['info'] = implode(" - ",$stmt2->errorInfo());

                }


            }
            else
            {
                $res['mess'] = "fail";
                $res['info'] = " الاستلاف الان معطل من قبل الادارة - الرجاء المحاولة لاحقا ";
            }


        }
        else
        {
            $res['mess'] = "fail";
            $res['info'] = implode(" - ",$stmt->errorInfo());
        }



}
else
{
    $res['mess'] = "fail";
    $res['info'] = " الرجاء تسجيل الدخول قبل امكانية الاستلاف من المكتبة ";
}

    echo json_encode($res);     
}



function searchbooks($dbh)
{
    $res = array();

    $type = test_input($_POST['search_type']);
    $keyword = test_input($_POST['keyword']);


    if($type=="title") {
        $sql = "SELECT * FROM books WHERE title LIKE '%".$keyword."%' ORDER BY title";
    }
    else
    {
        $sql = "SELECT * FROM books WHERE author LIKE '%".$keyword."%' ORDER BY title"; 
    }  
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $res['mess'] = "success";
        $res['data'] = $stmt->fetchAll();
    }
    else
    {
        $res['mess'] = "fail";
        $res['info'] = implode(" - ",$stmt->errorInfo());
    }

    echo json_encode($res);     
}



function getRegisterStatus($dbh)
{
    $res = array();

    $sql = "SELECT value FROM settings WHERE name='user_register'";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $row = $stmt->fetch();
        $res['mess'] = "success";
        $res['register'] = $row['value'];
    }
    else
    {
        $res['mess'] = "fail";
        $res['info'] = implode(" - ",$stmt->errorInfo());
    }

    echo json_encode($res);     
}


function getRegisterStatusBoolean($dbh)
{
    $sql = "SELECT value FROM settings WHERE name='user_register'";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
           $row = $stmt->fetch();
            if((int)$row['value']==1)
            {
                return true;
            }
            else
            {
                return false;
            }
    }
    else
    {
                return false;
    }
  
}


function registeruser($dbh)
{

    $res = array();
if(getRegisterStatusBoolean($dbh))
{
    $name = test_input($_POST['name']);
    $email = test_input($_POST['email']);
    $password = test_input($_POST['password']);
    $passwordc = test_input($_POST['passwordc']);

    $pass = 1;
    $mess = "";

    if(mb_strlen($name)<3)
    {
        $pass = 0;
        $mess = $mess." - الاسم على الاقل يجب ان يكون 3 احرف";
    }


    if(empty($email))
    {
        $pass = 0;
        $mess = $mess." - الايميل مطلوب";
    }
    else
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $pass = 0;
            $mess = $mess." - يوجد مشكلة في هيئة الايميل ";   
        }
        else
        {

            if(!checkuseremailnotexist($dbh,$email))
            {
                $pass = 0;
                $mess = $mess." - هذا البريد الالكتروني مسجل لدينا في السابق الرجاء استخدام بريد الكتروني اخر او الذهاب للدخول"  ;    
            }

        }
    }

    if(mb_strlen($password)<6)
    {
        $pass = 0;
        $mess = $mess." -  يجب ان تكون كلمة السر على الاقل 6 احرف ";        
    }


    if(mb_strlen($passwordc) < 6)
    {
        $pass = 0;
        $mess = $mess." -  يجب ان تكون تأكيد كلمة السر على الاقل 6 احرف ";        
    }


    if($password!=$passwordc)
    {
        $pass = 0;
        $mess = $mess." - لا يوجد تطابق بين كلمة السر و تأكيدها ";     
    }


    if($pass == 1)
    {
        

        
        

        $websalt = "qazwsx_36";
        $salt = getRandomString(20);
        $hashedpassword = sha1($salt.$password.$websalt);

        $now = time();


        $sql = "INSERT INTO users(name,email,password,salt,type,create_date,status) VALUES(:name,:email,:hashedpassword,:salt,2,:now,0)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":name",$name);
        $stmt->bindParam(":email",$email);
        $stmt->bindParam(":hashedpassword",$hashedpassword);
        $stmt->bindParam(":salt",$salt);
        $stmt->bindParam(":now",$now);
        if($stmt->execute())
        {   
            $new_id_for_user = $dbh->lastInsertId();
            
                    $link_secret = getRandomString(20);
                    $valid_until = time()+(60*60*24);
                    $link = "https://www.standardsi.com/bookborrowing/validateuserreg.php?id=".$new_id_for_user."&secret=".$link_secret;
                    
                    $sql2 = "UPDATE users SET link_secret=:link_secret,validuntil=:valid_until where id=:new_id_for_user";
                    $stmt2 = $dbh->prepare($sql2);
                    $stmt2->bindParam(":new_id_for_user",$new_id_for_user);
                    $stmt2->bindParam(":link_secret",$link_secret);
                    $stmt2->bindParam(":valid_until",$valid_until);
                    if($stmt2->execute()) {
                        
                        $mess = "مرحبا بك سيد/سيدة ".$name." - لتفعيل حسابك الرجاء الضغط على الرابط التالي - ".$link;
                        $mess = wordwrap($mess,70);
                        
                        $to = $email;
                        $subject = "تفعيل حسابك في نظام الاستلاف من المكتبة";
                        $txt = $mess;
                        $headers = "From: noreply@standardsi.com";
                        mail($to,$subject,$txt,$headers);
                        
                        
                       $res['mess'] = "success"; 
                    }
                    else
                    {
                        $res['mess'] = "fail";
                        $res['info'] = implode(" - ",$stmt2->errorInfo());    
                    }
                    

            
        }
        else
        {
            $res['mess'] = "fail";
            $res['info'] = implode(" - ",$stmt->errorInfo());
        }

    }
    else
    {
        $res['mess'] = "fail";
        $res['info'] = $mess;  
    }

}
else
{
    $res['mess'] = "fail";
    $res['info'] = " التسجيل موقوف حاليا ";
}

    echo json_encode($res);

}


function checkuseremailnotexist($dbh,$email) {

    $sql = "SELECT COUNT(*) AS count FROM users WHERE email=:email";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":email",$email);
    if($stmt->execute())
    {
        $row = $stmt->fetch();
        $count = $row['count'];
        if($count<=0) {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }

}


function loginuser($dbh)
{
    $res = array();
    $username = test_input($_POST['email']);
    $password = test_input($_POST['password']);

    $pass = 1;
    $mess = "";

    if(empty($username))
    {
        $pass = 0;
        $mess = $mess." -  اسم المستخدم مطلوب و يجب ان يكون على هيئة بريد الكتروني ";
    }
    else
    {
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) 
        {
            $pass = 0;
            $mess = $mess." - يوجد مشكلة في هيئة الايميل ";   
        } 
    }

    if(mb_strlen($password)<6) {
        $pass = 0;
        $mess = $mess." -  كلمة السر يجب ان تكون على الاقل 6 احرف ";
    }



if($pass == 1)
{
    $websalt = "qazwsx_36";
    
    $sql = "SELECT COUNT(*) AS count,id,password,salt,email,name,status FROM users WHERE email=:username AND type=2";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam("username",$username);
    if($stmt->execute())
    {
        $row = $stmt->fetch();
        $salt = $row['salt'];
        $dbpass = $row['password'];
        $userid = $row['id'];
        $count = $row['count'];
        $name = $row['name'];
        $status = (int)$row['status'];

        if($count==1)
        {

        $hash = sha1($salt.$password.$websalt);

        if($dbpass==$hash)
        {
            if($status==1)
            // مفعل
            {


            $sarray = array();
            $sarray['userid'] = $userid;
            $sarray['name'] = $name;


            $_SESSION["userlogin_visitor"] = $sarray;

            $sql2 = "SELECT COUNT(*) AS count FROM logins WHERE userid=:userid";
            $stmt2 = $dbh->prepare($sql2);
            $stmt2->bindParam("userid",$userid);
            if($stmt2->execute())
            {

                $row2 = $stmt2->fetch();
                $count = $row2['count'];
                if($count>0)
                {

                    $sql3 = "DELETE FROM logins WHERE userid=:userid";
                    $stmt3 = $dbh->prepare($sql3);
                    $stmt3->bindParam("userid",$userid);
                    if($stmt3->execute())
                    {
                        $now = time();
                        $sql4 = "INSERT INTO logins(userid,create_date,status) VALUES(:userid,:now,1)";
                        $stmt4 = $dbh->prepare($sql4);
                        $stmt4->bindParam("userid",$userid);
                        $stmt4->bindParam("now",$now);
                        if($stmt4->execute())
                        {
                            $res['mess'] = "success";
                            $res['name'] = $name;
                        }
                    }
                    else
                    {
                        $res['mess'] = "fail";
                        $res['info'] = implode(" - ",$stmt3->errorInfo());  
                    }

                }
                else
                {
                    $now = time();
                    $sql4 = "INSERT INTO logins(userid,create_date,status) VALUES(:userid,:now,1)";
                    $stmt4 = $dbh->prepare($sql4);
                    $stmt4->bindParam("userid",$userid);
                    $stmt4->bindParam("now",$now);
                    if($stmt4->execute())
                    {
                        $res['mess'] = "success";
                    }
                }

            }
            else
            {
                $res['mess'] = "fail";
                $res['info'] = implode(" - ",$stmt2->errorInfo());  
            }


        }
        else if($status==0) {
            $res['mess'] = "fail";
            $res['info'] = " انت لم تقم بتفعيل حسابك بعد، الرجاء الذهاب الى بريدك الالكتروني و الضغط على رابط التفعيل الذي ارسلناه لك حين التسجيل";     
        }
        else if($status==2) {
            $res['mess'] = "fail";
            $res['info'] = " لقد تم حظر حسابك من قبل الادارة ";     
        }
        }
        else
        {
            $res['mess'] = "fail";
            $res['info'] = " خطأ في معلومات الدخول ";    
        }
    }
    else
    {
        $res['mess'] = "fail";
        $res['info'] = " خطأ في معلومات الدخول "; 
    }
    }
    else
    {
        $res['mess'] = "fail";
        $res['info'] = implode(" - ",$stmt->errorInfo());
    }
}
else
{
    $res['mess'] = "fail";
    $res['info'] = $mess;
}



    echo json_encode($res);
}



function checkloginboolean($dbh)
{

    if(isset($_SESSION["userlogin_visitor"]))
    {
        $sarray = $_SESSION["userlogin_visitor"];
        $userid = $sarray['userid'];
        $sql = "SELECT COUNT(*) AS count,name FROM users WHERE id=:userid AND type=2";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam("userid",$userid);
        if($stmt->execute())
        {

            $row = $stmt->fetch();
            $count = $row['count'];
            $name = $row['name'];
            if($count>0)
            {

                $sql2 = "SELECT COUNT(*) AS count FROM logins WHERE userid=:userid";
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam("userid",$userid);
                if($stmt2->execute())
                {
                    $row2 = $stmt2->fetch();
                    $count2 = $row2['count'];
                    if($count2>0)
                    {
                            return true;
                    }
                    else
                    {
                        return false;
                    }

                }
                
            }
            else
            {
                return false; 
            }

        }
        else
        {
            return false;
        }

    }
    else
    {
        return false;
    }

}

function checklogin($dbh)
{
$res = array();
    if(isset($_SESSION["userlogin_visitor"]))
    {
        $sarray = $_SESSION["userlogin_visitor"];
        $userid = $sarray['userid'];
        $sql = "SELECT COUNT(*) AS count,name FROM users WHERE id=:userid AND type=2";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam("userid",$userid);
        if($stmt->execute())
        {

            $row = $stmt->fetch();
            $count = $row['count'];
            $name = $row['name'];
            if($count>0)
            {

                $sql2 = "SELECT COUNT(*) AS count FROM logins WHERE userid=:userid";
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam("userid",$userid);
                if($stmt2->execute())
                {
                    $row2 = $stmt2->fetch();
                    $count2 = $row2['count'];
                    if($count2>0)
                    {
                        $res['mess'] = "success";  
                        $res['name'] = $name;   
                    }
                    else
                    {
                        $res['mess'] = "fail"; 
                    }

                }
                
            }
            else
            {
                $res['mess'] = "fail";   
            }

        }
        else
        {
            $res['mess'] = "fail";
            $res['error'] = implode(" - ",$stmt->errorInfo());           
        }

    }
    else
    {
        $res['mess'] = "fail";
        $res['error'] = "no permissions";      
    }

    echo json_encode($res);

}


function logout($dbh)
{
    $res = array();
    if(isset($_SESSION["userlogin_visitor"]))
    {

        $sarray = $_SESSION["userlogin_visitor"];
        $userid = $sarray['userid'];

                $sql2 = "SELECT COUNT(*) AS count FROM logins WHERE userid=:userid";
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam("userid",$userid);
                if($stmt2->execute())
                {
                    $row2 = $stmt2->fetch();
                    $count2 = $row2['count'];
                    if($count2>0)
                    {
                        $sql = "DELETE FROM logins WHERE userid=:userid";  
                        $stmt = $dbh->prepare($sql);
                        $stmt->bindParam("userid",$userid);
                        if($stmt->execute())
                        {
                            unset($_SESSION["userlogin_visitor"]);
                            $res['mess'] = "success";
                        }
                        else
                        {
                            $res['mess'] = "fail";    
                        }
                    }
                    else
                    {
                        $res['mess'] = "fail"; 
                    }

                }
                else
                {
                    $res['mess'] = "fail"; 
                }
            }
            else
            {
                $res['mess'] = "fail"; 
            }
                
    echo json_encode($res);
  
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }


  function getRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';

    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}

?>