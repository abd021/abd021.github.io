<?php
session_start();
include_once("db/connect.php");

//$tag = test_input($_POST['tag']);

$tag = test_input($_REQUEST['tag']);

switch($tag)
{
    case "login":
    login($dbh);
    break;
    case "checklogin":
    checklogin($dbh);
    break;
    case "logout":
    logout($dbh);
    break;
    case "addbook":
    addbook($dbh);
    break;
    case "getbooks":
    getbooks($dbh);
    break;
    case "getbook":
    getbook($dbh);
    break;
    case "editbook":
    editbook($dbh);
    break;
    case "deletebook":
    deletebook($dbh);
    break;
    case "enablereg":
    enablereg($dbh);
    break;
    case "disablereg":
    disablereg($dbh);
    break;
    case "enableborrow":
    enableborrow($dbh);
    break;
    case "disableborrow":
    disableborrow($dbh);
    break;
    case "getsettings":
    getsettings($dbh);
    break;
    case "getusers":
    getusers($dbh);
    break;
    case "banuser":
    banuser($dbh);
    break;
    case "activateuser":
    activateuser($dbh);
    break;
    case "deleteuser":
    deleteuser($dbh);
    break;
    case "getallregistredborrowings":
    getallregistredborrowings($dbh);
    break;
    case "confirmborrowing":
    confirmborrowing($dbh);
    break;
    case "getalltakenborrowings":
    getalltakenborrowings($dbh);
    break;
    case "confirmreturnbookborrowing":
    confirmreturnbookborrowing($dbh);
    break;
    case "getallarchiveborrowings":
    getallarchiveborrowings($dbh);
    break;
    case "changepassword":
    changepassword($dbh);
    break;
    case "getstat":
    getstat($dbh);
    break;
}

