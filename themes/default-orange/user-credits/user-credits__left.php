<div id="user-credits-container">
    <?php
    if($__TRANSACTION_IN_PROCESS == 1) {
        if($__PAYMENT_SUCCESS == 1) 
            echo '<div id="payment-success-container">' . $__LANGUAGE_STRINGS['user_credits']['PAYMENT_SUCCESSFUL'] . '</div>';
        else
            echo '<div id="payment-failed-container">' . $__PAYMENT_ERROR_MESSAGE . '</div>';
    }
    ?>
    <div id="user-credits-title-container"><h3 id="user-credits-title"><?= $__LANGUAGE_STRINGS['user_credits']['PAGE_TITLE'] ?></h3></div>
    <div id="credits-container-buttons">
        <button class="theme-active-button credits-tab" id="credits-available-button"><?= $__LANGUAGE_STRINGS['user_credits']['CREDITS_AVAILABLE_BUTTON'] ?></button><!--
     --><button class="theme-passive-button credits-tab" id="credits-purchase-button"><?= $__LANGUAGE_STRINGS['user_credits']['PURCHASE_CREDITS_BUTTON'] ?></button><!--
     --><button class="theme-passive-button credits-tab" id="credits-transactions-button"><?= $__LANGUAGE_STRINGS['user_credits']['TRANSACTIONS_BUTTON'] ?></button>
    </div>
    <div id="credits-available-container"><span><?= $__USER_AVAILABLE_CREDITS ?></span><label><?= $__LANGUAGE_STRINGS['user_quiz']['CREDITS_REMAINING'] ?></label></div>
    <div id="purchase-credits-container">
        <div id="purchase-credits-buy">
            <span><?= CREDITS_QUANTITY . ' ' . $__LANGUAGE_STRINGS['user_credits']['CREDITS_PACK'] ?></span>
            <span id="purchase-credits-x">x</span>
            <span><select id="credits-pack-quantity" data-credits-unit="<?= CREDITS_QUANTITY ?>" data-price-unit="<?= CREDITS_VALUE ?>"><option value="1">1</option><option value="2">2</option><option value="5">5</option><option value="10">10</option><option value="20">20</option></select></span>
            <div id="total-credits-container">
                <label><?= $__LANGUAGE_STRINGS['user_credits']['TOTAL_CREDITS'] ?></label>
                <span id="final-credits-purchased"><?= CREDITS_QUANTITY ?></span>
            </div>
            <div id="total-price-container">
                <label><?= $__LANGUAGE_STRINGS['user_credits']['TOTAL_PRICE'] ?></label>
                <span id="purchased-credits-purchased-price"><?= CREDITS_VALUE ?></span> <span><?= TRANSACTION_CURRENCY ?></span>
            </div>
            <img id="pay-button" src="https://fpdbs.paypal.com/dynamicimageweb?cmd=_dynamic-image&locale=<?= $__LANGUAGE_CODE_CURRENT ?>" /><i id="pay-loader" class="fa fa-spin fa-spinner"></i>
            <div id="token-error"></div>
        </div>
    </div>
    <div id="transactions-container"></div>
</div>