<?php if (!defined('ABSPATH')) exit; ?>
<aside id="secondary" class="widget-area" role="complementary">
  <?php if (is_active_sidebar('sidebar-1')): dynamic_sidebar('sidebar-1'); else: ?>
    <section class="widget">
      <h2 class="widget-title"><?php echo esc_html__('Sidebar','pf2'); ?></h2>
      <p><?php echo esc_html__('Tambahkan widget melalui Appearance → Widgets.','pf2'); ?></p>
    </section>
  <?php endif; ?>
</aside>
