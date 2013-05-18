<html>

<?php if ($s==1) { ?>
    asdjasd
    <?php } else { ?>
    asd
<?php } ?>
<?php if ($s < time()) { ?>
    比时间小
    <?php } else if (time()==time()) { ?>
    和时间一样
<?php } ?>
<?php foreach ($arr as $ee=>$e){ ?>
    <?php echo $ee; ?> => <?php echo $e; ?>
<?php } ?>
<?php foreach ($arrarr as $i1=>$e){ ?>
    <li>转换出来的时间: <?php echo strtotime($e["c"]); ?></li>
<?php } ?>
<?php foreach ($arrdeep as $i2=>$e){ ?>
    <?php foreach ($e["c"] as $i3=>$e2_val){ ?>
        <?php if ($e2_val == 'deep_waA') { ?>
            有一个deep_waA
        <?php } ?>
        <?php echo $e2_val; ?>
    <?php } ?>
<?php } ?>

</html>