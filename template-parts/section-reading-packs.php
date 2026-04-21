<?php
/**
 * ==========================================================
 * Reading Packs Section (Member Home)
 * ==========================================================
 */

$languages = get_field('reading_pack_languages', 'option');

if (empty($languages)) {
    return;
}
?>

<div class="meta-wrap reading-packs-wrap">

    <div class="heading reading-packs">
        <span>Reading Packs</span>
    </div>
    <span></span>
    <span></span>
    <span></span>

    <?php foreach ($languages as $row): ?>

        <?php
        $language_id = $row['reading_pack_language'];

        if (!$language_id) {
            continue;
        }

        // Backend logic — ensure this language actually has a pack
        $stories = l4k_get_reading_pack_for_language($language_id);

        if (empty($stories)) {
            continue;
        }

        // Build the same structure as $metaArr items
        $label     = get_the_title($language_id);
        $img_url   = get_field('flag', $language_id); // adjust if needed
        $permalink = get_permalink($language_id);
        ?>

        <a class="lang-item reading-pack"
           href="<?php echo esc_url($permalink); ?>">

            <?php if ($img_url): ?>
                <img src="<?php echo esc_url($img_url); ?>" />
            <?php endif; ?>

            <h5><?php echo esc_html($label); ?></h5>

        </a>

    <?php endforeach; ?>

</div>
