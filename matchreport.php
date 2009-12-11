<?php
/**
* matchreport.php
*
* This page is for users to edit their account information
* such as their password, email address, etc. Their
* usernames can not be edited. When changing their
* password, they must first confirm their current password.
*
*/
require_once("../../class2.php");
include_once(e_PLUGIN."ebattles/include/main.php");
require_once e_PLUGIN.'ebattles/include/match.php';

/*******************************************************************
********************************************************************/
// Specify if we use WYSIWYG for text areas
global $e_wysiwyg;
$e_wysiwyg = "match_comment";  // set $e_wysiwyg before including HEADERF
require_once(HEADERF);

$text = '';

$text .= '
<script type="text/javascript">
';
$text .= "
<!--
function SwitchSelected(id)
{
var select = document.getElementById('rank'+id);
nbr_ranks = select.length
new_rank_txt = select.options[select.selectedIndex].text

for (k = 1; k <= nbr_ranks; k++)
{
old_rank_found=0
for (j = 1; j <= nbr_ranks; j++)
{
var select = document.getElementById('rank'+j);
rank_txt = select.options[select.selectedIndex].text
if (rank_txt == 'Team #'+k) {old_rank_found=1}
}
if (old_rank_found==0) {old_rank = k}
}

for (j = 1; j <= nbr_ranks; j++)
{
if (j!=id)
{
var select = document.getElementById('rank'+j);
rank_txt = select.options[select.selectedIndex].text
if (rank_txt == new_rank_txt) {select.selectedIndex=old_rank-1}
}
}
}
//-->
";
$text .= '
</script>
';

/* Event Name */
$event_id = $_GET['eventid'];

$q = "SELECT ".TBL_EVENTS.".*"
." FROM ".TBL_EVENTS
." WHERE (".TBL_EVENTS.".eventid = '$event_id')";
$result = $sql->db_Query($q);

$ename = mysql_result($result,0 , TBL_EVENTS.".Name");
$etype = mysql_result($result,0 , TBL_EVENTS.".Type");
$eELO_K = mysql_result($result,0 , TBL_EVENTS.".ELO_K");
$eELO_M = mysql_result($result,0 , TBL_EVENTS.".ELO_M");
$eTS_beta = mysql_result($result,0 , TBL_EVENTS.".TS_beta");
$eTS_epsilon = mysql_result($result,0 , TBL_EVENTS.".TS_epsilon");
$ePointPerWin = mysql_result($result,0 , TBL_EVENTS.".PointsPerWin");
$ePointPerDraw = mysql_result($result,0 , TBL_EVENTS.".PointsPerDraw");
$ePointPerLoss = mysql_result($result,0 , TBL_EVENTS.".PointsPerLoss");
$eAllowDraw = mysql_result($result,0 , TBL_EVENTS.".AllowDraw");
$eAllowScore = mysql_result($result,0 , TBL_EVENTS.".AllowScore");

$q = "SELECT ".TBL_PLAYERS.".*, "
.TBL_USERS.".*"
." FROM ".TBL_PLAYERS.", "
.TBL_USERS
." WHERE (".TBL_PLAYERS.".Event = '$event_id')"
." AND (".TBL_USERS.".user_id = ".TBL_PLAYERS.".User)"
." ORDER BY ".TBL_USERS.".user_name";

$result = $sql->db_Query($q);
$num_rows = mysql_numrows($result);

$players_id[0] = '-- select --';
$players_uid[0] = '-- select --';
$players_name[0] = '-- select --';
for($i=0; $i<$num_rows; $i++){
    $pid  = mysql_result($result,$i, TBL_PLAYERS.".PlayerID");
    $puid  = mysql_result($result,$i, TBL_USERS.".user_id");
    $prank  = mysql_result($result,$i, TBL_PLAYERS.".Rank");
    $pname  = mysql_result($result,$i, TBL_USERS.".user_name");
    $pteam  = mysql_result($result,$i, TBL_PLAYERS.".Team");

    $pclan = '';
    $pclantag = '';
    if ($etype == "Team Ladder")
    {
        $q_2 = "SELECT ".TBL_CLANS.".*, "
        .TBL_DIVISIONS.".*, "
        .TBL_TEAMS.".* "
        ." FROM ".TBL_CLANS.", "
        .TBL_DIVISIONS.", "
        .TBL_TEAMS
        ." WHERE (".TBL_TEAMS.".TeamID = '$pteam')"
        ." AND (".TBL_DIVISIONS.".DivisionID = ".TBL_TEAMS.".Division)"
        ." AND (".TBL_CLANS.".ClanID = ".TBL_DIVISIONS.".Clan)";
        $result_2 = $sql->db_Query($q_2);
        $num_rows_2 = mysql_numrows($result_2);
        if ($num_rows_2 == 1)
        {
            $pclan  = mysql_result($result_2,0, TBL_CLANS.".Name");
            $pclantag  = mysql_result($result_2,0, TBL_CLANS.".Tag") ."_";
        }
    }
    if ($prank==0)
    $prank_txt = "Not ranked";
    else
    $prank_txt = "#$prank";

    $players_id[$i+1] = $pid;
    $players_uid[$i+1] = $puid;
    $players_name[$i+1] = $pclantag.$pname." ($prank_txt)";
}

