<script>
window.fbAsyncInit = function() {
    FB.init({ appId: '<?= FACEBOOK_APP_ID ?>', xfbml: true, version: 'v2.5', status: false });
    $("#login-button-unloaded").hide();
    $("#login-button-loaded").show();

    /* "play_quiz" defined in post.php */
    if(typeof play_quiz != 'undefined') {
        FB.Event.subscribe('comment.create', FacebookCommentCreated);
    }
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/<?= str_replace('-', '_', $__LANGUAGE_CODE_CURRENT) ?>/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>

<div id="header-container">
    <div id="header-inner">
        <div id="mobile-list">&#9776;</div>
        <a id="logo-container" href="<?= $__SITE_URL ?>">
            <img src="<?= LOCATION_SITE . 'img/' . LOGO_NAME . '?' . LOGO_CACHE ?>" />
        </a><!--
     --><div id="tabs-container-whole">
            <div id="tabs-container">
              <?php
                $html = '';

                foreach($__LANGUAGE_SETTINGS['categories'] as $category_id => $category_contents) {
                    if($__LANGUAGE_SETTINGS['categories'][$category_id]['name'] == '')
                        continue;

                    $html .= '<div class="tab-container">';
                        if(PRETTY_URLS == 0)
                            $posts_categories_url = LOCATION_SITE . 'posts.php?' . http_build_query(array_merge($__URL_PARAMETERS, ['cat_name' => str_replace(' ', '-', strtolower($__LANGUAGE_SETTINGS['categories'][$category_id]['name'])), 'cat_id' => $category_id]));
                        else
                            $posts_categories_url = LOCATION_SITE . implode('/', array_merge( $__URL_PARAMETERS, ['posts', 'cat', $category_id, strtolower($__LANGUAGE_SETTINGS['categories'][$category_id]['name']) ] ));
    
                        $html .= '<a class="menu-category" href="' . $posts_categories_url . '">' . ($__LANGUAGE_SETTINGS['categories'][$category_id]['icon'] == '' ? '' : '<i class="fa fa-' . $__LANGUAGE_SETTINGS['categories'][$category_id]['icon'] . '"></i>') . $__LANGUAGE_SETTINGS['categories'][$category_id]['name'] . '</a>';
                        $html .= '<div class="menu-sub-categories">';
                        for($i=0; $i<sizeof($__LANGUAGE_SETTINGS['categories'][$category_id]['tags']); $i++) {
                            if(PRETTY_URLS == 0)
                                $posts_tags_url = LOCATION_SITE . 'posts.php?' . http_build_query(array_merge($__URL_PARAMETERS, ['tag_name' => str_replace(' ', '-', strtolower($__LANGUAGE_SETTINGS['categories'][$category_id]['tags'][$i]['name'])), 'tag_id' => $__LANGUAGE_SETTINGS['categories'][$category_id]['tags'][$i]['id'] ]));
                            else
                                $posts_tags_url = LOCATION_SITE . implode('/', array_merge( $__URL_PARAMETERS, ['posts', 'tag', $__LANGUAGE_SETTINGS['categories'][$category_id]['tags'][$i]['id'], str_replace(' ', '-', strtolower($__LANGUAGE_SETTINGS['categories'][$category_id]['tags'][$i]['name'])) ] ));
        
                            $html .= '<a href="' . $posts_tags_url . '" class="menu-sub-category sub-header-button">' . $__LANGUAGE_SETTINGS['categories'][$category_id]['tags'][$i]['name'] . '</a>';
                        }
                        $html .= '</div>';
                    $html .= '</div>';
                }

                echo $html;
                ?>
            </div><!--
         --><div id="user-tabs-container">
                <a id="login-button" data-in-progress="0" style="<?= (isset($_SESSION['user']) ? 'display:none' : '') ?>"><span id="login-button-unloaded"><i class="fa fa-spin fa-spinner"></i></span><span id="login-button-progress"><i class="fa fa-spin fa-spinner"></i><?= $__LANGUAGE_STRINGS['user_menu']['LOGIN'] ?></span><span id="login-button-loaded"><i class="fa fa-facebook-official"></i><?= $__LANGUAGE_STRINGS['user_menu']['LOGIN'] ?></span></a>
                <div id="loggedin-user-info" style="<?= (isset($_SESSION['user']) ? '' : 'display:none') ?>"><img src="<?= (isset($_SESSION['user']) ? $_SESSION['user']['user_picture_url'] : '') ?>" /><span><?= (isset($_SESSION['user']) ? $_SESSION['user']['user_full_name'] : '') ?></span></div>
                <div id="loggedin-user-menu">
                    <a href="<?= LOCATION_SITE . str_replace('URL_NAME', 'user-quiz', $__NO_PARAMETER_URL) ?>"><?= $__LANGUAGE_STRINGS['user_menu']['CREATE_POST'] ?></a>
                    <a href="<?= LOCATION_SITE . str_replace('URL_NAME', 'user-post-list', $__NO_PARAMETER_URL) ?>"><?= $__LANGUAGE_STRINGS['user_menu']['POSTS'] ?></a>
                    <a href="<?= LOCATION_SITE . str_replace('URL_NAME', 'user-credits', $__NO_PARAMETER_URL) ?>" id="loggedin-user-credits-menu" style="<?= isset($_SESSION['user']) ? (($_SESSION['user']['user_premium'] == 1 && ACTIVATE_PREMIUM == 1) ? '' : 'display:none') : '' ?>"><?= $__LANGUAGE_STRINGS['user_menu']['CREDITS'] ?></a>
                    <a href="<?= LOCATION_SITE . str_replace('URL_NAME', 'user-profile', $__NO_PARAMETER_URL) ?>"><?= $__LANGUAGE_STRINGS['user_menu']['PROFILE'] ?></a>
                    <a id="logout-button" data-in-progress="0"><?= $__LANGUAGE_STRINGS['user_menu']['LOGOUT'] ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

var TIMEOUT;

(function() {
    if($("#header-container").width() < 800) {
        $("#header-container").attr('data-mobile', 1);
        /* 16 is "header-container" padding, 1 is for box shadow */
        $("#tabs-container-whole").css('top', $("#header-container").height() + 16 + 1);
    }
    else {
        $("#header-container").attr('data-mobile', 0);
        $("#tabs-container-whole").css('min-height', '0');
    }
})();

$("#mobile-list").on('click', function() {
    if($("#tabs-container-whole").is(":visible"))
        $("#tabs-container-whole").hide();
    else
        $("#tabs-container-whole").css('min-height', ($(document).height() - $("#header-container").outerHeight()) + 'px').show();
});

$(".menu-category").on('click', function(e) {
    if($("#header-container").attr('data-mobile') == 1) {
        if(!$(this).next().is(":visible")) {
            $(".menu-sub-categories").hide();
            $("#loggedin-user-menu").hide();
            $(this).next().show();
            return false;
        }
    }
});

$(".menu-category").on('mouseenter', function() {
    if($("#header-container").attr('data-mobile') == 1)
        return;

    $(this).addClass('menu-category-hover');
    $(this).next().css('min-width', $(this).outerWidth() + 'px').slideDown(300);
});

$(".menu-category").on('mouseleave', function() {
    if($("#header-container").attr('data-mobile') == 1)
        return;

    var that = this;
    TIMEOUT = setTimeout(function() {
        $(that).removeClass('menu-category-hover');
        $(that).next().hide();
    }, 50);
});

$(".menu-sub-categories").on('mouseenter', function() {
    if($("#header-container").attr('data-mobile') == 1)
        return;

    $(this).prev().addClass('menu-category-hover');
    clearTimeout(TIMEOUT);
});

$(".menu-sub-categories").on('mouseleave', function() {
    if($("#header-container").attr('data-mobile') == 1)
        return;

    $(this).prev().removeClass('menu-category-hover');
    $(this).hide();
});

$(window).on('resize', function() {
    if($("#header-container").width() < 800) {
        $("#header-container").attr('data-mobile', 1);
        $(".menu-sub-categories").hide();
        $("#tabs-container-whole").css('min-height', $(document).height() + 'px').hide();
    }
    else { 
        $("#header-container").attr('data-mobile', 0);
        $("#tabs-container-whole").css({ 'display': 'inline-block', 'min-height' : 0 });
    }
});

$("#login-button").on('click', function() {
    if($("#login-button").attr('data-in-progress') == 1)
        return;

    $("#login-button").attr('data-in-progress', 1);
    $("#login-button-progress").show();
    $("#login-button-loaded").hide();
    FB.login(function(response) {
        FacebookLogin(response);
    }, {scope: 'public_profile,email'});
});

function FacebookLogin(response) {
    if(response.authResponse) {
        $.ajax({
            type: 'POST',
            url: LOCATION_SITE + 'ajax/login.php',
            cache: false,
            data: { access_token: response.authResponse.accessToken },
            dataType: 'JSON',
            success: function(login_response) { 
                if($("#quiz-container-without-login-container").is(":visible")) {
                    document.location.reload();
                    return;
                }

                $("#loggedin-user-info img").attr('src', login_response.user_picture_url);
                $("#loggedin-user-info span").text(login_response.user_full_name);
                
                if(login_response.user_premium == 0)
                    $("#loggedin-user-credits-menu").hide();
                else if(login_response.user_premium == 1)
                    $("#loggedin-user-credits-menu").show();
                
                $("#loggedin-user-info").show();
                $("#login-button").attr('data-in-progress', 0).hide();
                $("#login-button-progress").hide();
                $("#login-button-loaded").show();
            },
            error: function(error_response) {
                $("#login-button").attr('data-in-progress', 0);
                $("#login-button-progress").hide();
                $("#login-button-loaded").show();
            }
        });
    }
    else {
        $("#login-button").attr('data-in-progress', 0);
        $("#login-button-progress").hide();
        $("#login-button-loaded").show();
    }
}

$("#logout-button").on('click', function() {
    $.ajax({
        type: 'POST',
        url: LOCATION_SITE + 'ajax/logout.php',
        cache: false,
        dataType: 'JSON',
        success: function(logout_response) { 
            if($("#main-container").attr('data-reload-on-logout') == 1) {
                document.location.reload();
                return;
            }

            $("#loggedin-user-info").hide();
            $("#loggedin-user-menu").hide();
            $("#login-button").show();
        },
        error: function(error_response) {
            
        }
    });
});

$("#loggedin-user-info").on('click', function() {
    $(this).next().show();
});

$("#loggedin-user-info").on('mouseenter', function() {
    if($("#header-container").attr('data-mobile') == 1)
        return;

    $("#loggedin-user-menu").show();
});

$("#loggedin-user-info").on('mouseleave', function() {
    if($("#header-container").attr('data-mobile') == 1)
        return;

    var that = this;
    TIMEOUT = setTimeout(function() {
        $("#loggedin-user-menu").hide();
    }, 100);
});

$("#loggedin-user-menu").on('mouseenter', function() {
    if($("#header-container").attr('data-mobile') == 1)
        return;

    clearTimeout(TIMEOUT);
});

$("#loggedin-user-menu").on('mouseleave', function() {
    if($("#header-container").attr('data-mobile') == 1)
        return;

    $(this).hide();
});

</script>