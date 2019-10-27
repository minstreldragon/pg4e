<?php

require_once "../config.php";
require_once "sql_util.php";

use \Tsugi\Core\LTIX;
use \Tsugi\Util\LTI;
use \Tsugi\Util\PDOX;
use \Tsugi\Util\U;
use \Tsugi\Util\Mersenne_Twister;

$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;
$unique = getUnique($LAUNCH);

$dbname = "pg4e_".$unique;
if ( $LAUNCH->user->instructor && U::get($_GET, 'dbname') ) {
    $dbname = U::get($_GET, 'dbname');
}

$retval = pg4e_request($dbname);
$info = false;
if ( is_object($retval) ) {
  $info = pg4e_extract_info($retval);
}

$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
?>
<h1>Postgres Info</h1>
<?php if ( $LAUNCH->user->instructor ) { ?>
<form>
Value to check:
<input type="text" size="80" name="dbname" value="<?= htmlentities($dbname) ?>"><br/>
<input type="submit">
</form>
<?php } ?>
<p>
<?php
if ( is_int($retval) && $retval == 404 ) {
    echo("<p>Environment ".htmlentities($dbname)." not found.</p>\n");
} else if ( is_string($retval) ) {
    echo("<p>Error retrieving environment: ".htmlentities($dbname)."<br/>".htmlentities($retval)."</p>\n");
} else if ( is_object($info) ) {
    echo("<p>Details for ".htmlentities($dbname).":</p>\n");
    echo("<pre>\n");
    echo("Server: ".$info->ip."\n");
    echo("User: ".$info->user."\n");
    echo("Password: ");
    echo('<span id="pass" style="display:none">'.htmlentities($info->password).'</span> (<a href="#" onclick="$(\'#pass\').toggle();return false;">hide/show</a>)'."\n");
    echo("psql -h ".htmlentities($info->ip)." -U ".htmlentities($info->user)."\n");
    echo("</pre>\n");
}
if (  $LAUNCH->user->instructor ) {
    echo("<hr/>\n<pre>\n");
    echo("Returned data:\n");
    var_dump($retval);
    echo("</pre>\n");
}
$OUTPUT->footerStart();
$OUTPUT->footerEnd();
