<?php
include_once("php/db/connect.php");

$now = time();

$sql = "SELECT * FROM borrowing WHERE status=1";
$stmt = $dbh->prepare($sql);
if($stmt->execute()) {
    
    while($row = $stmt->fetch()) {
        
        $reg_date = $row['create_date'];
        $b_id = $row['id'];
        
        if(($now - (int)$reg_date) > (60*60*24))
        {
                
                $sql2 = "SELECT book_id FROM borrowing WHERE id=:b_id";
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":b_id",$b_id);
                if($stmt2->execute()) {
                    $row2 = $stmt2->fetch();
                    $book_id = $row2['book_id'];
                    
                    $sql3 = "DELETE FROM borrowing WHERE id=:b_id";
                    $stmt3 = $dbh->prepare($sql3);
                    $stmt3->bindParam(":b_id",$b_id);
                    if($stmt3->execute()) {
                        
                        
                        $sql4 = "UPDATE books SET copynov = copynov + 1 WHERE id=:book_id";
                        $stmt4 = $dbh->prepare($sql4);
                        $stmt4->bindParam(":book_id",$book_id);
                        if($stmt4->execute())
                        {
                            echo "success<br />";
                        }
                        
                    }    
                    
                    
                    
                }
            
        }
        
    }
    
}



?>