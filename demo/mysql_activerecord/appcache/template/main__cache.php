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
    <link href="<?php echo Flow::$cfg['base_url']; ?>/asserts/css/main.css" rel="stylesheet"/>
</head>

<body data-spy="scroll" data-target=".smartnav">
<!-- Navbar -->
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" href="/">一起宅</a>

            <div class="nav-collapse collapse smartnav" id="main-menu">
                <ul class="nav" id="main-menu-left">
                    <li class='active'><a href="#comic">Comic</a></li>
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
            <?php foreach ($comicnodes as $i0=>$vo){ ?>
                <li class="span3">
                    <div class="thumbnail">
                        <img src="<?php echo $vo["main_pic"]; ?>"/>
                        <div class="caption">
                            <p class="title"><?php echo $vo["title"]; ?></p>
                            <div class="intro"><?php echo $vo["description"]; ?></div>
                            <a class="btn btn-primary" href="#">Action</a> <a class="btn" href="#">Action</a>
                        </div>
                    </div>
                </li>
            <?php } ?>

        </ul>
    </div>
    <div id="movie">
        电影
        <ul class="thumbnails">
            <?php foreach ($movienodes as $i1=>$vo){ ?>
                <li class="span3">
                    <div class="thumbnail">
                        <img src="<?php echo $vo["main_pic"]; ?>"/>
                        <div class="caption">
                            <p class="title"><?php echo $vo["title"]; ?></p>
                            <div class="intro"><?php echo $vo["description"]; ?></div>
                            <a class="btn btn-primary" href="#">Action</a> <a class="btn" href="#">Action</a>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
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
<script src="<?php echo Flow::$cfg['base_url']; ?>/asserts/js/jquery.masonry.min.js"></script>


</body>
</html>
