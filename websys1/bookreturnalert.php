<?php
include_once("php/db/connect.php");

$now = time();
$sql = "SELECT * FROM borrowing WHERE status=2";
$stmt = $dbh->prepare($sql);
if($stmt->execute()) {
    
    while($row = $stmt->fetch()) {
        
        $user_id = $row['user_id'];
        $book_id = $row['book_id'];
        $borrowing_id = $row['id'];
        
        
        $sql8 = "SELECT warning_email_status FROM borrowing WHERE id=:borrowing_id";
        $stmt8 = $dbh->prepare($sql8);
        $stmt8->bindParam(":borrowing_id",$borrowing_id);
        if($stmt8->execute())
        {
        $row8 = $stmt8->fetch();
        $we_status = (int)$row8['warning_email_status'];
        
        if($westatus==0)
        {
        $taken_date = (int)$row['t_date'];
        $returnmax = $taken_date + (14*24*60*60);
        $oneday = (24*60*60);
        
        if(($returnmax-$now)<$oneday)
        {
        
        $sql2 = "SELECT name,email FROM users WHERE id=:user_id";
        $stmt2 = $dbh->prepare($sql2);
        $stmt2->bindParam(":user_id",$user_id);
        if($stmt2->execute()) {
            
            $row2 = $stmt2->fetch();
            $user_full_name = $row2['name'];
            $user_email = $row2['email'];
            
            
            $sql3 = "SELECT title,bookid FROM books WHERE id=:book_id";
            $stmt3 = $dbh->prepare($sql3);
            $stmt3->bindParam(":book_id",$book_id);
            if($stmt3->execute()) {
                
                $row3 = $stmt3->fetch();
                $book_full_id = $row3['bookid'];
                $book_title = $row3['title'];
                
                
                                                            $to = $user_email;
                                            $subject = "تنبيه بقي معك اقل من 24 ساعة لارجاع الكتاب بعنوان  ".$book_title;

                                            $message = "
                                    <html>
                                        <head>
                                            <title>HTML email</title>
                                                </head>
                                                <body>
                                                <h2>مكتبة جامعة العلوم الاسلامية</h2>
                                                <h2>تنبيه لارجاع كتاب</h4>
                                                <br />
                                                <br />
                                                <h4>معلومات الكتاب</h2>
                                                <p> عنوان الكتاب: ".$book_title."</p>
                                                <p> رقم الكتاب: ".$book_full_id ."</p>
                                                <br />
                                                <br />
                                                <h4>معلومات المستلف</h4>
                                                <p>الاسم: ".$user_full_name."</p>
                                                <p>الايميل: ".$user_email."</p>
                                                <br />
                                                <br />
                                                <h4>معلومات عملية الاستلاف</h4>
                                                <p>رقم عملية الاستلاف: ".$borrowing_id."</p>

                                                
                                                <br>
                                                <br>
                                                <p style='color:red'>تنبيه: عليك ارجاع الكتاب المبين اعلاه خلال اقل من 24 ساعة و الا سيفرض عليك غرامة</p>
                                                
                                                </body>
                                                    </html>
                                                ";

                                    // Always set content-type when sending HTML email
                                $headers = "MIME-Version: 1.0" . "\r\n";
                                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                                // More headers
                                    $headers .= 'From: <noreply@standardsi.com>' . "\r\n";

                                        
                                        $sql7 = "UPDATE borrowing SET warning_email_status=1 WHERE id=:borrowing_id";
                                        $stmt7 = $dbh->prepare($sql7);
                                        $stmt7->bindParam(":borrowing_id",$borrowing_id);
                                        if($stmt7->execute())
                                        {
                                            mail($to,$subject,$message,$headers);
                                        }
                                        else
                                        {
                                            
                                        }
                                        
                                    
                
                 
                
                
                
                }
                else
                {
                    
                }
            
        }
            
        }
        }
        else
        {
            
        }
        }
        else
        {
            
        }
        
        
        }
        
        }

?>