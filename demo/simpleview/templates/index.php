<html>
<head>
    <meta charset="utf-8"/>
    <meta name="author" content="princehaku"/>
</head>

<body>

<if con="$s==1">
    asdjasd
    <else/>
    asd
</if>
<if con="$s < time()">
    比时间小
    <elseif con="time()==time()"/>
    和时间一样
</if>
<list from="$arr" key="$ee"  val="$e">
    {{$ee}} => {{$e}}
</list>
<list from="$arrarr" val="$e">
    <li>转换出来的时间: {{strtotime($e.c)}}</li>
</list>
<list from="$arrdeep" val="$e">
    <list from="$e.c" val="$e2_val">
        <if con="$e2_val == 'deep_waA'">
            有一个deep_waA
        </if>
        {{$e2_val}}
    </list>
</list>
</body>
</html>