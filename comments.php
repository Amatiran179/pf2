<?php if (!defined('ABSPATH')) exit;
if (post_password_required()) return; ?>
<section id="comments" class="comments-area">
<?php if (have_comments()): ?>
  <h2 class="comments-title">
    <?php $c=get_comments_number(); printf(esc_html(_n('%s Comment','%s Comments',$c,'pf2')), number_format_i18n($c)); ?>
  </h2>
  <ol class="comment-list"><?php wp_list_comments(['style'=>'ol','short_ping'=>true,'avatar_size'=>48]); ?></ol>
  <?php the_comments_navigation(); ?>
<?php endif;
if (!comments_open() && get_comments_number()):
  echo '<p class="no-comments">'.esc_html__('Comments are closed.','pf2').'</p>';
endif;
comment_form(); ?>
</section>
