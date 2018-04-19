<?php
$dspec = array(
    0 => array("pipe","r"),
    1 => array("pipe","w"),
    2 => array("file","tmp.txt",'w')
);

$pwd = NULL;
$env = NULL; #array();
$str = "wildan\n";

$proc = proc_open('python test.py',$dspec,$pipes,$pwd,$env);
if(is_resource($proc)) {
    fwrite($pipes[0],$str);
    fwrite($pipes[0],"line2\n");
    fclose($pipes[0]);

    $sout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $aout = explode("\n",$sout);
    foreach ($aout as $line) {
        echo "$line<br>";
    }

    $pstat = proc_close($proc);
    echo "return $pstat";
}