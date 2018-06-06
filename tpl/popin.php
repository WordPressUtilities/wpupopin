<div class="wpupopin__wrapper">
    <a href="#" <?php echo $this->settings_values['close_overlay'] ? ' rel="button" title="' . __('Close popin', 'wpupopin') . '"' : ''; ?> class="wpupopin__overlay"><span></span></a>
    <div class="wpupopin">
        <div class="wpupopin__inner">
            <?php if (!$this->settings_values['close_button_hidden']): ?>
            <a rel="button" title="<?php echo __('Close popin', 'wpupopin'); ?>" href="#" class="wpupopin__close"><span>&times;</span></a>
            <?php endif;?>
            <div class="wpupopin__content">
                <?php echo apply_filters('wpupopin_the_content', $this->settings_values['content_text']); ?>
            </div>
            <?php if (!$this->settings_values['button_hidden']): ?>
            <div class="wpupopin__button-wrapper">
                <a rel="button" title="<?php echo esc_attr($this->settings_values['button_text']); ?>" href="#" class="wpupopin__button"><span><?php echo $this->settings_values['button_text']; ?></span></a>
            </div>
            <?php endif;?>
        </div>
    </div>
</div>
