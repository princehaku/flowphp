<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>一起宅 - 爱就宅一起</title>
    <meta name="description" content="爱就一起宅，宅到春暖花开。最新最全的宅游戏，宅动漫，宅技术，宅漫画，宅电影，宅美剧的聚合地，推荐只属于你的宅元素。"/>
    <meta name="author" content="princehaku"/>
    <!--[if lt IE 9]>
    <script src="<?php echo Flow::$cfg['base_url']; ?>/asserts/js/html5.js"></script>
    <![endif]-->
    <link href="<?php echo Flow::$cfg['base_url']; ?>/asserts/css/bootstrap.min.css" rel="stylesheet"/>
    <!--[if lt IE 7]>
    <link href="<?php echo Flow::$cfg['base_url']; ?>/asserts/css/bootstrap-ie6.min.css" rel="stylesheet"/>
    <![endif]-->
    <link href="<?php echo Flow::$cfg['base_url']; ?>/asserts/css/bootstrap-responsive.css" rel="stylesheet"/>
    <link href="<?php echo Flow::$cfg['base_url']; ?>/asserts/css/bootswatch.css" rel="stylesheet"/>
</head>

<body data-spy="scroll" data-target=".smartnav">
<!-- Navbar -->
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" href="/">一起宅</a>

            <div class="nav-collapse collapse smartnav" id="main-menu">
                <ul class="nav" id="main-menu-left">
                    <li><a href="#comic">Comic</a></li>
                    <li><a href="#movie">Movie</a></li>
                    <li><a href="#game">Game</a></li>
                    <li><a href="#tech">Technolegy</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="maincontainer container">
    <div id="comic">
        漫画
        <ul class="thumbnails">
            <?php foreach ($entry as $i0=>$r){ ?>

                <li class="span3">
                    <div class="thumbnail">
                        <img src="http://i-7.vcimg.com/crop/e2454ce66185b45eac7b2c9f3a32370071972(600x)/thumb.jpg"/>

                        <div class="caption">
                            <p class="title">问题儿童都来自异世界</p>

                            <div class="intro">
                                对世界已经厌烦的逆回十六夜收到了一封邀请函。当他看清信中内容写著：
                                「望你舍弃一切，前来『箱庭』」的瞬间──他来到了完美无缺的异世界！
                                眼前是带著猫的沉默少女与态度高傲的大小姐，还有召唤他们的罪魁祸首──
                                黑兔。当黑兔正在说明箱庭世界的规则时，十六夜却突然表示：「来打倒魔王吧！」
                                ?黑兔没有拜托你做那种事情呀!三个超级问题儿和黑兔的明天将会前往何方？
                            </div>
                            <a class="btn btn-primary" href="#">Action</a> <a class="btn" href="#">Action</a>
                        </div>
                    </div>
                </li>

            <?php } ?>

        </ul>
    </div>
    <div id="movie">
        电影
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
    </div>
    <div id="game">
        电影
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
    </div>
    <div id="tech">
        电影
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
    </div>

    <!-- Footer-->
    <hr/>
    <div id="footer">
        <p class="pull-right"><a href="#top">返回顶部</a></p>

        <div class="links">
            欢迎使用
        </div>
    </div>

</div>
<!-- /container -->


<script src="<?php echo Flow::$cfg['base_url']; ?>/asserts/js/jquery.min.js"></script>
<script src="<?php echo Flow::$cfg['base_url']; ?>/asserts/js/jquery.masonry.min.js"></script>
<script src="<?php echo Flow::$cfg['base_url']; ?>/asserts/js/jquery.smooth-scroll.min.js"></script>
<script src="<?php echo Flow::$cfg['base_url']; ?>/asserts/js/bootstrap.min.js"></script>
<script src="<?php echo Flow::$cfg['base_url']; ?>/asserts/js/bootswatch.js"></script>


</body>
</html>
