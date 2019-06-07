<!DOCTYPE html>


<html lang="en">
<head>
   <meta charset="utf-8">
    <meta name="author" content="apj180001" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <title>Search and Check out</title>
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
	if (isset($_GET['bookID']))
		$bookID=$_GET['bookID'];
	else
		$bookID='';
	
	if (isset($_GET['cardID']))
		$cardID=$_GET['cardID'];
	else
		$cardID='';
		
	if (isset($_GET['bookTitle']))
		$bookTitle=$_GET['bookTitle'];
	else
		$bookTitle='';
		
	if (isset($_GET['bname']))
		$bname=$_GET['bname'];
	else
		$bname='';
	
	if (isset($_GET['loanID']))
		$loanID=$_GET['loanID'];
	else
		$loanID='';
	
	if (!isset($_GET['loanID'])){
?>
    
        <form style="margin: 10px" action="checkIn.php" method="get">
            <div><div><input type="text" class="form-control rounded-pill"
 id="cardID" name="cardID" placeholder="Card ID" value="<?php echo $cardID?>"></div></div>
            <div><div><input type="text" class="form-control rounded-pill"
 id="bname" name="bname" placeholder="Borrower's Name" value="<?php echo $bname?>"></div></div>
            <div><div><input type="text" class="form-control rounded-pill"
 id="bookID" name="bookID" placeholder="Isbn13" value="<?php echo $bookID?>"></div></div>
            <div><div><input type="text" class="form-control rounded-pill"
 id="bookTitle" name="bookTitle" placeholder="Book Title" value="<?php echo $bookTitle?>"></div></div>
             <button type="submit" class="btn btn-primary rounded-pill">SEARCH</button>
        </form>	
            
<?php       
        include('config.php');
        if (isset($_GET['bookID']) ||isset($_GET['cardID'])){
        
            $query= "SELECT * from BOOK, BORROWER, BOOK_LOANS WHERE BOOK.Isbn13 = BOOK_LOANS.Isbn13 AND BORROWER.Card_id = BOOK_LOANS.Card_id AND BOOK_LOANS.Date_in = \"0000-00-00\" AND BOOK_LOANS.Card_id LIKE \"%". $cardID . "%\" AND Bname LIKE \"%" . $bname . "%\" AND BOOK.Isbn13 LIKE \"%" . $bookID . "%\" AND  BOOK.Title LIKE \"%" . $bookTitle . "%\"" ;
            $res= mysqli_query($conn,$query);
            $row = mysqli_num_rows($res);
            
            if($row>0){           
 ?>
                <table class="table table-striped table-dark">
                    <caption>Found <?php echo $row;?> Records</caption>                   
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Card ID</th>
                            <th>Borrower Name</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Checkout Date</th>
                            <th>Due Date</th>
                            <th></th>
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
                        <td><?php echo $resArray['Isbn13']; ?></td>
                        <td><?php echo $resArray['Title']; ?></td>
                        <td><?php echo $resArray['Date_out']; ?></td>
                        <td><?php echo $resArray['Due_date']; ?></td>
                        <td>
                         <button type="button" class="btn btn-danger rounded-pill" id=" <?php echo $resArray['Loan_id']?> " findloanID="<?php echo $resArray['Loan_id']; ?>" onClick="checkin(this.id)">CHECK IN</button>
<?php
                    }
?>
                        </td>
                    </tr>
                    </tbody>
                   </table>

<?php
                 }
		else{ //row<=0, now records
?>		
              <p class="g4 alert alert-danger" role="alert">No records found.</p> 
<?php
	   }
		
   }
}
	
    else{
			include('config.php');
			$query="SELECT * FROM BOOK_LOANS, BORROWER, BOOK WHERE Loan_id = \"" . $loanID . "\" AND BOOK.Isbn13=BOOK_LOANS.Isbn13 AND BOOK_LOANS.Card_id=BORROWER.Card_id" ;
			$res= mysqli_query($conn,$query);
			$resArray=mysqli_fetch_array($res);
?>
						                     
             <table class="table table-striped table-dark">
                <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Card ID</th>
                            <th>Borrower's Name</th>
                            <th>Isbn13</th>
                            <th>Title</th>
                            <th>Checkout Date</th>
                            <th>Due Date</th>
                        </tr>
                </thead> 
                <tbody>       
                        <tr>
                            <td><?php echo $resArray['Loan_id']; ?></td>
                            <td><?php echo $resArray['Card_id']; ?></td>
                            <td><?php echo $resArray['Bname']; ?></td>
                            <td><?php echo $resArray['Isbn13']; ?></td>
                            <td><?php echo $resArray['Title']; ?></td>
                            <td><?php echo $resArray['Date_out']; ?></td>
                            <td><?php echo $resArray['Due_date']; ?></td>
                        </tr>
                </tbody>
                </table> 
                <br>
                               
<?php
		 if($resArray['Date_in']=="0000-00-00"){ //book not yet returned
				  $query="UPDATE BOOK_LOANS SET Date_in= \"" .  date("Y/m/d") . "\" WHERE Loan_id = \"" . $loanID . "\"" ;
				  $res= mysqli_query($conn,$query);
				
				 if($res){ //update succeed
?>
					     <p class="g3 alert alert-success" role="alert">Book successfully checked in. </p>

<?php
				    }
		  }

}
		

?>


<script>
function checkin(buttonID){
	var button = document.getElementById(buttonID);
	var loanID = button.getAttribute("findloanID");
	
	window.location.href = "checkIn.php?loanID=" + loanID;	
}

</script>


</body>

</html>