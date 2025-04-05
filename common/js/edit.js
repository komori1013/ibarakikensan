$(function(){
  var entry_url = $("#entry_url").val();
  $("#edit").click(function(){
    var price = $("#price").val(); 
    var product_id = $("#name").val();
    var quantity = $("#num").val();
    location.href = entry_url + "controller/user/editcart.php?product_id=" + product_id + "&price=" + price + "&quantity=" + quantity;
  });
});