<?php include "templates/header.php" ?>

     <div class="container">
        <div class="row">
            <div class="col-sm-6 col-lg-6 col-md-6">
                <h3> Congradulations! You order has been placed. </h3>
                <table class="table table-striped table-responsive">
                    <thead>
                        <tr>
                            <td>Product</td>
                            <td>Quantity</td>
                            <td>Price</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="name" value="lc">Lounge Chair</td>
                            <td id="quantity">X 2</td>
                            <td id="price">$628.98</td>
                        </tr>
                    </tbody>
                </table>
                <p> Total Price: $628.98 </p>
                <a href="index.php"> Back To Home </a>
            </div>
        </div>
    </div>
    
<?php include "templates/footer.php" ?>

