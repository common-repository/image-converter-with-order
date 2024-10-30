// Show live preview of image
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      jQuery('#woo_bw_image_order_upload_preview').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}

jQuery("#woo_bw_image_order_upload_btn").change(function(){
    jQuery( "#woo_bw_image_order_upload_preview" ).css( "display", "block" );
  readURL(this);
});