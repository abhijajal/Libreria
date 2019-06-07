<!DOCTYPE html>


<html lang="en">
<head>
   <meta charset="utf-8">
    <meta name="author" content="apj180001" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <title>Search Borrower</title>

</head>

<body>
<nav class="navbar navbar-dark bg-dark">
  <!-- Navbar content -->
        <a href='index.php' target='body'>CHECK OUT</a>
        <a href='checkIn.php' target='body'>CHECK IN</a>
        <a href='borrowers.php' target='body'>BORROWERS</a>
        <a href='fines.php' target='body'>FINES</a>
</nav>

<p class="g3"><a href="addBorrowers.php"><button style="margin: 10px" class="btn btn-info rounded-pill" type="button">ADD NEW</button></a><br><br>


<?php
	if (isset($_GET['cardID']))
		$cardID=$_GET['cardID'];
	else
		$cardID='';
		
	if (isset($_GET['bname']))
		$bname=$_GET['bname'];
	else
		$bname='';	
	?>
    
  <form style="margin: 10px" action="borrowers.php" method="get">
         <div><div><input type="text" class="form-control rounded-pill" id="cardID" name="cardID" placeholder="Card ID" value="<?php echo $cardID?>"></div></div>
          <div><div><input type="text" class="form-control rounded-pill" id="bname" name="bname" placeholder="Borrower's Name" value="<?php echo $bname?>"></div></div>
          <button type="submit" class="btn btn-primary rounded-pill">SEARCH</button>
  </form>	
    
        
<?php
        include('config.php');
        if (isset($_GET['cardID']) ||isset($_GET['bname'])){
        
            $query= "SELECT * from BORROWER WHERE Card_id LIKE \"%".  $cardID . "%\" AND Bname LIKE \"%" . $bname . "%\"" ;
            $res= mysqli_query($conn,$query);
            $row = mysqli_num_rows($res);
            
            if($row>0){           
 ?>
         
                <table class="table table-striped table-dark">
                    <caption>Found <?php echo $row;?> Results</caption>  
                    <thead>
                        <tr>
                            
                            <th>Borrower Name</th>                                                        
                            <th>Card ID</th>
                            <th>Ssn</th>
                            <th>Adress</th>
                            <th>Phone</th>
                        </tr>
                     </thead>
                     <tbody>       
            
<?php	
                while($resArray=mysqli_fetch_array($res)){
?>
                    <tr>
                        <td><?php echo $resArray['Bname']; ?></td>
                        <td><?php echo $resArray['Card_id']; ?></td>
                        <td><?php echo $resArray['Ssn']; ?></td>
                        <td><?php echo $resArray['Address']; ?></td>
                        <td><?php echo $resArray['Phone']; ?></td>
                        <td>
<?php
                  }
?>
                        </td>
                    </tr>
                    </tbody>
                   </table>
<?php
              }		
		
		else{ //row<=0, no records
?>		
                 <p class="g4 alert alert-danger" role="alert">No records found.</p> 
<?php
	     }
		
}
?>	
		
	


</body>

</html>