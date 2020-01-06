<?php
require_once('controllers/UserController.php');
require_once('controllers/BiddingController.php');
require_once('controllers/FileController.php');
require_once("views/shared/objectCards/horizontal-sm.php");
require_once('helpers/ProductHelper.php');

// TODO Text based (all, winning, losing)
switch (filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_NUMBER_INT)) {
    case 0:
        $title = 'Alle biedingen';
        $products = BiddingController::getFromPerson($_SESSION['authenticated']['Username']);
        break;
    case 1:
        $title = 'Winnende biedingen';
        $products = BiddingController::getWinningFromPerson($_SESSION['authenticated']['Username']);
        break;
    case 2:
        $title = 'Verliezende biedingen';
        $products = BiddingController::getLosingFromPerson($_SESSION['authenticated']['Username']);
        break;
    default:
        $title = 'Mijn biedingen';
        $products = BiddingController::getFromPerson($_SESSION['authenticated']['Username']);
        break;
}

if (isset($_POST['submit'])) {
    BiddingController::quickBid($_POST['productId'], $_POST['submit']);
}

?>

<h1><?php echo $title; ?></h1>

<div class="row" style="padding-bottom: 30px;">
    <div class="col-md-6 col-sm-12">
        <div class="btn-group" role="group">
            <a href="/gebruiker/biedingen/0" class="btn btn-primary text-white <?php echo active_url('gebruiker/biedingen/0', 'active') ?>">Alle biedingen</a>
            <a href="/gebruiker/biedingen/1" class="btn btn-primary text-white <?php echo active_url('gebruiker/biedingen/1', 'active') ?>">Winnende biedingen</a>
            <a href="/gebruiker/biedingen/2" class="btn btn-primary text-white <?php echo active_url('gebruiker/biedingen/2', 'active') ?>">Verliezende biedingen</a>
        </div>
    </div>
</div>

<div class="row">
    <?php foreach ($products as $key => $product) { ?>
        <div class="col-lg-4 mt-2">
            <?php echo HorizontalSm::generate(
                [
                    "title" => $product["Title"],
                    "price" => $product['Price'],
                    "duration" => ProductHelper::getDurationTimer($product),
                    "productId" => $product["ProductId"],
                    "track" => ($product["BidAmount"] !== null),
                    "biddedPrice" => $product["BidAmount"],
                    "winning" => ($product["Buyer"] === ($_SESSION['authenticated']['Username'] ?? false))
                ],
                FileController::get($product["ProductId"], "ProductId", 1)
            ); ?>
        </div>
    <?php } ?>
</div>

<?php if ($products == null) { ?>
    <div class="alert alert-primary" role="alert">Je hebt nog geen biedingen gedaan!</div>
<?php } ?>