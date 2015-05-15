<?php
include_once("PayPalCheckOut/config.php");
include_once("PayPalCheckOut/paypalapi.php");

$paypalmode = ($PayPalMode=='sandbox') ? '.sandbox' : '';
$paypal= new MyPayPal();

if ($_POST) {
    //we will be using these two variables to execute the "DoExpressCheckoutPayment"
    //Note: we haven't received any payment yet.   
    $token = $_POST["token"];
    $payer_id = $_POST["payer_id"];

    //get session variables
    $item               = $_SESSION['Item'];
    $totalprice         = $_SESSION['ItemTotalPrice'];
    $TotalTaxAmount     = $_SESSION['TotalTaxAmount'] ;  //Sum of tax for all items in this order. 
    $HandalingCost      = $_SESSION['HandalingCost'];  //Handling cost for this order.
    $InsuranceCost      = $_SESSION['InsuranceCost'];  //shipping insurance cost for this order.
    $ShippinDiscount    = $_SESSION['ShippinDiscount']; //Shipping discount for this order. Specify this as negative number.
    $ShippinCost        = $_SESSION['ShippinCost']; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.
    $GrandTotal         = $_SESSION['GrandTotal'];

    $padata =   '&TOKEN='.urlencode($token).
                '&PAYERID='.urlencode($payer_id).
                '&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");
                
    for($i=0;$i<count($item);$i++)
        {

            $padata = $padata.'&L_PAYMENTREQUEST_0_NAME'.$i.'='.urlencode($item[$i]->name).
                '&L_PAYMENTREQUEST_0_AMT'.$i.'='.urlencode((float)$item[$i]->price).
                '&L_PAYMENTREQUEST_0_QTY'.$i.'='. urlencode((int)$item[$i]->quantity);
        }

        $padata= $padata."&PAYMENTREQUEST_0_ITEMAMT=".urlencode($totalprice).
                '&PAYMENTREQUEST_0_TAXAMT='.urlencode($TotalTaxAmount).
                '&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($ShippinCost).
                '&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($HandalingCost).
                '&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($ShippinDiscount).
                '&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($InsuranceCost).
                '&PAYMENTREQUEST_0_AMT='.urlencode($GrandTotal).
                '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode);
    
    //We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
    $paypal= new MyPayPal();
    $httpParsedResponseAr = $paypal->PPHttpPost('DoExpressCheckoutPayment', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
    //Check if everything went ok..
    if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) or "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
    {
        header("Location: /success.php");
    } else {
        ?>
        <?php include "templates/header.php" ?>
            <div style="color:red"><b>Error : </b>
                <?= urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]) ?>
            </div>
            <pre>
                <?php print_r($httpParsedResponseAr) ?>
            </pre>
        <?php include "templates/footer.php" ?>
        <?php
    }
    
    die();
}
else{
$getexp = '&'.http_build_query(array('TOKEN'=>$_GET['token']));
$httpParsedResponseAr = $paypal->PPHttpPost('GetExpressCheckoutDetails', $getexp, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

?>
    <?php include "templates/header.php" ?>
    <pre>
    <?php print_r($httpParsedResponseAr) ?>
    </pre>
     <div class="container confirm">
        <div class="row">
            <div class="col-sm-6 col-lg-6 col-md-6">
                <h3> Your order: </h3>
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
                <form method="POST" action="confirm.php">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($httpParsedResponseAr['TOKEN']) ?>">
                    <input type="hidden" name="payer_id" value="<?= htmlspecialchars($httpParsedResponseAr['PAYERID']) ?>">
                    <button class="confirm-btn btn btn-primary"> Pay Now </button>
                </form>
            </div>
        </div>
    </div>
    <?php include "templates/footer.php" ?>
<?php 
 } 
?>
