<?php
require_once("helpers/BiddingHelper.php");
require_once("helpers/ProductHelper.php");
require_once('controllers/BiddingController.php');

$biddings = Bidding::query("519519591519", "WHERE ProductId = " . $product['ProductId'] . " ORDER BY BidAmount DESC");
$minimalAmount = BiddingHelper::defineMinimalAmount($biddings[0]["BidAmount"]);

if (isset($_POST['quickBid'])) {
    BiddingController::quickBid($product['ProductId'], $_POST['quickBid']);
}
if (isset($_POST['ProductId'])) {
  BiddingController::post($_POST);
}

?>
<div class="h-100 card bg-light-grey">
  <div class="card-header">
    <?php
    if (!$product['AuctionClosed']) {
      ?>
      <div class="timer">
        <h2 class="text-center"><?php echo ProductHelper::getDurationTimer($product); ?></h2>
      </div>
    <?php
    }
    ?>
  </div>
  <div class="card-body bg-grey" id="biddings">
    <div id="current-biddings" class="<?php echo $product["ProductId"]; ?>">
      <?php
      if ($product["AuctionClosed"] && $product['Buyer']) {
        echo ("<h1 style='position: relative; top: 50%; transform: translateY(-50%);'>De veiling is gesloten!</h1>
    <h3>" . $biddings[0]["Username"] . " is de winnaar. Gefelicteerd!</h3>");
      } elseif (!$product["AuctionClosed"]) {
        ?>
        <table width="100%" id="product-bid-table" class="table no-border">
          <?php foreach ($biddings as $key => $bidding) {
              $bidDate = $bidding["BidDate"];
              $bidTime = $bidding["BidTime"];
              if ($key == 0) { ?>
              <tr>
                <th id="bidAmountHeader">
                  € <?php echo number_format($bidding["BidAmount"], 2); ?>
                </th>
                <th id="bidDateTimeHeader">
                  <?php echo date("d-m-Y H:i:s", strtotime("$bidDate $bidTime")); ?>
                </th>
                <th id="bidUsernameHeader">
                  <?php echo $bidding["Username"]; ?>
                </th>
              </tr>
            <?php } else if ($key <= 2) { ?>
              <tr>
                <td id="bidAmount<?php echo $key; ?>">
                  € <?php echo number_format($bidding["BidAmount"], 2); ?>
                </td>
                <td id="bidDateTime<?php echo $key; ?>">
                  <?php echo date("d-m-Y H:i:s", strtotime("$bidDate $bidTime")); ?>
                </td>
                <td id="bidUsername<?php echo $key; ?>">
                  <?php echo $bidding["Username"]; ?>
                </td>
              </tr>
            <?php }
              }
              if (count($biddings) - 3 >= 1) { ?>
            <tr>
              <td> Er <?php if (count($biddings) - 3 != 1) {
                            echo "zijn (";
                          } else {
                            echo "is (";
                          }
                          echo count($biddings) - 3; ?>) meer bieding<?php if (count($biddings) - 3 != 1) echo "en"; ?> </td>
            </tr>
          <?php
            }
            ?>
        </table>
    </div>
    <form method="post" style="all:none;">
      <input type="hidden" name="ProductId" value="<?php echo $product['ProductId']; ?>">
      <div class="row py-3">
        <div class="col-lg-8">
          <input class="form-control mt-2" name="BidAmount" id="BidAmount" type="number" min="<?php echo round($product['Price'] * 2) / 2; ?>" step="0.5" value="<?php echo round($product['Price'] * 2) / 2; ?>" />
        </div>
        <div class="col-lg-4">
          <?php
            if (isset($_SESSION['authenticated'])) {
              ?>
            <button class='btn btn-primary text-white w-100 mt-2' type="submit">Bied</button>
          <?php
            } else {
              ?>
            <a class='btn btn-primary text-white w-100 mt-2' href="/inloggen">Inloggen</a>
          <?php
            }
            ?>
        </div>
      </div>
    </form>

    <div class="row">
      <form method="post" class="col-lg-4 mt-2">
        <button type="submit" name="quickBid" class="btn btn-primary text-white w-100" value="<?php echo $minimalAmount; ?>">
            Verhoog bod ( + € <?php echo $minimalAmount; ?> )
        </button>
      </form>
      <form method="post" class="col-lg-4 mt-2">
        <button type="submit" name="quickBid" class="btn btn-primary text-white w-100" value="<?php echo $minimalAmount * 2; ?>">
            Verhoog bod ( + € <?php echo $minimalAmount * 2; ?> )
        </button>
      </form>
      <form method="post" class="col-lg-4 mt-2">
        <button type="submit" name="quickBid" class="btn btn-primary text-white w-100" value="<?php echo $minimalAmount * 3; ?>">
            Verhoog bod ( + € <?php echo $minimalAmount * 3; ?> )
        </button>
      </form>
    </div>
  <?php
  } else {
  ?>
      <div>
          <h2>Deze veiling is gesloten.</h2>
          <h3>Er zijn geen winnaars</h3>
      </div>
  <?php } ?>
  </div>
</div>