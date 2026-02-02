<div class="rest-api-post-card animate-on-scroll">
    <a href="<?php echo $link; ?>" target="_blank">
        <div class="rest-api-post-image">
            <img src="<?php echo $image_url; ?>" alt="<?php echo $title; ?>">
        </div>
        <div class="rest-api-post-content">
            <p class="rest-api-post-date"><?php echo $date; ?></p>
            <h3><?php echo $title; ?></h3>
            <?php if (!empty($excerpt)): ?>
                <p class="rest-api-post-excerpt"><?php echo esc_html($excerpt); ?></p>
            <?php endif; ?>
        </div>
    </a>
</div>

