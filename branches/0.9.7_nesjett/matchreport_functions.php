<?php
// function to output form and hold previously entered values.
function user_form($players_id, $players_name, $event_id, $match_id, $allowDraw, $allowForfeit, $allowScore, $userclass) {
	global $sql;
	global $text;
	global $tp;
	global $time;

	/* Event Info */
	$event = new Event($event_id);

	if (e_WYSIWYG)
	{
		$insertjs = "rows='15'";
	}
	else
	{
		require_once(e_HANDLER."ren_help.php");
		$insertjs = "rows='5' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'";
	}
	/*
	//dbg form
	echo "<br>_POST: ";
	print_r($_POST);    // show $_POST
	echo "<br>_GET: ";
	print_r($_GET);     // show $_GET
	*/

	$match_str = '';
	$matchreport_str = EB_MATCHR_L31;

	if(isset($_POST['matchedit']))
	{
		$matchreport_str = EB_MATCHR_L46;

		$text .= '<div>';
		$text .= EB_MATCHR_L45." $match_id<br />";
		$text .= '<img src="'.e_PLUGIN.'ebattles/images/exclamation.png"/>';
		$text .= EB_MATCHR_L47;
		$text .= '</div><br />';
	}
	if(isset($_POST['matchschedule']))
	{
		$matchreport_str = EB_MATCHR_L48;
	}


	if($match_id)
	{
		$match_str = '&amp;matchid='.$match_id;
	}

	// Assign values to POST if not set
	// fm: check might not be correct here
	// should check if isset($_POST['matchedit']) only?
	if ((!isset($_POST['time_reported']))
	||isset($_POST['matchscheduledreport'])) $_POST['time_reported'] = $time;
	$time_reported = $_POST['time_reported'];

	if(!isset($_POST['reported_by'])) $_POST['reported_by'] = USERID;
	$reported_by = $_POST['reported_by'];

	if(isset($_POST['match_comment']))
	{
		$comment = $tp->toDB($_POST['match_comment']);
	} else {
		$comment = '';
	}

	for ($matchMap = 0; $matchMap<min($numMaps, $event->getField('MaxMapsPerMatch')); $matchMap++)
	{
		if (!isset($_POST['map'.$matchMap])) $_POST['map'.$matchMap] = 0;
	}

	// if vars are not set, set them as empty.
	if (!isset($_POST['nbr_players'])) $_POST['nbr_players'] = 2;
	if (!isset($_POST['nbr_teams'])) $_POST['nbr_teams'] = 2;

	// now to output the form HTML.
	$max_nbr_players = count($players_id)-1;

	$nbr_players = $_POST['nbr_players'];
	$nbr_teams = $_POST['nbr_teams'];

	if (isset($_POST['addPlayer']))
	{
		$nbr_players++;
	}
	if (isset($_POST['removePlayer']))
	{
		if ($nbr_players==$nbr_teams)
		{
			$nbr_teams--;
		}
		$nbr_players--;
	}
	$_POST['nbr_players']=$nbr_players;

	for($i=1;$i<=$nbr_players;$i++)
	{
		if (!isset($_POST['player'.$i])) $_POST['player'.$i] = $players_id[0];
		//debug - echo "Player #".$i.": ".$_POST['player'.$i]."<br />";
	}

	if (isset($_POST['addTeam']))
	{
		$nbr_teams++;
	}
	if (isset($_POST['removeTeam']))
	{
		$nbr_teams--;
	}
	$_POST['nbr_teams']=$nbr_teams;
	for($i=1;$i<=$nbr_players;$i++)
	{
		if (!isset($_POST['team'.$i])) $_POST['team'.$i] = 'Team #'.$i;
		if (!isset($_POST['score'.$i])) $_POST['score'.$i] = 0;
		if (!isset($_POST['faction'.$i])) $_POST['faction'.$i] = 0;
	}

	for($i=1;$i<=$nbr_teams;$i++)
	{
		if (!isset($_POST['rank'.$i])) $_POST['rank'.$i] = 'Team #'.$i;
	}

	/////////////////
	/// MAIN FORM ///
	/////////////////
	$text .= '<form id="matchreport" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'?eventid='.$event_id.$match_str.'" method="post">';
	$text .= '<div>';
	// TABLE - Player/Teams Add/Remove
	//----------------------------------
	$text .= EB_MATCHR_L15;
	$text .= '<table id="matchresult_nbrPlayersTeams"><tbody>';
	$text .= '<tr><td><input type="hidden" name="matchreport" value="1"/></td></tr>';
	// Players
	$text .= '<tr><td>'.$nbr_players.'&nbsp;'.EB_MATCHR_L21.'</td>';
	$text .= '<td><input type="hidden" name="nbr_players" value="'.$_POST['nbr_players'].'"/>';
	// Add Player
	if (($nbr_players < $max_nbr_players)
	&&(!isset($_POST['matchscheduledreport'])))
	{
		$text .= '<input class="button" type="submit" value="'.EB_MATCHR_L16.'" name="addPlayer"/></td>';
	}
	else
	{
		$text .= '<input class="button_disabled" type="submit" value="'.EB_MATCHR_L16.'" name="addPlayer" disabled="disabled"/></td>';
	}
	// Remove Player
	if (($nbr_players>2)
	&&(!isset($_POST['matchscheduledreport'])))
	{
		$text .= '<td><input class="button" type="submit" value="'.EB_MATCHR_L17.'" name="removePlayer"/></td>';
	}
	else
	{
		$text .= '<td><input class="button_disabled" type="submit" value="'.EB_MATCHR_L17.'" name="removePlayer" disabled="disabled"/></td>';
	}
	$text .= '</tr>';

	// Teams
	$text .= '<tr><td>'.$nbr_teams.'&nbsp;'.EB_MATCHR_L22.'</td>';
	$text .= '<td><input type="hidden" name="nbr_teams" value="'.$_POST['nbr_teams'].'"/>';
	// Add Team
	if (($nbr_teams<$nbr_players)
	&&(!isset($_POST['matchscheduledreport'])))
	{
		$text .= '<input class="button" type="submit" value="'.EB_MATCHR_L18.'" name="addTeam"/></td>';
	}
	else
	{
		$text .= '<input class="button_disabled" type="submit" value="'.EB_MATCHR_L18.'" name="addTeam" disabled="disabled"/></td>';
	}
	// Remove Team
	if (($nbr_teams>2)
	&&(!isset($_POST['matchscheduledreport'])))
	{
		$text .= '<td><input class="button" type="submit" value="'.EB_MATCHR_L19.'" name="removeTeam"/></td>';
	}
	else
	{
		$text .= '<td><input class="button_disabled" type="submit" value="'.EB_MATCHR_L19.'" name="removeTeam" disabled="disabled"/></td>';
	}
	$text .= '</tr>';
	$text .= '</tbody></table>';

	//$text .= '<p><input class="inspector" type="button" value="Inspect" onclick="junkdrawer.inspectListOrder(\'matchresultlist\')"/></p>';
	$text .= '<br />';

	// TABLE - Players/Teams Selection
	//----------------------------------
	// List of all Factions
	$q_Factions = "SELECT ".TBL_FACTIONS.".*"
	." FROM ".TBL_FACTIONS
	." WHERE (".TBL_FACTIONS.".Game = '".$event->getField('Game')."')";
	$result_Factions = $sql->db_Query($q_Factions);
	$numFactions = mysql_numrows($result_Factions);

	$text .= EB_MATCHR_L20;
	$text .= '<table id="matchresult_selectPlayersTeams"><tbody>';
	$text .= '<tr><td></td><td>'.EB_MATCHR_L38.'</td>';
	$text .= '<td>'.EB_MATCHR_L25.'</td>';
	if (!isset($_POST['matchschedule'])&&($allowScore == TRUE)) $text .= '<td>'.EB_MATCHR_L26.'</td>';
	if (!isset($_POST['matchschedule'])&&($numFactions > 0)) $text .= '<td>'.EB_MATCHR_L41.'</td>';
	$text .= '</tr>';

	$select_disabled_str = (isset($_POST['matchscheduledreport'])) ? 'disabled="disabled"' : '';

	for($i=1;$i<=$nbr_players;$i++)
	{
		$text .= '<tr><td>'.EB_MATCHR_L23.$i.':</td>';

		$text .= '<td><select class="tbox" name="player'.$i.'" '.$select_disabled_str.'>';
		for($j=1;$j <= $max_nbr_players+1;$j++)
		{
			$text .= '<option value="'.$players_id[($j-1)].'"';
			if (strtolower($_POST['player'.$i]) == strtolower($players_id[($j-1)])) $text .= ' selected="selected"';
			$text .= '>'.$players_name[($j-1)].'</option>';
		}
		$text .= '</select></td>';

		$text .= '<td><select class="tbox" name="team'.$i.'" '.$select_disabled_str.'>';
		for($j=1;$j<=$nbr_teams;$j++)
		{
			$text .= '<option value="Team #'.$j.'"';
			if (strtolower($_POST['team'.$i]) == 'team #'.$j) $text .= ' selected="selected"';
			$text .= '>'.EB_MATCHR_L29.$j.'</option>';
		}
		$text .= '</select></td>';
		if (!isset($_POST['matchschedule']))
		{
			if ($allowScore == TRUE)
			{
				$text .= '<td>';
				$text .= '<input class="tbox" type="text" name="score'.$i.'" value="'.$_POST['score'.$i].'"/>';
				$text .= '</td>';
			}
			if ($numFactions > 0)
			{
				$text .= '<td><select class="tbox" name="faction'.$i.'">';
				$text .= '<option value="0"';
				$text .= '>'.EB_MATCHR_L43.'</option>';
				for($faction=1;$faction<=$numFactions;$faction++)
				{
					$fID = mysql_result($result_Factions,$faction - 1 , TBL_FACTIONS.".FactionID");
					$fIcon = mysql_result($result_Factions,$faction - 1, TBL_FACTIONS.".Icon");
					$fName = mysql_result($result_Factions,$faction - 1, TBL_FACTIONS.".Name");
					$text .= '<option value="'.$fID.'"';
					if (strtolower($_POST['faction'.$i]) == $fID) $text .= ' selected="selected"';
					$text .= '>'.$fName.'</option>';
				}
				$text .= '</select></td>';
			}
		}
		$text .= '</tr>';
	}
	$text .= '</tbody></table>';
	$text .= '<br />';

	if(!isset($_POST['matchschedule']))
	{
		// TABLE - Teams Rank Selection
		//----------------------------------
		$text .= EB_MATCHR_L24;
		$text .= '<table id="matchresult_rankTeams"><tbody>';
		$text .= '<tr><td></td><td>'.EB_MATCHR_L25.'</td>';
		if ($allowDraw == TRUE) $text .= '<td>'.EB_MATCHR_L27.'</td>';
		if ($allowForfeit == TRUE) $text .= '<td>'.EB_MATCHR_L57.'</td>';
		$text .= '</tr>';

		for($i=1;$i<=$nbr_teams;$i++)
		{
			$text .= '<tr>';
			$text .= '<td>';
			$text .= EB_MATCHR_L28.$i.':';
			$text .= '</td>';
			$text .= '<td><select class="tbox" name="rank'.$i.'" id="rank'.$i.'" onchange = "SwitchSelected('.$i.')">';
			for($j=1;$j<=$nbr_teams;$j++)
			{
				$text .= '<option value="Team #'.$j.'"';
				if (strtolower($_POST['rank'.$i]) == 'team #'.$j) $text .= ' selected="selected"';
				$text .= '>'.EB_MATCHR_L29.$j.'</option>';
			}
			$text .= '</select></td>';
			if ($allowDraw == TRUE)
			{
				$text .= '<td>';
				if ($i>1)
				{
					$text .= '<input class="tbox" type="checkbox" name="draw'.$i.'" value="1"';
					if (strtolower($_POST['draw'.$i]) != "") $text .= ' checked="checked"';
					$text .= '/>';
				}
				$text .= '</td>';
			}
			if ($allowForfeit == TRUE)
			{
				$text .= '<td>';
				if ($i>1)
				{
					$text .= '<input class="tbox" type="checkbox" name="forfeit'.$i.'" value="1"';
					if (strtolower($_POST['forfeit'.$i]) != "") $text .= ' checked="checked"';
					$text .= '/>';
				}
				$text .= '</td>';
			}
			$text .= '</tr>';
		}
		$text .= '</tbody></table>';
	}

	// Map Selection
	//----------------------------------
	// List of all Maps
	$q_Maps = "SELECT ".TBL_MAPS.".*"
	." FROM ".TBL_MAPS
	." WHERE (".TBL_MAPS.".Game = '".$event->getField('Game')."')";
	$result_Maps = $sql->db_Query($q_Maps);
	$numMaps = mysql_numrows($result_Maps);

	if ($numMaps > 0)
	{
		$text .= EB_MATCHR_L42;
		$text .= '<table id="matchresult_selectMap"><tbody>';

		for ($matchMap = 0; $matchMap<min($numMaps, $event->getField('MaxMapsPerMatch')); $matchMap++)
		{
			$text .= '<tr>';

			$text .= '<td><select class="tbox" name="map'.$matchMap.'">';
			$text .= '<option value="0"';
			$text .= '>'.EB_MATCHR_L43.'</option>';
			for($map=0;$map < $numMaps;$map++)
			{
				$mID = mysql_result($result_Maps,$map , TBL_MAPS.".MapID");
				$mImage = mysql_result($result_Maps,$map , TBL_MAPS.".Image");
				$mName = mysql_result($result_Maps,$map , TBL_MAPS.".Name");
				$mDescrition = mysql_result($result_Maps,$map , TBL_MAPS.".Description");

				$text .= '<option value="'.$mID.'"';
				if (strtolower($_POST['map'.$matchMap]) == $mID) $text .= ' selected="selected"';
				$text .= '>'.$mName.'</option>';
			}
			$text .= '</select></td>';
			$text .= '</tr>';
		}

		$text .= '</tbody></table>';
		$text .= '<br />';
	}

	if(!isset($_POST['matchschedule']))
	{
		// Comments
		//----------------------------------
		$text .= '<br />';
		$text .= '<div style="display:table; margin-left:auto; margin-right:auto;">';
		$text .= EB_MATCHR_L30.'<br />';
		$text .= '<textarea class="tbox" id="match_comment" name="match_comment" style="width:500px" cols="70" '.$insertjs.'>'.$comment.'</textarea>';
		if (!e_WYSIWYG)
		{
			$text .= '<br />'.display_help("helpb","comment");
		}
		$text .= '</div>';
		$text .= '<br />';
	}

	if(isset($_POST['matchschedule']))
	{
		//<!-- Date Selection -->
		$text .= EB_MATCHR_L49;
		$text .= '
		<table>
		<tr>
		<td>
		<table>
		<tr>
		<td>
		<div><input class="tbox timepicker" type="text" name="date_scheduled" id="f_date"  value="'.$date_scheduled.'" readonly="readonly" /></div>
		</td>
		<td>
		<div><input class="button" type="button" value="'.EB_MATCHR_L51.'" onclick="clearDate(this.form);"/></div>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		';
	}

	$text .= '<div style="display:table; margin-left:auto; margin-right:auto;">';
	$text .= '<input type="hidden" name="userclass" value="'.$userclass.'"/>';
	$text .= '<input type="hidden" name="reported_by" value="'.$reported_by.'"/>';
	$text .= '<input type="hidden" name="time_reported" value="'.$time_reported.'"/>';
	if(isset($_POST['matchscheduledreport']))
	{
		$text .= '<input type="hidden" name="matchscheduledreport" value="1"/>';
	}
	if(isset($_POST['matchschedule']))
	{
		$text .= '<input type="hidden" name="matchschedule" value="1"/>';
	}
	$text .= '<input class="button" type="submit" value="'.$matchreport_str.'" name="submit"/>';
	$text .= '</div>';
	$text .= '<br /><br />';
	$text .= '</div>';
	$text .= '</form>';
}

?>