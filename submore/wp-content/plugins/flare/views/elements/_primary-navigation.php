<ul id="<?php echo $namespace; ?>-primary-navigation">
    <?php foreach( $menu_hooks as $menu_key => $menu_hook ): ?>
        <li>
            <a id="<?php echo "{$namespace}-nav-{$menu_key}"; ?>" href="<?php echo admin_url( 'admin.php?page=' . $menu_hook['path'] ); ?>"<?php if( $screen->id == $menu_hook['hook'] ) echo ' class="active"'; ?>>
                <span><?php echo $menu_hook['label']; ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<div id="deprecation-notice" class="updated below-h2"><p>This Flare plugin is no longer in active development. See <a href="http://wordpress.org/support/topic/a-few-of-the-common-questions-about-the-plugin?replies=1#pagebody" target="_blank">this forum post</a> for the full explanation.</p></div>