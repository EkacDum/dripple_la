var zoom = .73;
var the_image 	= img_url;
var gh 			= jQuery("#grpinddivlog").height();
jQuery("#drip_zone_3 .the_cropper .cropper_subject").attr("src",the_image).removeAttr('style');

jQuery("#drip_zone_3 .the_cropper img.cropper_subject").removeClass("rot_90");
jQuery("#drip_zone_3 .the_cropper img.cropper_subject").removeClass("rot_180");
jQuery("#drip_zone_3 .the_cropper img.cropper_subject").removeClass("rot_270");
jQuery("#drip_zone_3 .the_cropper img.cropper_subject").removeClass("rot_0");

var theImage = new Image();
theImage.src = img_url;
var imageWidth = theImage.width;
var imageHeight = theImage.height;

jQuery("#drip_zone_3 .the_cropper").show();
var subject_width 			= LP_zoom(parseInt(imageWidth),zoom);
var subject_height 			= LP_zoom(parseInt(imageHeight),zoom);
var cropper_padding 		= LP_zoom(40,zoom);
var max_cropped_height 		= LP_zoom(800,zoom);
var cropper_dimension		= {};
cropper_dimension.width 	= LP_zoom(495,zoom);

var cropper_taller_height 	= parseFloat(jQuery("#drip_zone_3 .the_cropper .cropper_taller").height()) - parseFloat(jQuery("#drip_zone_3 .the_cropper .cropper_taller .drag").height());

/* Determining the height of the cropper */
var cheight = subject_height - (((subject_width - cropper_dimension.width)/subject_width)*subject_height);
var cropper_init_height 		= LP_zoom(550,zoom);