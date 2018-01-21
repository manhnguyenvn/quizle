<div id="slider-container">
    <div id="slider">
        <div id="slider-left">
            <?= $__LEFT_SIDE_FEATURED_POSTS_COUNT > 1 ? '<div id="slider-prev-post" class="slider-post-navigation"><i class="fa fa-angle-' . ($__LANGUAGE_SETTINGS['language_direction'] == 'rtl' ? 'right' : 'left') . '"></i></div><div id="slider-next-post" class="slider-post-navigation"><i class="fa fa-angle-' . ($__LANGUAGE_SETTINGS['language_direction'] == 'rtl' ? 'left' : 'right') . '"></i></div>' : '' ?>
            <div id="slider-left-contents" style="width:<?= sizeof($__LEFT_SIDE_FEATURED_POSTS) ?>00%">
                <?php
                foreach($__LEFT_SIDE_FEATURED_POSTS as $post_id => $post_info) {
                    echo '<a href="' . $post_info['post_url'] . '" class="slider-left-post"><img src="' . LOCATION_SITE . 'img/QUIZ/quiz/' . $post_info['image'] . '" /><div class="slider-post-title">' . $post_info['title']. '</div></a>';
                }
                ?>
            </div>
        </div><!--
      --><div id="slider-right">
            <div id="slider-right-top" style="background-image:url('<?= LOCATION_SITE . 'img/QUIZ/quiz/m-' . $__RIGHT_TOP_FEATURED_POST[key($__RIGHT_TOP_FEATURED_POST)]['image'] ?>')">
                <?= '<a href="' . $__RIGHT_TOP_FEATURED_POST[key($__RIGHT_TOP_FEATURED_POST)]['post_url'] . '" class="slider-right-post"><div class="slider-post-title">' . $__RIGHT_TOP_FEATURED_POST[key($__RIGHT_TOP_FEATURED_POST)]['title'] . '</div></a>' ?>
            </div>
            <div id="slider-right-bottom" style="background-image:url('<?= LOCATION_SITE . 'img/QUIZ/quiz/m-' . $__RIGHT_BOTTOM_FEATURED_POST[key($__RIGHT_BOTTOM_FEATURED_POST)]['image'] ?>')">
                <?= '<a href="' . $__RIGHT_BOTTOM_FEATURED_POST[key($__RIGHT_BOTTOM_FEATURED_POST)]['post_url'] . '" class="slider-right-post"><div class="slider-post-title">' . $__RIGHT_BOTTOM_FEATURED_POST[key($__RIGHT_BOTTOM_FEATURED_POST)]['title'] . '</div></a>' ?>
            </div>
        </div>
    </div>
</div>
