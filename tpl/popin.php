<div class="wpupopin__wrapper">
    <a href="#" class="wpupopin__overlay"><span></span></a>
    <div class="wpupopin">
        <div class="wpupopin__inner">
            <?php if (!$this->settings_values['close_button_hidden']): ?>
            <a href="#" class="wpupopin__close"><span>&times;</span></a>
            <?php endif;?>
            <div class="wpupopin__content">
                <?php echo apply_filters('the_content', $this->settings_values['content_text']); ?>
            </div>
            <?php if (!$this->settings_values['button_hidden']): ?>
            <div class="wpupopin__button-wrapper">
                <a href="#" class="wpupopin__button"><span><?php echo $this->settings_values['button_text']; ?></span></a>
            </div>
            <?php endif;?>
        </div>
    </div>
</div>
