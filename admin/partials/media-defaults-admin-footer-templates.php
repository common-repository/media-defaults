<?php

/**
 * Contains the HTML output for the <script> templates that control the media options in the
 * "Add Media" overlay.
 * 
 * This file is included by MediaDefaultsAdmin::printMediaTemplates and has access to that
 * class' properties and methods.
 */

?>


<script type="text/html" id="tmpl-gallery-settings-sswmd">
    <h2><?php _e( 'Gallery Settings' ); ?></h2>

    <label class="setting">
        <span><?php _e('Link To', 'sswmd'); ?></span>
        <select class="link-to"
            data-setting="link"
            <# if ( data.userSettings ) { #>
                data-user-setting="urlbutton"
            <# } #>>

            <option value="post" <# if ( ! wp.media.galleryDefaults.link || 'post' == wp.media.galleryDefaults.link ) {
                #>selected="selected"<# }
            #>>
                <?php esc_attr_e('Attachment Page', 'sswmd'); ?>
            </option>
            <option value="file" <# if ( 'file' == wp.media.galleryDefaults.link ) { #>selected="selected"<# } #>>
                <?php esc_attr_e('Media File', 'sswmd'); ?>
            </option>
            <option value="none" <# if ( 'none' == wp.media.galleryDefaults.link ) { #>selected="selected"<# } #>>
                <?php esc_attr_e('None', 'sswmd'); ?>
            </option>
        </select>
    </label>

    <label class="setting">
        <span><?php _e('Columns'); ?></span>
        <select class="columns" name="columns"
            data-setting="columns">
            <?php for ( $i = 1; $i <= 9; $i++ ) : ?>
                <option value="<?php echo esc_attr( $i ); ?>" <#
                    if ( <?php echo $i ?> == wp.media.galleryDefaults.columns ) { #>selected="selected"<# }
                #>>
                    <?php echo esc_html( $i ); ?>
                </option>
            <?php endfor; ?>
        </select>
    </label>

    <label class="setting">
        <span><?php _e( 'Random Order', 'sswmd' ); ?></span>
        <input type="checkbox" data-setting="_orderbyRandom" <?php 
            checked(1, $this->currentOptions['galleries']['toggle_random']); ?> />
    </label>

    <label class="setting size">
        <span><?php _e( 'Size', 'sswmd' ); ?></span>
        <select class="size" name="size"
            data-setting="size"
            <# if ( data.userSettings ) { #>
                data-user-setting="imgsize"
            <# } #>
            >
            <?php
            // This filter is documented in wp-admin/includes/media.php
            $size_names = apply_filters( 'image_size_names_choose', array(
                'thumbnail' => __('Thumbnail', 'sswmd'),
                'medium' => __('Medium', 'sswmd'),
                'large' => __('Large', 'sswmd'),
                'full' => __('Full Size', 'sswmd'),
            ));

            foreach ( $size_names as $size => $label ) : ?>
                <option value="<?php echo esc_attr( $size ); ?>" <?php 
                    selected(esc_attr($size), $this->currentOptions['galleries']['thumbnail_size']); ?>>
                    <?php echo esc_html( $label ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <p>
        <small><?php _e('Defaults for Gallery Settings can be changed in'); ?> 
            <a href="<?php echo admin_url('options-media.php'); ?>">
                <?php _e('<em>Settings &gt; Media</em></a> under &quot;Adding Galleries&quot;.', 'sswmd'); ?>
        </small>
    </p>

</script>

<script type="text/html" id="tmpl-attachment-display-settings">
    <h2><?php _e( 'Attachment Display Settings' ); ?></h2>

    <# if ( 'image' === data.type ) { #>
        <label class="setting">
            <span><?php _e('Alignment'); ?></span>
            <select class="alignment"
                data-setting="align"
                <# if ( data.userSettings ) { #>
                    data-user-setting="align"
                <# } #>>

                <option value="left" <?php selected(
                    'left', $this->currentOptions['inserting']['attachment_display_alignment']); 
                    ?>>
                    <?php esc_attr_e('Left'); ?>
                </option>
                <option value="center" <?php selected(
                    'center', $this->currentOptions['inserting']['attachment_display_alignment']); 
                    ?>>
                    <?php esc_attr_e('Center'); ?>
                </option>
                <option value="right" <?php selected(
                    'right', $this->currentOptions['inserting']['attachment_display_alignment']); 
                    ?>>
                    <?php esc_attr_e('Right'); ?>
                </option>
                <option value="none" <?php selected(
                    'none', $this->currentOptions['inserting']['attachment_display_alignment']); 
                    ?>>
                    <?php esc_attr_e('None'); ?>
                </option>
            </select>
        </label>
    <# } #>

    <div class="setting">
        <label>
            <# if ( data.model.canEmbed ) { #>
                <span><?php _e('Embed or Link'); ?></span>
            <# } else { #>
                <span><?php _e('Link To'); ?></span>
            <# } #>

            <select class="link-to" data-setting="link"
            <# if ( data.userSettings && ! data.model.canEmbed ) { #>
                data-user-setting="urlbutton"
            <# } #>>

            <# if ( data.model.canEmbed ) { #>
                <option value="embed" <?php selected(
                    'embed', $this->currentOptions['inserting']['attachment_display_link_to_embeddable']); 
                    ?>>
                    <?php esc_attr_e('Embed Media Player'); ?>
                </option>
                <option value="file" <?php selected(
                    'file', $this->currentOptions['inserting']['attachment_display_link_to_embeddable']); 
                    ?>>
                    <?php esc_attr_e('Link to Media File'); ?>
                </option>
                <option value="post" <?php selected(
                    'post', $this->currentOptions['inserting']['attachment_display_link_to_embeddable']); 
                    ?>>
                    <?php esc_attr_e('Link to Attachment Page'); ?>
                </option>
            <# } else { #>
                <option value="none" <?php selected(
                    'none', $this->currentOptions['inserting']['attachment_display_link_to']); 
                    ?>>
                    <?php esc_attr_e('None'); ?>
                </option>
                <option value="file" <?php selected(
                    'file', $this->currentOptions['inserting']['attachment_display_link_to']); 
                    ?>>
                    <?php esc_attr_e('Media File'); ?>
                </option>
                <option value="post" <?php selected(
                    'post', $this->currentOptions['inserting']['attachment_display_link_to']); 
                    ?>>
                    <?php esc_attr_e('Attachment Page'); ?>
                </option>
            <# } #>
            <# if ( 'image' === data.type ) { #>
                <option value="custom" <?php selected(
                    'custom', $this->currentOptions['inserting']['attachment_display_link_to']); 
                    ?>>
                    <?php esc_attr_e('Custom URL'); ?>
                </option>
            <# } #>
            </select>
        </label>
        <input type="text" class="link-to-custom" data-setting="linkUrl" />
    </div>

    <# if ( 'undefined' !== typeof data.sizes ) { #>
        <label class="setting">
            <span><?php _e('Size'); ?></span>
            <select class="size" name="size" data-setting="size"
            <# if ( data.userSettings ) { #>
                data-user-setting="imgsize"
            <# } #>>
            <?php
            /** This filter is documented in wp-admin/includes/media.php */
            $sizes = apply_filters( 'image_size_names_choose', array(
                'thumbnail' => __('Thumbnail'),
                'medium'    => __('Medium'),
                'large'     => __('Large'),
                'full'      => __('Full Size'),
            ) );

            foreach ($sizes as $value => $name) : ?>
            <#
            var size = data.sizes['<?php echo esc_js($value); ?>'];
            if ( size ) { #>
                <option value="<?php echo esc_attr($value); ?>" <?php selected(
                    $value, $this->currentOptions['inserting']['attachment_display_size']); ?>>
                        <?php echo esc_html($name); ?> &ndash; {{ size.width }} &times; {{ size.height }}
                    </option>
                <# } #>
            <?php endforeach; ?>
            </select>
        </label>
    <# } #>
    <p>
        <small><?php _e('Defaults for Attachment Display Settings can be changed in'); ?> 
            <a href="<?php echo admin_url('options-media.php'); ?>">
                <?php _e('<em>Settings &gt; Media</em></a> under &quot;Inserting Media&quot;.', 'sswmd'); ?>
        </small>
    </p>
</script>
