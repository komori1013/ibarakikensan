$(function(){
  $("#cart_in").click(function(){
    var entry_url = $("#entry_id").val();
    var price = $("#price").val(); 
    var product_id = $("#product_id").val();
    var quantity = $("#quantity").val();
    location.href = entry_url + "controller/cart/cart.php?product_id=" + product_id + "&price=" + price + "&quantity=" + quantity;
  });
});