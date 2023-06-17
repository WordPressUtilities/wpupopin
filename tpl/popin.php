<?php

$close_overlay_attr = $this->settings_values['close_overlay'] ? ' rel="button" title="' . __('Close popin', 'wpupopin') . '"' : '';
$button_url = isset($this->settings_values['button_url']) && $this->settings_values['button_url'] ? esc_url($this->settings_values['button_url']) : '#';
$button_attr = $button_url != '#' ? ' target="_blank"' : ' rel="button"';

echo '<div class="wpupopin__wrapper" data-nosnippet>';

/* Overlay */
echo '<a href="#"' . $close_overlay_attr . 'class="wpupopin__overlay"><span></span></a>';

echo '<div class="wpupopin"><div class="wpupopin__inner">';

/* Close button */
if (!$this->settings_values['close_button_hidden']):
    echo '<a rel="button" title="' . __('Close popin', 'wpupopin') . '" href="#" class="wpupopin__close"><span>&times;</span></a>';
endif;

/* Content */
echo '<div class="wpupopin__content">' . apply_filters('wpupopin_the_content', $this->settings_values['content_text']) . '</div>';

/* Button */
if (!$this->settings_values['button_hidden']):
    echo '<div class="wpupopin__button-wrapper">';
    echo '<a ' . $button_attr . ' title="' . esc_attr($this->settings_values['button_text']) . '" href="' . $button_url . '" class="wpupopin__button"><span>' . $this->settings_values['button_text'] . '</span></a>';
    echo '</div>';
endif;

echo '</div></div>';

echo '</div>';