function getstat($dbh) {
 
    $res = array();
    if(islogged($dbh))
    {

        $sql = "SELECT COUNT(*) AS count FROM books";
        $stmt = $dbh->prepare($sql);
        if($stmt->execute()) {

            $row = $stmt->fetch();
            $count = $row['count'];
            $res['bookc'] = $count;

        }
        else
        {
            $res['mess'] = "fail";
            $res['info'] = implode(" - ",$stmt->errorInfo());
        }



        $sql = "SELECT COUNT(*) AS count FROM users where type=2";
        $stmt = $dbh->prepare($sql);
        if($stmt->execute()) {

            $row = $stmt->fetch();
            $count = $row['count'];
            $res['userc'] = $count;

        }
        else
        {
            $res['mess'] = "fail";
            $res['info'] = implode(" - ",$stmt->errorInfo());
        }



        $sql = "SELECT COUNT(*) AS count FROM borrowing WHERE status in(1,2)";
        $stmt = $dbh->prepare($sql);
        if($stmt->execute()) {

            $row = $stmt->fetch();
            $count = $row['count'];
            $res['takenc'] = $count;

        }
        else
        {
            $res['mess'] = "fail";
            $res['info'] = implode(" - ",$stmt->errorInfo());
        }



        $sql = "SELECT COUNT(*) AS count FROM borrowing WHERE status=3";
        $stmt = $dbh->prepare($sql);
        if($stmt->execute()) {

            $row = $stmt->fetch();
            $count = $row['count'];
            $res['archivec'] = $count;
            $res['mess'] = "success";

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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);  
   

}

function changepassword($dbh) {
    $res = array();
    if(islogged($dbh))
    {

        $oldpassword = test_input($_POST['cpassword']);
        $newpassword = test_input($_POST['newpassword']);
        $newpasswordc = test_input($_POST['newpasswordc']);
    
        $pass = 1;
        $mess = "";
    

    
        if(mb_strlen($oldpassword)<6)
        {
            $pass = 0;
            $mess = $mess." -  يجب ان تكون كلمة السر القديمة على الاقل 6 احرف ";        
        }
        else
        {

            $sarray = $_SESSION['userlogin'];
            $user_id = $sarray['id'];

            $sql = "SELECT password,salt FROM users WHERE id=:user_id AND type=1";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(":user_id",$user_id);
            if($stmt->execute()) {
                $row = $stmt->fetch();
                $db_password = $row['password'];
                $db_salt = $row['salt']; 

                $websalt = "bbookssystem2022";

                $hashed = sha1($db_salt.$oldpassword.$websalt);

                if($db_password!=$hashed)
                {
                    $pass = 0;
                    $mess = $mess." - كلمة السر الحالية المدخلة خاطئة ";
                }

            }
            else
            {
                $pass = 0;
                $error = implode(" - ",$stmt->errorInfo());
                $mess = $mess." - ".$error;
            }
        }
    
    
        if(mb_strlen($newpassword) < 6)
        {
            $pass = 0;
            $mess = $mess." -  يجب ان تكون كلمة السر الجديدة على الاقل 6 احرف ";        
        }


        if(mb_strlen($newpasswordc) < 6)
        {
            $pass = 0;
            $mess = $mess." -  يجب ان تكون تأكيد كلمة السر الجديدة على الاقل 6 احرف ";        
        }
    
    
        if($newpassword!=$newpasswordc)
        {
            $pass = 0;
            $mess = $mess." - لا يوجد تطابق بين كلمة السر الجديدة و تأكيدها ";     
        }
    
    
        if($pass == 1)
        {
    
            $sarray = $_SESSION['userlogin'];
            $user_id = $sarray['id'];
            
            $websalt = "bbookssystem2022";
            $new_salt = getRandomString(20);
            $new_hashedpassword = sha1($new_salt.$newpassword.$websalt);
    
            $now = time();
    
    
            $sql = "UPDATE users SET password=:new_hashedpassword,salt=:new_salt WHERE id=:user_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(":new_hashedpassword",$new_hashedpassword);
            $stmt->bindParam(":new_salt",$new_salt);
            $stmt->bindParam(":user_id",$user_id);
            if($stmt->execute())
            {
                $row = $stmt->fetch();
                $res['mess'] = "success";
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
        $res['info'] = "No Permissions";
    }
    
        echo json_encode($res);
    
}

function getallarchiveborrowings($dbh) {

    $res = array();
    if(islogged($dbh))
    {

        $sql = "SELECT borrowing.id AS bid,borrowing.t_date AS taken_date,borrowing.return_date,users.name,users.email,books.title,books.bookid AS bookidg FROM borrowing INNER JOIN users ON borrowing.user_id=users.id INNER JOIN books ON borrowing.book_id=books.id WHERE borrowing.status=3";
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
}
else
{
    $res['mess'] = "fail";
    $res['info'] = "No Permissions";
}

    echo json_encode($res);  

}

function confirmreturnbookborrowing($dbh) {
    $res = array();
    if(islogged($dbh))
    {

        $id = test_input($_POST['id']);
        $now = time();

        $sql = "UPDATE borrowing SET return_date=:now,status=3 WHERE id=:id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":now",$now);
        $stmt->bindParam(":id",$id);
        if($stmt->execute())
        {

            $sql2 = "SELECT book_id FROM borrowing WHERE id=:id";
            $stmt2 = $dbh->prepare($sql2);
            $stmt2->bindParam(":id",$id);
            if($stmt2->execute()) {

                $row2 = $stmt2->fetch();
                $book_id = $row2['book_id'];

                $sql3 = "UPDATE books SET copynov = copynov + 1 WHERE id=:book_id";
                $stmt3 = $dbh->prepare($sql3);
                $stmt3->bindParam(":book_id",$book_id);
                if($stmt3->execute()) {








            $sql4= "SELECT book_id,user_id FROM borrowing WHERE id=:id";
            $stmt4 = $dbh->prepare($sql4);
            $stmt4->bindParam(":id",$id);
            if($stmt4->execute())
            {
                $row4 = $stmt4->fetch();
                $user_id = $row4['user_id'];
                $book_id = $row4['book_id'];
            
            
            
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
                                            $subject = "تأكيد عملية ارجاع كتاب بعنوان  ".$book_title;

                                            $message = "
                                    <html>
                                        <head>
                                            <title>HTML email</title>
                                                </head>
                                                <body>
                                                <h2>مكتبة جامعة العلوم الاسلامية</h2>
                                                <h2>تأكيد ارجاع كتاب</h4>
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
                                                <p>رقم عملية الاستلاف: ".$id."</p>
                                                
                                                <br>
                                                <br>
                                                <p style='color:green'>شكرا لك على ارجاع الكتاب</p>
                                                
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
                    $res['info'] = " لقد تم ارجاع الكتاب بنجاح ";


                                    
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
                $res['info'] = implode(" - ",$stmt2->errorInfo());    
            }


            $res['mess'] = "success";
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);     
}

function getalltakenborrowings($dbh)
{
    $res = array();
    if(islogged($dbh))
    {

        $sql = "SELECT borrowing.id AS bid,borrowing.t_date AS taken_date,borrowing.secret,users.name,users.email,books.title,books.bookid AS bookidg FROM borrowing INNER JOIN users ON borrowing.user_id=users.id INNER JOIN books ON borrowing.book_id=books.id WHERE borrowing.status=2";
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
}
else
{
    $res['mess'] = "fail";
    $res['info'] = "No Permissions";
}

    echo json_encode($res);  
}

function confirmborrowing($dbh) {
    $res = array();
    if(islogged($dbh))
    {

        $id = test_input($_POST['id']);
        $now = time();

        $sql = "UPDATE borrowing SET t_date=:now,status=2 WHERE id=:id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":now",$now);
        $stmt->bindParam(":id",$id);
        if($stmt->execute())
        {
            
            
            
            
            $sql4= "SELECT book_id,user_id FROM borrowing WHERE id=:id";
            $stmt4 = $dbh->prepare($sql4);
            $stmt4->bindParam(":id",$id);
            if($stmt4->execute())
            {
                $row4 = $stmt4->fetch();
                $user_id = $row4['user_id'];
                $book_id = $row4['book_id'];
            
            
            
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
                                            $subject = "تأكيد استلاف الكتاب بعنوان ".$book_title;

                                            $message = "
                                    <html>
                                        <head>
                                            <title>HTML email</title>
                                                </head>
                                                <body>
                                                <h2>مكتبة جامعة العلوم الاسلامية</h2>
                                                <h2>تاكيد استلاف الكتاب</h4>
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
                                                <p>رقم عملية الاستلاف: ".$id."</p>
                                                
                                                <br>
                                                <br>
                                                <p style='color:red'>لقد استلفت الكتاب و يرجى ترجيع الكتاب خلال 14 يوم بدءا من ارسال هذا البريد</p>
                                                
                                                <p style='color:red'>حين ارجاع الكتاب الرجاء اظهار رقم عملية الاستلاف لموظف المكتبة</p>

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
                                $res['info'] = " لقد تم استلاف الكتاب بنجاح و مع المستلف 14 يوم لاعادة الكتاب منذ هذه اللحظة ";


                                    
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
        $res['info'] = implode(" - ",$stmt->errorInfo());     
        }
}
else
{
    $res['mess'] = "fail";
    $res['info'] = "No Permissions";
}

    echo json_encode($res);     
}

function getallregistredborrowings($dbh) {
    $res = array();
    if(islogged($dbh))
    {

        $sql = "SELECT borrowing.id AS bid,borrowing.create_date AS register_date,borrowing.secret,users.name,users.email,books.title,books.bookid AS bookidg FROM borrowing INNER JOIN users ON borrowing.user_id=users.id INNER JOIN books ON borrowing.book_id=books.id WHERE borrowing.status=1";
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
}
else
{
    $res['mess'] = "fail";
    $res['info'] = "No Permissions";
}

    echo json_encode($res);  
}


function deleteuser($dbh) {
    $res = array();
    if(islogged($dbh))
    {
    $id = test_input($_POST['id']);

    $sql2 = "SELECT COUNT(*) AS count FROM borrowing WHERE user_id=:id AND status IN(1,2)";
    $stmt2 = $dbh->prepare($sql2);
    $stmt2->bindParam(":id",$id);
    if($stmt2->execute())
    {
        $row2 = $stmt2->fetch();
        $b_count = $row2['count']; 
    
    if($b_count==0)
    {
    $sql = "DELETE FROM users WHERE id=:id AND type=2";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":id",$id);
    if($stmt->execute())
    {
        $res['mess'] = "success";
        $res['id'] = $id;
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
       $res['info'] = "لا تستطيع حذف هذا المستخدم لانه يوجد له عمليات استلاف نشطة حاليا";   
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res); 
}


function activateuser($dbh)
{
    $res = array();
    if(islogged($dbh))
    {

        $userid = $_POST['id'];
    
    $sql = "UPDATE users SET status=1 WHERE id=:userid AND type=2";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":userid",$userid);
    if($stmt->execute())
    {
        $res['mess'] = "success";
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);  
}

function banuser($dbh)
{
    $res = array();
    if(islogged($dbh))
    {

        $userid = $_POST['id'];
    
    $sql = "UPDATE users SET status=2 WHERE id=:userid AND type=2";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":userid",$userid);
    if($stmt->execute())
    {
        $res['mess'] = "success";
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);  
}

function disableborrow($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
    
    $sql = "UPDATE settings SET VALUE='0' WHERE name='user_borrow'";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $res['mess'] = "success";
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);    
}

function enableborrow($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
    
    $sql = "UPDATE settings SET VALUE='1' WHERE name='user_borrow'";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $res['mess'] = "success";
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);    
}

function disablereg($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
    
    $sql = "UPDATE settings SET VALUE='0' WHERE name='user_register'";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $res['mess'] = "success";
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);    
}

function enablereg($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
    
    $sql = "UPDATE settings SET VALUE='1' WHERE name='user_register'";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $res['mess'] = "success";
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);    
}



function getsettings($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
    
    $sql = "SELECT value FROM settings WHERE name='user_register'";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $row = $stmt->fetch();
        $res['register'] = $row['value'];
    }
    else
    {
        $res['mess'] = "fail";
        $res['info'] = implode(" - ",$stmt->errorInfo());
    }




    $sql = "SELECT value FROM settings WHERE name='user_borrow'";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $row = $stmt->fetch();
        $res['mess'] = "success";
        $res['borrow'] = $row['value'];
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);       
}

function deletebook($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
    $id = test_input($_POST['id']);

    
    $sql = "DELETE FROM books WHERE id=:id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":id",$id);
    if($stmt->execute())
    {
        $res['mess'] = "success";
        $res['id'] = $id;
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res); 
}




function editbook($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
    $pid = test_input($_POST['pid']);
    $title = test_input($_POST['title']);
    $author = test_input($_POST['author']);
    $bookid = test_input($_POST['serial']);
    $pdate = test_input($_POST['pdate']);
    $pagesno = test_input($_POST['pages']);
    $copyno = test_input($_POST['copynum']);
    $now = time();


    $sql2 = "SELECT copyno,copynov FROM books WHERE id=:pid";
    $stmt2 = $dbh->prepare($sql2);
    $stmt2->bindParam(":pid",$pid);
    if($stmt2->execute())
    {

        $row2 = $stmt2->fetch();
        $oldcopyno = $row2['copyno'];
        $oldcopyno = (int)$oldcopyno;

        $oldcopynov = $row2['copynov'];
        $oldcopynov = (int)$oldcopynov;

        if($copyno != $oldcopyno)
        {
            $diff = $copyno - $oldcopyno;
            $newcopynov = $oldcopynov + $diff;
        }
        else
        {
            $newcopynov = $oldcopynov;
        }

        if($newcopynov<0)
        {
            $newcopynov = 0;
        }


        $sql = "UPDATE books SET bookid=:bookid,title=:title,author=:author,publish_date=:pdate,pagesno=:pagesno,copyno=:copyno,copynov=:newcopynov,update_date=:now WHERE id=:pid";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":title",$title);
        $stmt->bindParam(":author",$author);
        $stmt->bindParam(":bookid",$bookid);
        $stmt->bindParam(":pdate",$pdate);
        $stmt->bindParam(":pagesno",$pagesno);
        $stmt->bindParam(":copyno",$copyno);
        $stmt->bindParam(":newcopynov",$newcopynov);
        $stmt->bindParam(":now",$now);
        $stmt->bindParam(":pid",$pid);
        if($stmt->execute())
        {
            $res['mess'] = "success";
            $res['newcopynov'] = $newcopynov;
            $res['id'] = $pid;
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
        $res['info'] = implode(" - ",$stmt2->errorInfo());
    }

    

}
else
{
    $res['mess'] = "fail";
    $res['info'] = "No Permissions";
}

    echo json_encode($res); 
}


function getbook($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
        $id = test_input($_POST['id']);

    $sql = "SELECT * FROM books WHERE id=".$id;
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $res['mess'] = "success";
        $res['data'] = $stmt->fetch();
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);   
}

function islogged($dbh)
{
    if(isset($_SESSION["userlogin"]))
    {

        $sarray = $_SESSION["userlogin"];
        $userid = $sarray['id'];

        $sql = "SELECT COUNT(*) AS count FROM users WHERE id=:userid";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam("userid",$userid);
        if($stmt->execute())
        {

            $row = $stmt->fetch();
            $count = $row['count'];
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

function addbook($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
    $title = test_input($_POST['title']);
    $author = test_input($_POST['author']);
    $bookid = test_input($_POST['serial']);
    $pdate = test_input($_POST['pdate']);
    $pagesno = test_input($_POST['pages']);
    $copyno = test_input($_POST['copynum']);
    $now = time();
    
    $sql = "INSERT INTO books(bookid,title,author,publish_date,pagesno,copyno,copynov,create_date,update_date,status) VALUES(:bookid,:title,:author,:pdate,:pagesno,:copyno,:copyno,:now,:now,1)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":title",$title);
    $stmt->bindParam(":author",$author);
    $stmt->bindParam(":bookid",$bookid);
    $stmt->bindParam(":pdate",$pdate);
    $stmt->bindParam(":pagesno",$pagesno);
    $stmt->bindParam(":copyno",$copyno);
    $stmt->bindParam(":now",$now);
    if($stmt->execute())
    {
        $lastid = $dbh->lastInsertId();
        $res['mess'] = "success";
        $res['id'] = $lastid;
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res); 
}

function logout($dbh)
{
    $res = array();
    if(isset($_SESSION["userlogin"]))
    {
        $sarray = $_SESSION["userlogin"];
        $userid = $sarray['id'];

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
                            unset($_SESSION["userlogin"]);
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

function checklogin($dbh)
{
$res = array();
    if(isset($_SESSION["userlogin"]))
    {

        $sarray = $_SESSION["userlogin"];
        $userid = $sarray['id'];
        $user_full_name = $sarray['name'];
        $sql = "SELECT COUNT(*) AS count FROM users WHERE id=:userid";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam("userid",$userid);
        if($stmt->execute())
        {

            $row = $stmt->fetch();
            $count = $row['count'];
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
                        $res['user_full_name'] = $user_full_name;   
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

function login($dbh)
{
    $res = array();
    $username = test_input($_POST['username']);
    $password = test_input($_POST['password']);

    $websalt = "bbookssystem2022";
    
    $sql = "SELECT COUNT(*) AS count,id,password,salt,email,name FROM users WHERE email=:username and type=1";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam("username",$username);
    if($stmt->execute())
    {
        $row = $stmt->fetch();
        $salt = $row['salt'];
        $dbpass = $row['password'];
        $userid = $row['id'];
        $user_full_name = $row['name'];
        $count = $row['count'];

        if($count==1)
        {

        $hash = sha1($salt.$password.$websalt);

        if($dbpass==$hash)
        {

            $sarray = array();
            $sarray['name'] = $user_full_name;
            $sarray['id'] = $userid;

            $_SESSION["userlogin"] = $sarray;

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
                        }
                    }
                    else
                    {
                        $res['mess'] = "fail";
                        $res['error'] = implode(" - ",$stmt3->errorInfo());  
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
                $res['error'] = implode(" - ",$stmt2->errorInfo());  
            }

        }
        else
        {
            $res['mess'] = "fail";
            $res['error'] = " خطأ في معلومات الدخول ";    
        }
    }
    else
    {
        $res['mess'] = "fail";
        $res['error'] = " خطأ في معلومات الدخول "; 
    }
    }
    else
    {
        $res['mess'] = "fail";
        $res['error'] = implode(" - ",$stmt->errorInfo());
    }



    echo json_encode($res);
}


function getbooks($dbh)
{
    $res = array();
    if(islogged($dbh))
    {
    $sql = "SELECT * FROM books ORDER BY create_date";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $res['mess'] = "success";
        $res['data'] = $stmt->fetchAll();
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
    $res['info'] = "No Permissions";
}

    echo json_encode($res);
}


function getusers($dbh) {
    $res = array();
    if(islogged($dbh))
    {
    $sql = "SELECT * FROM users where type=2 ORDER BY create_date";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute())
    {
        $res['mess'] = "success";
        $res['data'] = $stmt->fetchAll();
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
    $res['info'] = "No Permissions";
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