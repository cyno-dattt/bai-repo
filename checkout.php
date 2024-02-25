<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = 'flat no. '. $_POST['flat'] .', '. $_POST['street'] .', '. $_POST['city'] .', '. $_POST['state'] .', '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){

      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'order placed successfully!';
   }else{
      $message[] = 'your cart is empty';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">

   <form action="" method="POST">

   <h3>Đơn đặt hàng hàng của bạn</h3>

      <div class="display-orders">
      <?php
         $grand_total = 0;
         $cart_items[] = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
         <p> <?= $fetch_cart['name']; ?> <span>(<?= '$'.$fetch_cart['price'].'/- x '. $fetch_cart['quantity']; ?>)</span> </p>
      <?php
            }
         }else{
            echo '<p class="empty">your cart is empty!</p>';
         }
      ?>
         <input type="hidden" name="total_products" value="<?= $total_products; ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
         <div class="grand-total">Tổng cộng: <span>$<?= $grand_total; ?>/-</span></div>
      </div>

      <h3>Đặt hàng của bạn</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Tên của bạn :</span>
            <input type="text" name="name" placeholder="nhập tên của bạn" class="box" maxlength="20" required>
         </div>
         <div class="inputBox">
            <span>Số của bạn :</span>
            <input type="number" name="number" placeholder="nhập số của bạn" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" required>
         </div>
         <div class="inputBox">
            <span>Email của bạn :</span>
            <input type="email" name="email" placeholder="nhập email của bạn" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Phương thức thanh toán :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">Thanh toán khi giao hàng</option>
               <option value="credit card">Thẻ tín dụng</option>
               <option value="momo">Momo</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Số nhà :</span>
            <input type="text" name="flat" placeholder="ví dụ số nhà" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Tên đường:</span>
            <input type="text" name="street" placeholder="ví dụ tên đường phố" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Thành phố :</span>
            <input type="text" name="city" placeholder="ví dụ Hồ Chí Minh" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Tình trạng :</span>
            <input type="text" name="state" placeholder="ví dụ còn mới 100%" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Quốc gia :</span>
            <input type="text" name="country" placeholder="ví dụ Việt Nam" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Mã pin :</span>
            <input type="number" min="0" name="pin_code" placeholder="ví dụ 123456" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>" value="Đặt hàng">

   </form>

</section>


<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
<div id="map" style="width:500px;height:300px;">
      <iframe style="width:1700px;height:600px;"  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4775762798427!2d106.63214551474887!3d10.774687292322717!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ea144839ef1%3A0x798819bdcd0522b0!2zVHLGsOG7nW5nIENhbyDEkOG6s25nIEPDtG5nIE5naOG7hyBUaMO0bmcgVGluIFRwLkjhu5MgQ2jDrSBNaW5o!5e0!3m2!1svi!2s!4v1677730660150!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</body>
</html>