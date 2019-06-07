<!DOCTYPE html>


<html lang="en">
<head>
   <meta charset="utf-8">
    <meta name="author" content="apj180001" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <title>Check out</title>
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

if (isset($_GET['bookID']) && !isset($_GET['cardID'])){
	   if (isset($_GET['bookID']))
		       $bookID=$_GET['bookID'];
	   else
		       $bookID='';
?>

	<form style="margin: 10px" action="checkOut.php" method="get">
          <div><div><input class="form-control rounded-pill"  type="text" id="cardID" name="cardID" placeholder=" Card ID"></div></div>
          <div><div><input  class="form-control rounded-pill" type="text" id="bookID" name="bookID" placeholder=" Isbn13" value="<?php echo $bookID?>"></div></div>
           <button type="submit" class="btn btn-primary rounded-pill">CHECK OUT</button>
    </form>	

<?php
}

elseif(isset($_GET['bookID']) && isset($_GET['cardID'])){
	
	     if (isset($_GET['bookID']))
		          $bookID=$_GET['bookID'];
	     else
		          $bookID='';
	
	     if (isset($_GET['cardID']))
		          $cardID=$_GET['cardID'];
	     else
		          $cardID='';
?>

	<form style="margin: 10px" action="checkOut.php" method="get">
	      <div><input type="text" class="form-control rounded-pill" id="cardID" name="cardID" placeholder=" Card ID" value="<?php echo $cardID ?>">
	      <?php   
						include('config.php');
						$query= "SELECT 1 from BORROWER WHERE Card_id='" . $cardID . "'";
						$res2= mysqli_query($conn,$query);
						if (mysqli_num_rows($res2) == 0) { //card ID not valid
			?>
						<span class="g5 alert alert-danger" role="alert">The Card ID was not correct.</span>
			<?php	
						}						
         ?>
	      </div></div>
	      
        <div><div><input type="text" class="form-control rounded-pill" id="bookID" name="bookID" placeholder=" Isbn13" value="<?php echo $bookID?>">
         <?php   						
						$query= "SELECT 1 from BOOK WHERE Isbn13='" . $bookID . "'";
						$res1= mysqli_query($conn,$query);
						if (mysqli_num_rows($res1) == 0) {  //book ID not valid
			?>
						<span class="g5 alert alert-danger" role="alert">The Isbn13 was not correct.</span>
			<?php	
						}
          ?>
        </div></div>

<?php
			if(mysqli_num_rows($res1) == 0 || mysqli_num_rows($res2) == 0){
?>
           <button type="submit" class="btn btn-primary rounded-pill">CHECK OUT</button>
<?php
			}
			else{//this means bookID, branchID and CardNo. entered is correct
				
				$query= "SELECT * FROM BOOK_LOANS, BOOK, BOOK_AUTHOR WHERE BOOK_LOANS.Isbn13 = BOOK.Isbn13 AND BOOK.Isbn13=BOOK_AUTHOR.Isbn13 AND Card_id = '" . $cardID . "' AND Date_in = '0000-00-00'";
				$res3= mysqli_query($conn,$query);
							$checkout='0';
					      //check if this user has unpaid fines
							$query="SELECT * FROM BOOK_LOANS, FINES WHERE BOOK_LOANS.Loan_id=FINES.Loan_id AND FINES.PAID=0";
							$result=mysqli_query($conn, $query);
							$resArray = $result->fetch_all();
							$cardIDArray[]="";
							foreach($resArray as $res1){
								$cardIDArray[]=$res1[3];	
							}
					if(array_search($cardID, $cardIDArray)!=FALSE){//user has unpaid fines
						?>
                        <p class="g3 alert alert-danger" role="alert">The borrower has unpaid fines. Please pay fines before check out any books.</p>			
<?php
					}
					elseif (mysqli_num_rows($res3) >= 3 ) { //maximum number of books
							?>
                           <p class="g3 alert alert-danger" role="alert">The borrower has checked out maximum number of allowed books. </p>
							  <table class="table table-striped table-dark">
                                 
                                <thead>
                                    <tr>
                                        <th>Loan ID</th>
                                        <th>Book ID</th>
                                        <th>Title</th>
                                        <th>Checkout Date</th>
                                        <th>Due Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>       
                        
                        <?php	
                            while($resArray=mysqli_fetch_array($res3))
                            {
                        ?>
                                <tr>
                                    <td><?php echo $resArray['Loan_id']; ?></td>
                                    <td><?php echo $resArray['Isbn13']; ?></td>
                                    <td><?php echo $resArray['Title']; ?></td>
                                    <td><?php echo $resArray['Date_out']; ?></td>
                                    <td><?php echo $resArray['Due_date']; ?></td>
                                    <td><button type="button" class="btn btn-danger rounded-pill" id=" <?php echo $resArray['Isbn13'].$cardID;?> " findbookID="<?php echo $resArray['Isbn13']; ?>" findcardID="<?php echo $cardID;?>" onClick="checkout(this.id)">CHECK IN</button></td>
                                </tr>
                        <?php   
                            }
                        ?>
                                   </tbody>
                            </table>			

<?php
				     	}

					else{

						$query1 = "SELECT * FROM BOOK_LOANS WHERE Isbn13 LIKE '" . $bookID . "' AND Date_in='0000-00-00'";
						$res1= mysqli_query($conn,$query1);
						$checkedOut = mysqli_num_rows($res1);
						
						$query1 = "SELECT Copies FROM BOOK WHERE Isbn13 LIKE '" . $bookID . "'";
						$res1= mysqli_query($conn,$query1);
						$resArray=mysqli_fetch_array($res1);
						$totalBooks=$resArray['Copies'];
						
						$remaining = $totalBooks - $checkedOut; //copies available
						
						if($remaining>0){ //copies available
							$query="SELECT MAX(Loan_id) FROM BOOK_LOANS";
							$res= mysqli_query($conn,$query);
							$resArray=mysqli_fetch_array($res);
							$nextLoanID=$resArray['MAX(Loan_id)']+1;
							
							$query = "INSERT INTO BOOK_LOANS (Loan_id, Isbn13, Card_id, Date_out, Due_date, Date_in) VALUES ('" . $nextLoanID . "', '" . $bookID . "', '" . $cardID . "', '" . date("Y/m/d") . "', " . "DATE_ADD(Date_out, INTERVAL 14 DAY)" . ", '0000-00-00')"  ;
							$res= mysqli_query($conn,$query);
							if($res){  //check out succeed
							
							$query = "SELECT * FROM BOOK_LOANS, BORROWER, BOOK WHERE Loan_id = \"" . $nextLoanID . "\" AND BOOK.Isbn13=BOOK_LOANS.Isbn13 AND BOOK_LOANS.Card_id=BORROWER.Card_id";
							$res= mysqli_query($conn,$query);
							$resArray=mysqli_fetch_array($res);
?>
							<p class="g3 alert alert-success" role="alert">Book successfully checked out. Due date is <?php echo $resArray['Due_date'] ?></p>											 
						                     
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
																
								
<?php	
					      }
							else{ //check out failed
?>
								<p class="g4">Something went wrong. Please try again or contact webmaster.</p>
							
<?php
							  }
					}
			else{
?>
                            	<p class="g4">No available copy of this book. Please search for another book.</p>
<?php
              }
								
	}
					
}
				
			
?>	
    </form>
    	
<?php	
}

else
{
?>
<form action="checkOut.php" method="get">
   		<div><label for="cardID">Card ID</label><div><input type="text" class="form-control rounded-pill" id="cardID" name="cardID" placeholder=" Card ID"></div></div>
       <div><label for="bookID">Isbn13</label><div><input type="text" class="form-control rounded-pill" id="bookID" name="bookID" placeholder=" Isbn13"></div></div>
       <button type="submit" class="btn btn-primary rounded-pill">CHECK OUT</button>
</form>	
    
<?php 
}
?>
	

<script>
	function checkout(buttonID){
		var button=document.getElementById(buttonID);		
		var bookID = button.getAttribute("findbookID");
		var cardID = button.getAttribute("findcardID");
		
		window.location.href = 'checkIn.php?bookID=' + bookID + '&cardID=' + cardID; 
}
	</script>



</body>

</html>