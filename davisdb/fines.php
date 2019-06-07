<!DOCTYPE html>


<html lang="en">
<head>
   <meta charset="utf-8">
    <meta name="author" content="apj180001" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <title>Fines</title>
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

if (isset($_GET['cardID']))
	$cardID=$_GET['cardID'];
else
	$cardID='';
?>

<p class="g3"><a href="updateFines.php"><button style="margin: 10px" class="btn btn-info rounded-pill" type="button">UPDATE AND VIEW RECORDS</button></button></a></p>

	<form style="margin: 10px" action="fines.php" method="get">   	
        <div><div><input class="form-control rounded-pill" type="text" id="cardID" name="cardID" placeholder=" Card ID" value="<?php echo $cardID?>"></div></div>
        <button type="submit" class="btn btn-primary rounded-pill">SEARCH by CARD ID</button>
    </form>	

<?php

if (isset($_GET['cardID']) && !isset($_GET['pay'])){
	$cardID=$_GET['cardID'];
	
	$query="SELECT SUM(FINES.Fine_amt), Card_id FROM FINES, BOOK_LOANS WHERE FINES.Loan_id=BOOK_LOANS.Loan_id AND FINES.Paid=\"0\" AND BOOK_LOANS.Card_id =\"". $cardID ."\"";
	$res=mysqli_query($conn,$query);
	$resArray=mysqli_fetch_array($res);
	if($resArray['SUM(FINES.Fine_amt)']!=NULL){ //fine record exists
		
?>
	<div class="g2 alert alert-warning" role="alert">
	<p>Card ID: <?php echo $resArray['Card_id'];?><br>
	       Amount to pay: $<?php echo $resArray['SUM(FINES.Fine_amt)'];?></p>
	<p><a href="fines.php?cardID=<?php echo $cardID;?>&pay=1"><button class="btn btn-primary rounded-pill" type="button">PAY NOW</button></button></a></p>	
	</div>
	 
<?php
   $query="SELECT * FROM FINES, BOOK_LOANS, BORROWER WHERE FINES.Loan_id=BOOK_LOANS.Loan_id AND BOOK_LOANS.Card_id=BORROWER.Card_id AND BOOK_LOANS.Card_id =\"". $cardID ."\" AND FINES.Paid=\"0\"";
	$res=mysqli_query($conn, $query);
?>
	
	<table class="table table-striped table-dark">
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
	while($resArray=mysqli_fetch_array($res)) {
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
	else{
?>
    <br>
	<p class="g3 alert alert-danger" role="alert">No fines found. </p>
<?php
	}
	
}

if (isset($_GET['cardID']) && isset($_GET['pay'])){//pay fine
	$cardNo=$_GET['cardID'];

	$query="SELECT COUNT(*) FROM BOOK_LOANS WHERE Card_id=\"" . $cardID . "\" and Date_in='0000-00-00'";
	$res=mysqli_query($conn, $query);
	$resArray=mysqli_fetch_array($res);
	
	if($resArray['COUNT(*)']>0){//book not returned
?>
	<p class="g3 alert alert-danger" role="alert">Borrower should return all the books to pay the fine.</p>
<?php			
	}
	
	else{
		$query="UPDATE FINES SET Paid=1 WHERE Loan_id IN (SELECT Loan_id FROM BOOK_LOANS WHERE Card_id=\"" . $cardID. "\")";
		$res=mysqli_query($conn, $query);
		if($res){
	?>
		<p class="g4 alert alert-success" role="alert">Fine successfully Paid.</p>
	<?php	
		}
	 }
	 
	 
}

?>


</body>

</html>