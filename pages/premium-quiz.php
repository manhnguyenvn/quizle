<?php 
require_once('../settings/settings-2.php');
   
?>
<!DOCTYPE html>
<html>
<head>
<title><?= SITE_NAME . ' | ' . 'Introducing Premium Quizzes' ?></title>
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
<style type="text/css">
body {
    font-family: 'Open Sans';
    font-size: 14px;
    margin: 0;
    background-color: #f8f8f8;
}

#header-outer {
    background-color: #fefefe;
    padding: 16px 0 16px 0;
    box-shadow: 0 0 6px 0 rgba(0, 0, 0, 0.15);
}

#header-inner {
    width: 600px;
    margin: 0 auto;
    position: relative;
}

img {
    display: inline-block;
    vertical-align: middle;
}

#title {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    font-weight: 700;
    font-size: 16px;
    color: #336699;
}

#main-container {
    width: 600px;
    margin: 30px auto 0 auto;
}

p {
    margin: 0 0 20px 0;
    line-height: 1.5;
}

ol {
    margin: 0 0 20px 0;
    padding: 0 0 0 30px;
}

li {
    padding: 0 0 0 10px;
    margin: 0 0 10px 0;
}

</style>
</head>

<body>

<div id="header-outer">
    <div id="header-inner">
        <img src="<?= '../img/' . LOGO_NAME . '?' . LOGO_CACHE ?>" /><span id="title">PREMIUM QUIZZES</span>
    </div>
</div>
<div id="main-container">
    <p>
        <?= SITE_NAME ?> gives users the option of embeding avalable quizzes to their website. You might create a quiz but there can be multiple users embedding your quiz to their websites, in addition to the quiz being available on <?= SITE_NAME ?>.
    </p>
    
    <p>
    But by purchasing credits and making a quiz premium you can make your created quiz as "private". The premium quiz can be embedded only to a single domain of your choice. No user can embed that quiz, nor will the quiz be available on <?= SITE_NAME ?>. It is totally yours.
    </p>
    <p>Every time the quiz is played on your website (where you embedded the quiz), 1 credit is consumed. So you have to make sure that there are enough credits in your account so that the quiz remain premium.</p>

    <p>In addition to the above you should read this :</p>
    <ol>
        <li>The current pricing is set to <?= CREDITS_QUANTITY ?> credits for <?= CREDITS_VALUE ?> <?= TRANSACTION_CURRENCY ?></li>
        <li>You can pay only through Paypal</li>
        <li>The premium quiz can be embedded only to one domain</li>
        <li>Ant time you can deactivate the quiz as premium. The quiz will then become like a normal quiz available on <?= SITE_NAME ?></li>
        <li>A one time fee of <?= PREMIUM_QUIZ_CREDITS ?> credits is consumed when you make a quiz premium for the first time. If you deactivate the quiz as premium and re-activate it, this one time fee will NOT be charged</li>
        <li>One time fee holds for every quiz that you decide to make premium. For example if you make 3 quizzes as premium, one time fee is charged for each of the 3 quizzes</li>
        <li>1 play of a premium quiz on your website will cost 1 credit. By playing a quiz it means to click on the "Play" button. So 100 clicks on the "Play" button on your website will cost 100 credits</li>
        <li>You can purchase additional credits as per your choice</li>
        <li>To test out the premium quiz you can play it on <?= SITE_NAME ?>. Only you will be able to see and play the quiz there. No credits are charged when you play the quiz from <?= SITE_NAME ?></li>
    </ol>

    <p>By making a quiz premium you agree to the above terms & conditions.</p>
</div>

</body>
</html>