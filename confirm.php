<?php
include_once("PayPalCheckOut/config.php");
include_once("PayPalCheckOut/paypalapi.php");

$paypalmode = ($PayPalMode=='sandbox') ? '.sandbox' : '';
$paypal= new MyPayPal();

if ($_POST) {
    
    $token = $_POST["token"];
    $payer_id = $_POST["payer_id"];

    //get session variables
    $item               = $_SESSION['Item'];
    $totalprice         = $_SESSION['ItemTotalPrice'];
    $TotalTaxAmount     = $_SESSION['TotalTaxAmount'] ;  //Sum of tax for all items in this order. 
    $HandalingCost      = $_SESSION['HandalingCost'];  //Handling cost for this order.
    $InsuranceCost      = $_SESSION['InsuranceCost'];  //shipping insurance cost for this order.
    $ShippinDiscount    = $_SESSION['ShippinDiscount']; //Shipping discount for this order. Specify this as negative number.
    $ShippinCost        = $_SESSION['ShippinCost']; 
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
    
    //execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
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
     <div class="container confirm">
        <div class="row">
            <div class="col-sm-6 col-lg-6 col-md-6">
                <h3> Your order Details: </h3>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-lg-6 col-md-6">
                <p> Shipping To: <?= htmlspecialchars($httpParsedResponseAr["SHIPTOSTREET"]) ?> <?= htmlspecialchars($httpParsedResponseAr["SHIPTOCITY"]) ?>,
                                 <?= htmlspecialchars($httpParsedResponseAr["SHIPTOCOUNTRYNAME"]) ?>, <?= htmlspecialchars($httpParsedResponseAr["SHIPTOZIP"]) ?> </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-lg-6 col-md-6">
                <p> Total Price: <?= htmlspecialchars($_SESSION['GrandTotal']) ?> </p>
                <hr>
            </div>
        </div>
        
       
        <div class="row">
            <div class="col-sm-6 col-lg-6 col-md-6">       
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