$text .= '
<div class="spacer">
';

// assuming we saved the above function in "functions.php", let's make sure it's available
require_once e_PLUGIN.'ebattles/matchreport_functions.php';

// has the form been submitted?
if (isset($_POST['submit']))
{
    // the form has been submitted
    // perform data checks.
    $error_str = ''; // initialise $error_str as empty

    $reported_by = $_POST['reported_by'];
    //$text .= "reported by: $reported_by<br />";

    $comments = $tp->toDB($_POST['match_comment']);

    $nbr_players = $_POST['nbr_players'];
    $nbr_teams = $_POST['nbr_teams'];
    for($i=1;$i<=$nbr_players;$i++)
    {
        $pid = $_POST['player'.$i];
        $q =
        "SELECT ".TBL_USERS.".*, "
        .TBL_PLAYERS.".*"
        ." FROM ".TBL_USERS.", "
        .TBL_PLAYERS
        ." WHERE (".TBL_PLAYERS.".PlayerID = '$pid')"
        ."   AND (".TBL_PLAYERS.".User     = ".TBL_USERS.".user_id)";
        $result = $sql->db_Query($q);
        $row = mysql_fetch_array($result);
        $puid = $row['user_id'];
        $pTeam = $row['Team'];
        $pMatchTeam = $_POST['team'.$i];

        if ($pid == $players_name[0])
        $error_str .= '<li>Player #'.$i.' not selected</li>';

        for($j=$i+1;$j<=$nbr_players;$j++)
        {
            //if ($_POST['player'.$i] == $_POST['player'.$j])
            $pjid = $_POST['player'.$j];
            $q =
            "SELECT ".TBL_USERS.".*, "
            .TBL_PLAYERS.".*"
            ." FROM ".TBL_USERS.", "
            .TBL_PLAYERS
            ." WHERE (".TBL_PLAYERS.".PlayerID = '$pjid')"
            ."   AND (".TBL_PLAYERS.".User   = ".TBL_USERS.".user_id)";
            $result = $sql->db_Query($q);
            $row = mysql_fetch_array($result);
            $pjuid = $row['user_id'];
            $pjTeam = $row['Team'];
            $pjMatchTeam = $_POST['team'.$j];

            if ($puid == $pjuid)
            $error_str .= '<li>Player #'.$i.' is the same as Player #'.$j.'</li>';
            if (($pTeam == $pjTeam)&&($pMatchTeam != $pjMatchTeam)&&($pTeam != 0))
            $error_str .= '<li>Player #'.$i.' and Player #'.$j.' are in the same team division</li>';
        }
    }

    for($i=1;$i<=$nbr_teams;$i++)
    {
        if (!isset($_POST['score'.$i])) $_POST['score'.$i] = 0;
        $team_players = 0;
        for($j=1;$j<=$nbr_players;$j++)
        {
            if ($_POST['team'.$j] == 'Team #'.$i)
            $team_players ++;
        }
        if ($team_players == 0)
        $error_str .= '<li>Team #'.$i.' has no player</li>';
        if(!preg_match("/^\d+$/", $_POST['score'.$i]))
        $error_str .= '<li>Score #'.$i.' is not a number: '.$_POST['score'.$i].'</li>';
    }

    // we could do more data checks, but you get the idea.
    // we could also strip any HTML from the variables, convert it to entities, have a maximum character limit on the values, etc etc, but this is just an example.
    // now, have any of these errors happened? We can find out by checking if $error_str is empty

    //$error_str = 'test';

    if (!empty($error_str)) {
        // show form again
        user_form($players_id, $players_name, $event_id, $eAllowDraw, $eAllowScore);
        // errors have occured, halt execution and show form again.
        $text .= '<p style="color:red">There were errors in the information you entered, they are listed below:';
        $text .= '<ul style="color:red">'.$error_str.'</ul></p>';
    }
    else
    {
        //$text .= "OK<br />";
        $nbr_players = $_POST['nbr_players'];

        $actual_rank[1] = 1;
        for($i=1;$i<=$nbr_teams;$i++)
        {
            $text .= 'Rank #'.$i.': '.$_POST['rank'.$i];
            $text .= '<br />';
            // Calculate actual rank based on draws checkboxes
            if ($_POST['draw'.$i] != "")
            $actual_rank[$i] = $actual_rank[$i-1];
            else
            $actual_rank[$i] = $i;
        }

        $text .= '--------------------<br />';

        $text .= 'Comments: '.$tp->toHTML($comments).'<br />';

        // Create Match ------------------------------------------
        $q =
        "INSERT INTO ".TBL_MATCHS."(Event,ReportedBy,TimeReported,Comments)
        VALUES ($event_id,'$reported_by',$time, '$comments')";
        $result = $sql->db_Query($q);

        $last_id = mysql_insert_id();
        $match_id = $last_id;

        // Create Scores ------------------------------------------
        for($i=1;$i<=$nbr_players;$i++)
        {
            $pid = $_POST['player'.$i];
            $pteam = str_replace("Team #","",$_POST['team'.$i]);

            $q =
            "SELECT ".TBL_USERS.".*, "
            .TBL_PLAYERS.".*"
            ." FROM ".TBL_USERS.", "
            .TBL_PLAYERS
            ." WHERE (".TBL_PLAYERS.".PlayerID = '$pid')"
            ."   AND (".TBL_PLAYERS.".User     = ".TBL_USERS.".user_id)";
            $result = $sql->db_Query($q);
            $row = mysql_fetch_array($result);
            $pname = $row['user_name'];
            $puid = $row['user_id'];

            for($j=1;$j<=$nbr_teams;$j++)
            {
                if( $_POST['rank'.$j] == "Team #".$pteam)
                $prank = $actual_rank[$j];
            }

            for($j=1;$j<=$nbr_teams;$j++)
            {
                if( $_POST['rank'.$j] == "Team #".$pteam)
                $pscore = $_POST['score'.$j];
            }

            $q =
            "INSERT INTO ".TBL_SCORES."(MatchID,Player,Player_MatchTeam,Player_Score,Player_Rank)
            VALUES ($match_id,$pid,$pteam,$pscore,$prank)
            ";
            $result = $sql->db_Query($q);

            $text .= 'Player #'.$i.': '.$pname.' (user id:'.$puid.') (player id:'.$pid.')';
            $text .= ' in team '.$pteam;
            $text .= '<br />';
        }
        $text .= '--------------------<br />';

        // Update scores stats
        match_scores_update($match_id, FALSE);

        $q = "UPDATE ".TBL_EVENTS." SET IsChanged = 1 WHERE (EventID = '$event_id')";
        $result = $sql->db_Query($q);

        $text .= "<p>";
        $text .= "<br />Back to [<a href=\"".e_PLUGIN."ebattles/eventinfo.php?eventid=$event_id\">Event</a>]<br />";
        $text .= "</p>";

        header("Location: matchinfo.php?matchid=$match_id");
        exit();
    }
    // if we get here, all data checks were okay, process information as you wish.
} else {

    if (!isset($_POST['matchreport']))
    {
        $text .= "<p>You are not authorized to report a match.</p>";
        $text .= "<p>Back to [<a href=\"".e_PLUGIN."ebattles/eventinfo.php?eventid=$event_id\">Event</a>]</p>";
    }
    else if (!check_class(e_UC_MEMBER))
    {
        $text .= "<p>You are not logged in.</p>";
        $text .= "<p>Back to [<a href=\"".e_PLUGIN."ebattles/eventinfo.php?eventid=$event_id\">Event</a>]</p>";
    }
    else
    {
        // the form has not been submitted, let's show it
        user_form($players_id, $players_name, $event_id, $eAllowDraw, $eAllowScore);
    }
}

$text .= '
</div>
';

$ns->tablerender("$ename ($etype) - Match Report", $text);
require_once(FOOTERF);
exit;
?>
