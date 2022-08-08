<?php
global $post; 
header("Content-Type: application/rss+xml; charset=UTF-8");
$filename = 'witei.xml';
header("Content-disposition: attachment; filename=\"$filename\"");
echo '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>';

$wpestate_options=wpestate_page_details($post->ID); 
echo '<root>';
 echo '<kyero>';
  echo '<feed_version>3</feed_version>';
 echo '</kyero>';
 $argsen = array(
            'post_type' => 'estate_property',
			'post_status' => 'publish',
    'orderby' => 'date',
    'posts_per_page' => '-1',
    'order' => 'DESC',); 
 
$wpestate_currency  =   '';
$where_currency     =   '';
				
  $loop = new WP_Query( $argsen );
  if( $loop->have_posts() ):

  while( $loop->have_posts() ): $loop->the_post();	

	// Campos
	$property_id = $post->ID;
	$property_date = get_the_date("Y-m-d H:i:s");
	$property_price     =  get_post_meta($post->ID, 'property_price', true);				
	$property_type     =  get_the_terms( $post_id, 'property_category',0);
	$property_town     =  get_the_terms( $post_id, 'property_city',0);
	$property_province     =  get_the_terms( $post_id, 'property_county_state',0);
	$property_latitude     =  get_post_meta($post->ID, 'property_latitude', true);
	$property_longitude     =  get_post_meta($post->ID, 'property_longitude', true);
	$property_energy_class     =  get_post_meta($post->ID, 'energy_class', true);
	$property_energy_index     =  get_post_meta($post->ID, 'energy_index', true); // energy index no es emisiones
	$property_youtube     =  get_post_meta($post->ID, 'embed_video_id', true);
	$property_virtual_tour     =  get_post_meta($post->ID, 'embed_virtual_tour', true);
	$property_description     =  get_post_meta($post->ID, 'embed_virtual_tour', true);
	$property_content = apply_filters( 'the_content', get_the_content() );		
	$property_features     =  get_the_terms( $post_id, 'property_features',0);
	$property_media     =  get_post_meta($post->ID, 'property_media', true);
	$property_cp = get_post_meta($post->ID, 'property_zip', true);
	$property_calle = get_post_meta($post->ID, 'property_address', true);
				
	$property_size      =   get_post_meta($post->ID,'metrosutil',true);
	$property_plot_size     =   get_post_meta($post->ID,'metroscons',true);			
    $property_rooms     =   get_post_meta($post->ID,'property_rooms',true);
	$property_bedrooms     =   get_post_meta($post->ID,'property_bedrooms',true);
    $property_bathrooms =   get_post_meta($post->ID,'property_bathrooms',true);
	$property_year =   get_post_meta($post->ID,'property-year',true);
	$property_garage =   get_post_meta($post->ID,'property-garage',true);
	$property_floor =   get_post_meta($post->ID,'stories-number',true);
	$property_orientacion =   get_post_meta($post->ID,'orientacion',true);
	$property_ascensor =   get_post_meta($post->ID,'ascensores',true);
	
echo '<property>';			
	echo '<id>' . $property_id . '</id>';
	echo '<date>' . $property_date . '</date>';
	echo '<ref>' . $property_id . '</ref>';
	echo '<price>' . $property_price   . '</price>';
	echo '<currency>EUR</currency>';
	echo '<price_freq>sale</price_freq>';
	echo '<part_ownership>0</part_ownership>';
				echo '<leasehold>0</leasehold>';  // sin resolver
				echo '<new_build>0</new_build>'; // sin resolver				
	echo '<type>' . $property_type[0]->name . '</type>';
	echo '<town>' . $property_town[0]->name . '</town>';
	echo '<province>' . $property_province[0]->name . '</province>';
	echo '<country>spain</country>';
	echo '<location>';
		echo '<latitude>'. $property_latitude   . '</latitude>';;
		echo '<longitude>'. $property_longitude   . '</longitude>';;
	echo '</location>';
	if ( !empty($property_rooms) ){echo '<rooms>' . $property_rooms . '</rooms>';}
	echo '<beds>' . $property_bedrooms . '</beds>';
	echo '<baths>' . $property_bathrooms . '</baths>';
	if ( !empty($property_year) ){echo '<property_year>' . $property_year . '</property_year>';}
	if ( !empty($property_garage) ){echo '<property_garage>' . $property_garage . '</property_garage>';}
	if ( !empty($property_floor) ){echo '<floor>' . $property_floor . '</floor>';}
	if ( !empty($property_orientacion) ){echo '<orientation>' . $property_orientacion . '</orientation>';}
	if ( !empty($property_ascensor) ){echo '<elevator>' . $property_ascensor . '</elevator>';}
	echo '<surface_area>';
		echo '<built>' . $property_size . '</built>';
		echo '<plot>' . $property_plot_size . '</plot>';
    echo '</surface_area>';
	echo '<energy_rating>';
		echo '<consumption>' . $property_energy_class . '</consumption>';
		echo '<energy_index>' . $property_energy_index . '</energy_index>';
	echo '</energy_rating>';
	echo '<url>';
		echo '<es>' . get_post_permalink() . '</es>';		
	echo '</url>';
if ( !empty($property_youtube) ){echo '<video_url>';
		echo '<es>https://www.youtube.com/watch?v=' . $property_youtube . '</es>';		
	echo '</video_url>';}		
	echo '<virtual_tour_url>' . $property_virtual_tour . '</virtual_tour_url>';			
	echo '<desc>';
		echo '<es>' . $property_content . '</es>';		
	echo '</desc>';
	echo '<features>';			
	// FEATURES
		$tax2 = 'property_features';
		$terms2 = get_terms( $tax2 );
        $count2 = count( $terms2 );
	

        if ( $count2 > 0 ): 
            foreach ( $terms2 as $term2 ) {                
                echo '<feature>' . $term2->name . '</feature>';
            }
		endif;
	$n= 0;
	echo '</features>';
				
				 
   $args      = array(
    'numberposts'       => -1,
    'post_type'         => 'attachment',
    'post_mime_type'    => 'image',
    'post_parent'       => $post->ID,
    'post_status'       => null,
    'exclude'           => get_post_thumbnail_id(),
    'orderby'           => 'menu_order',
    'order'             => 'ASC'
    );
    $images = get_posts($args);
	if($images) { 
	echo '<images>';
    foreach($images as $image) { 
				
				$n = $n + 1;
		echo '<image id="'. $n .'">';
				echo '<url>' . wp_get_attachment_url($image->ID) . '</url>';		
		echo '</image>';	
  }
	echo '</images>';			
}						
echo '</property>';

  endwhile; 
  endif;
  
  echo '</root>';




$dom = new DOMDocument;
$dom->preserveWhiteSpace = FALSE;


//Save XML as a file
$dom->save('xml/sitemap.xml');

?>
