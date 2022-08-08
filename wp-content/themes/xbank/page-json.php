<?php
global $post;
global $agent_wid;
$wpestate_options=wpestate_page_details($post->ID); 
// JSON ARRAY 1
 $arrayjson = array();
$arrayjson['customerCountry'] = 'Spain';
$arrayjson['customerCode'] = 'ilc077ef5bcb04494b428b44d485ea1070a0bcce3b0';
$arrayjson['customerReference'] = 'IndieProp';
$arrayjson['customerSendDate'] = '2021/10/19 16:50:12';
$arrayjson['customerContact'] = array(
		'contactEmail'=> 'info@indieprop.com',
		'contactPrimaryPhonePrefix'=> '34',
		'contactPrimaryPhoneNumber'=> '930267942',
		'contactSecondaryPhonePrefix'=> '34',
		'contactSecondaryPhoneNumber'=> '654597030');

// JSON ARRAY 1 - EOF -
// JSON ARRAY customerProperties
 $argsen = array(
            'post_type' => 'estate_property',
			'post_status' => 'publish',
    'orderby' => 'date',
    'posts_per_page' => '-1',
    'order' => 'DESC',);
			
  $loop = new WP_Query( $argsen );
  if( $loop->have_posts() ):
  while( $loop->have_posts() ): $loop->the_post();	

	// Campos
	$property_id = strval($post->ID);
	$property_date = get_the_date("Y-m-d H:i:s");
	$property_price     =  intval(get_post_meta($post->ID, 'property_price', true));
	$property_price_comunidad     =  intval(get_post_meta($post->ID, 'property_hoa', true));
	$property_type     =  get_the_terms( $post_id, 'property_category',0);
	$property_town     =  get_the_terms( $post_id, 'property_city',0);
	$property_province     =  get_the_terms( $post_id, 'property_county_state',0);
	$property_latitude     =  floatval(get_post_meta($post->ID, 'property_latitude', true));
	$property_longitude     =  floatval(get_post_meta($post->ID, 'property_longitude', true));
	$property_energy_class     =  get_post_meta($post->ID, 'energy_class', true);
	$property_energy_index     =  floatval(get_post_meta($post->ID, 'energy_index', true)); // energy index no es emisiones
	$property_youtube     =  get_post_meta($post->ID, 'the_content', true);
	$property_virtual_tour     =  get_post_meta($post->ID, 'embed_virtual_tour', true);
	$property_description     =  get_post_meta($post->ID, 'embed_virtual_tour', true);
	$property_content = apply_filters( 'the_content', get_the_content() );		
	$property_features     =  get_the_terms( $post_id, 'property_features',0);
	$property_media     =  get_post_meta($post->ID, 'property_media', true);
	$property_cp = get_post_meta($post->ID, 'property_zip', true);
	$property_calle = get_post_meta($post->ID, 'property_address', true);
	$urbaniza = get_post_meta($post->ID, 'urbaniza', true);
	$property_estado = get_the_terms( $post_id, 'property_status',0);			
	$property_size      =   intval(get_post_meta($post->ID,'metrosutil',true));
	$property_plot_size     =   intval(get_post_meta($post->ID,'metroscons',true));			
    $property_rooms     =   intval(get_post_meta($post->ID,'property_rooms',true));
	$property_bedrooms     =   intval(get_post_meta($post->ID,'property_bedrooms',true));
    $property_bathrooms =   intval(get_post_meta($post->ID,'property_bathrooms',true));
	$property_year =   intval(get_post_meta($post->ID,'property-year',true));
	$property_garage =   get_post_meta($post->ID,'property-garage',true);
	$property_floor =   get_post_meta($post->ID,'stories-number',true);
	$property_orientacion =   get_post_meta($post->ID,'orientacion',true);
	$property_ascensor =   get_post_meta($post->ID,'ascensores',true);
	
	// AGENTS
	$name                = get_the_author_meta( 'first_name' ,$agent_wid);
	$agent_phone         = get_the_author_meta( 'phone'  ,$agent_wid);
	$agent_email         = get_the_author_meta( 'user_email' ,$agent_wid );
	$agent_mobile        = get_the_author_meta( 'mobile'  ,$agent_wid);



    // Get post type taxonomies.
