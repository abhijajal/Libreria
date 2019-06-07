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
if (isset($_GET['bookID']) || isset($_GET['bookID10']) ||isset($_GET['bookTitle']) || isset($_GET['bookAuthor']))
{
	if (isset($_GET['bookID']))
		$bookID=$_GET['bookID'];
	else
		$bookID='';
		
	if (isset($_GET['bookID10']))
		$bookID10=$_GET['bookID10'];
	else
		$bookID10='';
	
	if (isset($_GET['bookTitle']))
		$bookTitle=$_GET['bookTitle'];
	else
		$bookTitle='';
	
	if (isset($_GET['bookAuthor']))
		$bookAuthor=$_GET['bookAuthor'];
	else
		$bookAuthor='';
?>

	<form style="margin: 10px" action="index.php" method="get">
            <div><div><input type="text" name="bookID" class="form-control rounded-pill" placeholder="Isbn13" value="<?php echo $bookID?>"></div></div>         
            <div><div><input type="text" name="bookID10" class="form-control rounded-pill" placeholder="Isbn10" value="<?php echo $bookID10?>"></div></div>
            <div><div><input type="text" name="bookTitle" class="form-control rounded-pill" placeholder="Book Title" value="<?php echo $bookTitle?>"></div></div>
            <div><div><input type="text" name="bookAuthor" class="form-control rounded-pill" placeholder="Author Name" value="<?php echo $bookAuthor?>"></div></div>
            <button type="submit" class="btn btn-primary rounded-pill">SEARCH</button>
    </form>	
			
<?php	
	include('config.php');
	$query= "SELECT BOOK.Isbn13, Isbn10, Title, GROUP_CONCAT(Name) AS Name, Copies FROM BOOK, BOOK_AUTHOR, AUTHOR WHERE BOOK.Isbn13 = BOOK_AUTHOR.Isbn13 AND BOOK_AUTHOR.Author_id = AUTHOR.Author_id AND BOOK.Isbn13 LIKE '%" . $bookID . "%' AND  BOOK.Isbn10 LIKE '%" . $bookID10 . "%'AND BOOK.Title LIKE '%" . $bookTitle . "%' AND AUTHOR.Name LIKE '%" . $bookAuthor . "%' GROUP BY BOOK.Isbn13, Isbn10, Title, Copies";
	$res= mysqli_query($conn,$query);
	
	if(!$res) {
    	//die("Connection failed: " . mysql_error()); 
	}
	$row = mysqli_num_rows($res);
	
	if($row>0){
?>
  	<table class="table table-striped table-dark">
        <caption>Found <?php echo $row;?> Records:</caption>  
    <thead>
            <tr>
                <th>Isbn13</th>
                <th>Isbn10</th>
                <th>Title</th>
                <th>Author</th>
                <th>Total Copies</th>
                <th>Available Copies</th>
                <th></th>
            </tr>
        </thead>
        <tbody>       

<?php	
	while($resArray=mysqli_fetch_array($res))
	{
		$query1 = "SELECT * FROM BOOK_LOANS WHERE Isbn13 LIKE '" . $resArray['Isbn13'] . "' AND Date_in='0000-00-00'";
		$res1= mysqli_query($conn,$query1);
		$checkedOut = mysqli_num_rows($res1); //number of checked out copies
	
?>
     	<tr>
            <td><?php echo $resArray['Isbn13']; ?></td>
            <td><?php echo $resArray['Isbn10']; ?></td>
            <td><?php echo $resArray['Title']; ?></td>
            <td><?php echo $resArray['Name']; ?></td>
            <td><?php echo $resArray['Copies']; ?></td>
            <td><?php echo $resArray['Copies'] - $checkedOut; ?></td>
            <td>
            	<?php if(($resArray['Copies'] - $checkedOut) >0){//copie available
					?>
            		<button class="btn btn-success rounded-pill" type="button" id="<?php echo $resArray['Isbn13'];?>" findbookID="<?php echo $resArray['Isbn13']; ?>" onClick="checkout(this.id)">CHECK OUT</button>
            <?php
					        }
					  else{//Book is not available in this particular library
		       ?>
					  <button class="disable btn btn-danger rounded-pill" type="button" disabled id="<?php echo $resArray['Isbn13'];?>"  findbookID="<?php echo $resArray['Isbn13']; ?>"  onClick="checkout(this.id)">CHECK OUT</button>
			<?php
                      }
			?>	  
            </td>
		</tr>
<?php   
    }//while
?>

		</tbody>
	</table>
    
<?php
 }
	else{  //if no records exist
?>		
        <p class="g4 alert alert-danger" role="alert">No records found.</p> 
<?php
	  }
}


else
{
?>
		
	<form style="margin: 10px" action="index.php" method="get">        
           <div> <div><input type="text" name="bookID" class="form-control rounded-pill" placeholder="Isbn13"></div></div>
            <div>  <div><input type="text" name="bookID10" class="form-control rounded-pill" placeholder="Isbn10"></div></div>
            <div>     <div><input type="text" name="bookTitle" class="form-control rounded-pill" placeholder="Book Title"></div></div>
            <div> <div><input type="text" name="bookAuthor" class="form-control rounded-pill" placeholder="Author Name"></div></div>
            <button type="submit" class="btn btn-primary rounded-pill">SEARCH</button>
	</form>

<?php
}

?>

<script>
function checkout(buttonID){
		var button=document.getElementById(buttonID);		
		var bookID=button.getAttribute("findbookID");		
		
		window.location.href = 'checkOut.php?bookID='+bookID; 
}
</script>



</body>

</html>