<!DOCTYPE html>


<html lang="en">
<head>
   <meta charset="utf-8">
    <meta name="author" content="apj180001" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
   </head>

<body>
<nav class="navbar navbar-dark bg-dark">
  <!-- Navbar content -->
        <a href='index.php' target='body'>CHECK OUT</a>
        <a href='checkIn.php' target='body'>CHECK IN</a>
        <a href='borrowers.php' target='body'>BORROWERS</a>
        <a href='fines.php' target='body'>FINES</a>
</nav>

<?php
	include('config.php');
		
	$query="SELECT * FROM FINES";
	$res=mysqli_query($conn, $query);
	$finesArray = $res->fetch_all();
	
	$fineList[]="";
	$paidList[]="";
	foreach($finesArray as $fine){
		  $fineList[]=$fine[0];
		  $paidList[]=$fine[2];	
	}
	
	$query="SELECT * FROM BOOK_LOANS WHERE (Due_Date<Date_in AND Date_in!='0000-00-00') OR (Date_in='0000-00-00' AND Due_Date<\"" . date("Y/m/d") . "\")";
	$res=mysqli_query($conn, $query);
	
	while($resArray=mysqli_fetch_array($res)){
			
			$dueDate= new DateTime($resArray['Due_date']);
			if($resArray['Date_in']=='0000-00-00'){  //book not yet returned	
				$currDate= new DateTime(date("Y/m/d"));
				$diff=date_diff($dueDate,$currDate);
			  }
			
			else{
				$currDate= new DateTime($resArray['Date_in']);
				$diff=date_diff($dueDate,$currDate);
			 }
			
			$e = $diff->format("%R%a days");
			$diff=explode("+", $e);
			$dayDiff=explode(" ", $diff[1]);
			$dayDiff=$dayDiff[0];
						
		if(array_search($resArray['Loan_id'], $fineList) == FALSE){
			$query="INSERT INTO FINES(Loan_id, Fine_amt, Paid) VALUES(\"" . $resArray['Loan_id'] . "\", \"" . $dayDiff * 0.25 . "\",\"0\")";
			$res1=mysqli_query($conn, $query);
			if($res==FALSE)
			{
				die(mysql_error());
			}			
		}
		else{
			$index=array_search($resArray['Loan_id'], $fineList);
			if($paidList[$index]=='0'){  
					$query="UPDATE FINES SET Fine_amt = \"" . $dayDiff * 0.25 . "\" WHERE Loan_id=\"" . $resArray['Loan_id'] . "\"";
					$res1=mysqli_query($conn,$query);
			}
		}
	}//while


if(!isset($_GET['page'])){ //page goes to all the fines history
?>
	

<?php
	$query="SELECT BOOK_LOANS.Card_id, Bname, SUM(FINES.Fine_amt) FROM FINES, BOOK_LOANS, BORROWER WHERE FINES.Loan_id=BOOK_LOANS.Loan_id AND BOOK_LOANS.Card_id=BORROWER.Card_id AND FINES.PAID=\"0\" GROUP BY Card_id";
	$res=mysqli_query($conn, $query);
	$row = mysqli_num_rows($res);
?>

	<p class="g3 alert alert-warning" role="alert"><a href="updateFines.php?page=1"><button class="btn btn-primary rounded-pill" type="button">FINES HISTORY</button></a><br><br>
   <a href="fines.php"><button class="btn btn-primary rounded-pill" type="button">BACK TO SEARCH</button></a></p>
     <table class="table table-striped table-dark">
      <caption>Found <?php echo $row;?> Records:</caption>     
        <thead>
            <tr>
                <th>Card ID</th>
                <th>Borrower</th>
                <th>Fine to pay</th>
            </tr>
        </thead>
     	<tbody>
     	
<?php	
		while($resArray=mysqli_fetch_array($res)){
?>		
    			<tr>
                <td><?php echo $resArray['Card_id']; ?></td>
                <td><?php echo $resArray['Bname']; ?></td>
                <td><?php echo $resArray['SUM(FINES.Fine_amt)']; ?></td>               
            </tr>
         	
<?php	
	}
?>
		</tbody>
    </table>	
    
  
<?php
}

else{
?>



<?php
   $query="SELECT * FROM FINES, BOOK_LOANS, BORROWER WHERE FINES.Loan_id=BOOK_LOANS.Loan_id AND BOOK_LOANS.Card_id=BORROWER.Card_id";
	$res=mysqli_query($conn, $query);
	$row = mysqli_num_rows($res);
?>

<p class="g3 alert alert-warning" role="alert><a href="updateFines.php"><button class="btn btn-primary rounded-pill" type="button">FINES UNPAID</button></a><br><br>
<a href="fines.php"><button type="button" class="btn btn-primary rounded-pill">BACK TO SEARCH</button></a></p>	
	<table class="table table-striped table-dark">
        <caption>Found <?php echo $row;?> Records:</caption>  
        <thead>
            <tr>
                <th>Loan ID</th>
                <th>Card ID</th>
                <th>Borrower</th>
                <th>Fine</th>
                <th>Paid</th>
            </tr>
        </thead>
     	<tbody>      
	
<?php		
	while($resArray=mysqli_fetch_array($res)){
?>		           
            <tr>
			       <td><?php echo $resArray['Loan_id']; ?></td>
                <td><?php echo $resArray['Card_id']; ?></td>
                <td><?php echo $resArray['Bname']; ?></td>
                <td><?php echo $resArray['Fine_amt']; ?></td>
                
<?php
    if($resArray['Paid']=='0')
	      echo "<td>No</td>";
   else
	      echo "<td>Yes</td>";
?>
            </tr>
     
    	
<?php	
	} //while
?>
		</tbody>
    </table>	

<?php
}
?>


</body>

</html>