$feature_piscina = false;
$feature_jardin = false;
$feature_air = false;
$feature_portero = false;
$feature_trastero = false;
$feature_terraza = false;
 
        // Get the terms related to post.
        $terms = get_the_terms( $property_id, 'property_features');
 
        if ( ! empty( $terms ) ) {            
            foreach ( $terms as $term ) {
			$a = $term->slug;
				
				switch($a){
					case "piscina":
						
						$feature_piscina = true;
						break;
						
					case "jardin":
						
						$feature_jardin = true;
						break;
					
					case "aire-acondicionado":
						
						$feature_air = true;
						break;
						
					case "portero":
						
						$feature_air = true;
						break;
						
					case "trastero":
						
						$feature_trastero = true;
						break;
						
					case "terraza":
						
						$feature_terraza = true;
						break;
            }
        }
		}
  // IMAGENES

 $argimg      = array(
    'numberposts'       => -1,
    'post_type'         => 'attachment',
    'post_mime_type'    => 'image',
    'post_parent'       => $property_id,
    'post_status'       => null,    
    'orderby'           => 'menu_order',
    'order'             => 'ASC'
    );
$n = 0;
    $images = get_posts($argimg);
	if($images) { 
		
    	foreach($images as $image) {
			$n = $n + 1;
			
			$arrayimages[] = array(
				'imageOrder'=> $n,
				'imageUrl'=> wp_get_attachment_url($image->ID));				   
			}}					   
								  
// ARRAY CASAS

	$casas[] = array(
   		'propertyCode'=> $property_id,
		'propertyReference'=> $property_id,
		'propertyVisibility'=> 'idealista',
		'propertyOperation'=> array_filter(array(
			'operationType'=> 'sale',
			'operationPrice'=> $property_price,
			'operationPriceCommunity'=> $property_price_comunidad)),
		'propertyContact'=> array_filter(array(
			'contactName'=> $name,
			'contactEmail'=> $agent_email,
			'contactPrimaryPhonePrefix'=> '34',
			'contactPrimaryPhoneNumber'=> $agent_phone,
			'contactSecondaryPhonePrefix'=> '34',
			'contactSecondaryPhoneNumber'=> $agent_mobile)),
		'propertyAddress'=> array_filter(array(
				'addressVisibility'=> 'hidden',
				'addressStreetName'=> $property_calle,
				'addressUrbanization'=> $urbaniza,
				'addressPostalCode'=> $property_cp,
				'addressTown'=> $property_town[0]->name,
				'addressCountry'=> 'Spain',
				'addressCoordinatesPrecision'=> 'moved',
				'addressCoordinatesLatitude'=> $property_latitude,
				'addressCoordinatesLongitude'=> $property_longitude)),		
		'propertyFeatures' => array_filter(array(		
   			'featuresType'=> $property_type[0]->name,
			'featuresAreaConstructed'=> $property_size,
			'featuresAreaUsable'=> $property_plot_size,
			'featuresBathroomNumber'=> $property_bathrooms,
			'featuresBedroomNumber'=> $property_bedrooms,
			'featuresBuiltYear'=> $property_year,			
			'featuresConservation'=> $property_estado[0]->name,
			'featuresEnergyCertificateRating'=> $property_energy_class,
			'featuresEnergyCertificatePerformance'=> $property_energy_index,			
			'featuresPool'=> $feature_piscina,
			'featuresGarden'=> $feature_jardin,
			'featuresConditionedAir'=> $feature_air,
			'featuresDoorman'=> $feature_portero,
			'featuresWardrobes'=> $feature_trastero,
			'featuresTerrace'=> $feature_terraza)),		
			'propertyDescriptions' => array(array(
				'descriptionLanguage'=> "spanish",
				'descriptionText'=> apply_filters('the_content', $post->post_content))),
			'propertyImages' => $arrayimages
	);

	unset($arrayimages);
	
  endwhile; 
  endif;

$arrayjson["customerProperties"]=array_filter($casas);

	
$json_string = json_encode(array_filter($arrayjson),JSON_PRETTY_PRINT);
$file = 'ilc077ef5bcb04494b428b44d485ea1070a0bcce3b0.json';
file_put_contents($file, $json_string);




// Sigle - Blog post
// Wp Estate Pack

get_header(); 

?>
<div class="row">
   
    <div class="col-xs-12 <?php print esc_html($wpestate_options['content_class']);?> single_width_page"  style="padding-top:90px;">
        
         <?php get_template_part('templates/ajax_container'); ?>
        
        <?php while (have_posts()) : the_post(); ?>
            <?php if (esc_html( get_post_meta($post->ID, 'page_show_title', true) ) != 'no') { ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>
            <?php } ?>         
            <div class="single-content"><?php the_content();?></div><!-- single content-->

        <!-- #comments start-->
        <?php 
        if ( comments_open() || get_comments_number() ) :
            comments_template('', true);
        endif;
        ?>	
        <!-- end comments -->   
        
        <?php endwhile; // end of the loop. ?>
    </div>
  
<?php   include get_theme_file_path('sidebar.php'); ?>
</div>   
<?php get_footer(); ?>

?>
