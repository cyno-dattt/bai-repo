<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đặt hàng</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="orders">

   <h1 class="heading">Đặt hàng</h1>

   <div class="box-container">

   <?php
      if($user_id == ''){
         echo '<p class="empty">Vui lòng đăng nhập để xem đơn đặt hàng của bạn</p>';
      }else{
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
         $select_orders->execute([$user_id]);
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>Đặt vào : <span><?= $fetch_orders['placed_on']; ?></span></p>
      <p>Tên : <span><?= $fetch_orders['name']; ?></span></p>
      <p>Email : <span><?= $fetch_orders['email']; ?></span></p>
      <p>Số : <span><?= $fetch_orders['number']; ?></span></p>
      <p>địa chỉ : <span><?= $fetch_orders['address']; ?></span></p>
      <p>Phương thức thanh toán : <span><?= $fetch_orders['method']; ?></span></p>
      <p>Đơn đặt hàng của bạn : <span><?= $fetch_orders['total_products']; ?></span></p>
      <p>Tổng giá : <span>$<?= $fetch_orders['total_price']; ?>/-</span></p>
      <p>Trạng thái thanh toán : <span style="color:<?php if($fetch_orders['payment_status'] == 'pending'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>
   </div>
   <?php
      }
      }else{
         echo '<p class="empty">Chưa có đơn hàng nào được đặt!</p>';
      }
      }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
<div id="map" style="width:500px;height:300px;">
      <iframe style="width:1700px;height:600px;"  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4775762798427!2d106.63214551474887!3d10.774687292322717!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ea144839ef1%3A0x798819bdcd0522b0!2zVHLGsOG7nW5nIENhbyDEkOG6s25nIEPDtG5nIE5naOG7hyBUaMO0bmcgVGluIFRwLkjhu5MgQ2jDrSBNaW5o!5e0!3m2!1svi!2s!4v1677730660150!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</body>
</html>