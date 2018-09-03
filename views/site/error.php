<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>

<div class="comingcontainer">
    <div class="checkbacksoon">
        <p>
            <?php 
                for($i = 0; $i < 3; $i++){ 
                $statusCode = (String)$exception->statusCode;
            ?>
            <span class="go3d"><?php echo $statusCode[$i]; ?></span>
            <?php } ?>
            <span class="go3d">!</span>
        </p>
        
<p class="error">Страница не найдена!<br>
<a href="/">Перейти на главную страницу сайта!</a>
</p>
   
    </div>
</div>
