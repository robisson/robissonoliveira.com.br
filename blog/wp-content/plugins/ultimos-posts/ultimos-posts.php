<?php
/*
Plugin Name: Ultimos-posts
Description: Veja seus ultimos tres posts
Version: 1.0
Author: Robisson Oliveira
Author URI: http://robissonoliveira.com.br
*/

class rc_ultimos_posts extends WP_Widget {

	function rc_ultimos_posts() {
		//Load Language
		load_plugin_textdomain( 'ultimos-posts', false, dirname( plugin_basename( __FILE__ ) ) .  '/lang' );
		$widget_ops = array( 'description' => __( 'Shows most recent posts. You can customize it easily.', 'ultimos-posts' ) );
		//Create widget
		$this->WP_Widget( 'advancedrecentposts', __( 'Veja seus ultimos posts', 'ultimos-posts' ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );
		echo $before_widget;
		$title = empty( $instance[ 'title' ] ) ? '' : apply_filters( 'widget_title', $instance[ 'title' ] );
		$link = empty( $instance[ 'link' ]) ? '' : $instance[ 'link' ];

		if ( !empty( $title ) &&  !empty( $link ) ) {
				echo $before_title . '<a href="' . $link . '">' . $title . '</a>' . $after_title;
		}
		else if ( !empty( $title ) ) {
			 echo $before_title . $title . $after_title;
		}
        //print recent posts
		rc_recentposts();
		echo $after_widget;

  } //end of widget()
}

function rc_recentposts($args = '', $echo = true) {

    //aqui chamamos a global $post que traz os dados da página atual.
    global $post;

    //Aqui implementamos os argumentos da query, que escolhe o post_parent como a página atual, ou seja, o ID do post, e explica que queremos somente o tipo de post (post_type) page.
    $args = array(
    'post_type' => 'post',
	'posts_per_page'    => '3'
    );
    
    //Aqui a query
    $filhas = new WP_query($args);
    
    // Aqui vem o nosso loop que trará os dados de saída! Se a variável $filhas for verdadeira, ela traz os dados enquanto houver. se não houve filhas, escreve "Não há páginas Filhas"
    if ($filhas->have_posts()) :

	$saida = '';
	
?>
	<style type="text/css">
	    body.template_home #content, #sidebar {
			float:left;	
		}
    	body.template_home #content {
			width:540px;
		}
		
		#contact_form_default{
			width:360px;	
			
		}
		#sidebar {
			padding-left:20px;	
		}
		
		#contact_form_default .inner{
			padding-top:10px;	
		}
		
		div.sticky_note ol li input, div.sticky_note ol li textarea {width:230px;}
		
		@media only screen and (max-width: 500px) {
			body.template_home #content {
				width:auto;
			}
			#sidebar{
				display:block;	
			}
			body.template_home #content, #sidebar {float:none;}
			#sidebar{
				width:auto;
				padding:0px;	
			}
			
			#contact_form_default{
				width:auto;	
			}
			
			div.sticky_note ol li input, div.sticky_note ol li textarea {width:auto;}
			
			div.sticky_note button {
				width:140px;
				margin-left:20%;	
			}
			
			#sidebar .inner {
				overflow:hidden;	
			}
		}
		
    </style>
           <div id="content" role="main">
		   <h1>&Uacute;ltimos Posts</h1>		   
		   <?php
	   	while ($filhas->have_posts()) : $filhas->the_post();

?>
<div class="post <?php the_ID(); ?> post-listing" <?php post_class(); ?>>
	<h2 class="title">
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'leap' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
			<?php the_title(); ?>
		</a>
	</h2>
	<div class="entry-meta">
		<?php comments_popup_link( __( '0 comments', 'leap' ), __( '1 Comment', 'leap' ), __( '% Comments', 'leap' ), 'comments' ); ?>
		<span>&bull;</span>
		<?php 
		
		the_date(); 
		$tags_list = get_the_tag_list( '', ', ' );
		if ($tags_list): 
		
		?>
			<span>&bull;</span>
			<?php print($tags_list); ?>
		<?php endif; ?>
	</div><!-- .entry-meta -->
	<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
	<div class="entry entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry entry-listing">
		<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'leap' ) ); ?>
		<div class="clear"></div>
	</div>
	<?php endif; ?>
	
</div><!-- #post-## -->

<?php 

endwhile; // End the loop.?>
	   </div>
	   <div id="sidebar">
	       <div id="contact_form_default" class="contact_form sticky_note">
           
          <div class="inner">
          <form action="http://www.robissonoliveira.com.br/contato" id="contact_form_default" method="post">
          <fieldset>
          <center><h3>Contato</h3></center>
          <ol>
          <li><label for="default_contact_name"><span>Nome:</span></label>
          <div><input type="text" name="default_contact_name" id="default_contact_name" value="" class="requiredField" placeholder="Digite seu nome..."></div>
          </li>
          <li><label for="default_contact_email"><span>Email:</span></label>
          <div><input type="text" name="default_contact_email" id="default_contact_email" value="" class="requiredField" placeholder="Digite seu email..."></div>
          </li>
          <li class="textarea"><label for="default_contact_message"><span>Mensagem:</span></label>
          <div><textarea name="default_contact_message" id="default_contact_message" rows="6" cols="50" class="requiredField elastic" placeholder="Digite sua mensagem..." style="overflow: hidden;"></textarea><div style="position: absolute; display: none; word-wrap: break-word; border: 0px none rgb(0, 0, 0); font-weight: normal; width: 300px; font-family: 'Helvetica Neue', Helvetica, Arial, serif; line-height: 22px; font-size: 15px; padding: 0px;">&nbsp;</div></div>
          </li>
          </ol>
          <p><button type="submit" class="submit">Enviar Email</button><input type="hidden" value="default" class="form_name" name="leap_contact_form">
          </p><div class="error_status"></div>
          </fieldset>
          <div class="successMessage" style="display:none">Olá, <p></p>
          <p>    Recebi seu email, em breve entrarei em contato.</p>
          <p>Obrigado.</p></div>
          <input type="hidden" name="ajax" value="1">
          </form>
			
          </div>
          
          </div>  	

	  </div>
	  <div style="clear:both;"></div>	
<?php
	
    else :
    
        $saida = '<p>Não há páginas Filhas.</p>';
		
    endif;
    
    // Aqui resetamos a Query, para não ter problemas com o resto do conteúdo do template.
    wp_reset_postdata();
    // Por fim, retornamos a variável $saida.
    
    echo $saida;


}

add_action( 'widgets_init', create_function('', 'return register_widget("rc_ultimos_posts");') );
//Register Widget
?>
