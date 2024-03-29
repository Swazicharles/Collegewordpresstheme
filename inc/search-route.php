<?php 

add_action('rest_api_init','universityRegisterSearch');

function universityRegisterSearch(){
    register_rest_route('university/v1','search',array(
        'methods'=>WP_REST_SERVER::READABLE,
        'callback'=>'universitySearchResults'
    ));
}
// just create a query with a post type and create array with only the data we want
function universitySearchResults($data){
 $mainQuery = new WP_Query(array(
     'post_type'=>array('post','page','professor', 'program','campus','event'),
     's'=>sanitize_text_field($data['term'])
 ));

 $results = array(
     'generalInfo'=>array(),
     'professors' =>array(),
     'events' =>array(),
     'programs' =>array(),
     'campuses'=>array()
 );
 while($mainQuery->have_posts()){
   $mainQuery->the_post();

   if(get_post_type()=='post' OR get_post_type()=='page'){
    array_push($results['generalInfo'], array(
        'title' => get_the_title(),
        'permalink'=>get_the_permalink(),
        'postType' =>get_post_type(),
        'authorName'=>get_the_author()
    ));

   }
   if(get_post_type()=='professor'){
    array_push($results['professors'], array(
        'title' => get_the_title(),
        'permalink'=>get_the_permalink(q),
        'image'=>get_the_post_thumbnail_url(0,'professorLandscape')
    ));

   }
   if(get_post_type()=='program'){
    array_push($results['programs'], array(
        'title' => get_the_title(),
        'permalink'=>get_the_permalink(),
        'ID' => get_the_id()
    ));

   }

   if(get_post_type()=='campus'){
    array_push($results['campuses'], array(
        'title' => get_the_title(),
        'permalink'=>get_the_permalink()
    ));

   }

   if(get_post_type()=='event'){ 

    $description = null;

    if(has_excerpt()){
        $description = get_the_excerpt();
     } else{
       $description = wp_trim_words(get_the_content(),18);
  }

    $eventDate = new DateTime(get_field('event_date'));
    array_push($results['events'], array(
        'title' => get_the_title(),
        'permalink'=>get_the_permalink(),
        'month'=>$eventDate->format('M'),
        'day'=>$eventDate->format('d'),
        'description'=>$description
    ));

   }
 }

 if($results['programs']){
    $programsMetaQuery = array('relation'=>'OR');

    foreach($results['programs'] as $item){
      array_push($programsMetaQuery, array(
       'key'=>'related_programs',
       'compare'=> 'LIKE',
       'value'=>'"'.$item['ID'].'"'
   ));
    }
    $programRelationshipQuery = new WP_Query(array(
       'post_type'=>array('professor','event'),
       'meta_query'=>$programsMetaQuery
   ));
   
   while($programRelationshipQuery->have_posts()){
         $programRelationshipQuery->the_post();

         if(get_post_type()=='event'){ 

            $description = null;
        
            if(has_excerpt()){
                $description = get_the_excerpt();
             } else{
               $description = wp_trim_words(get_the_content(),18);
          }
        
            $eventDate = new DateTime(get_field('event_date'));
            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink'=>get_the_permalink(),
                'month'=>$eventDate->format('M'),
                'day'=>$eventDate->format('d'),
                'description'=>$description
            ));
        
           }
   
         if(get_post_type()=='professor'){
           array_push($results['professors'], array(
               'title' => get_the_title(),
               'permalink'=>get_the_permalink(),
               'image'=>get_the_post_thumbnail_url(0,'professorLandscape')
           ));
       
          }
       /* This line here is to prevent duplicates for your queries*/   
   }   $results['professors'] = array_values(array_unique($results['professors'],SORT_REGULAR));
       $results['events'] = array_values(array_unique($results['events'],SORT_REGULAR));

 }
 
    return $results;
}