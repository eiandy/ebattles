<?php
/**
 * Constants.php
 *
 * This file is intended to group all constants to
 * make it easier for the site administrator to tweak
 * the login script.
 *
 */

/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_PREFIX", "ebattles_");

define("TBL_USERS_SHORT",           "user");
define("TBL_EVENTS_SHORT",          TBL_PREFIX."Events");
define("TBL_EVENTMODS_SHORT",       TBL_PREFIX."EventModerators");
define("TBL_TEAMS_SHORT",           TBL_PREFIX."Teams");
define("TBL_MATCHS_SHORT",          TBL_PREFIX."Matchs");
define("TBL_PLAYERS_SHORT",         TBL_PREFIX."Players");
define("TBL_SCORES_SHORT",          TBL_PREFIX."Scores");
define("TBL_CLANS_SHORT",           TBL_PREFIX."Clans");
define("TBL_DIVISIONS_SHORT",       TBL_PREFIX."Divisions");
define("TBL_MEMBERS_SHORT",         TBL_PREFIX."Members");
define("TBL_STATSCATEGORIES_SHORT", TBL_PREFIX."StatsCategories");
define("TBL_GAMES_SHORT",           TBL_PREFIX."Games");
define("TBL_AWARDS_SHORT",          TBL_PREFIX."Awards");
define("TBL_PLAYERS_RESULTS_SHORT", TBL_PREFIX."PlayersResults");

define("TBL_USERS",           MPREFIX."user");
define("TBL_EVENTS",          MPREFIX.TBL_EVENTS_SHORT);
define("TBL_EVENTMODS",       MPREFIX.TBL_EVENTMODS_SHORT);
define("TBL_TEAMS",           MPREFIX.TBL_TEAMS_SHORT);
define("TBL_MATCHS",          MPREFIX.TBL_MATCHS_SHORT);
define("TBL_PLAYERS",         MPREFIX.TBL_PLAYERS_SHORT);
define("TBL_SCORES",          MPREFIX.TBL_SCORES_SHORT);
define("TBL_CLANS",           MPREFIX.TBL_CLANS_SHORT);
define("TBL_DIVISIONS",       MPREFIX.TBL_DIVISIONS_SHORT);
define("TBL_MEMBERS",         MPREFIX.TBL_MEMBERS_SHORT);
define("TBL_STATSCATEGORIES", MPREFIX.TBL_STATSCATEGORIES_SHORT);
define("TBL_GAMES",           MPREFIX.TBL_GAMES_SHORT);
define("TBL_AWARDS",          MPREFIX.TBL_AWARDS_SHORT);
define("TBL_PLAYERS_RESULTS", MPREFIX.TBL_PLAYERS_RESULTS_SHORT);

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "eBattles");
define("EMAIL_FROM_ADDR", "frederic.marchais@gmail.com");
define("EMAIL_PASSWORD", "gmax76");
define("EMAIL_WELCOME", true);

define("ELO_DEFAULT", 1000);
define("ELO_K", 50);
define("ELO_M", 100);
define("TS_Mu0"     , 25);
define("TS_sigma0"  , TS_Mu0/3);
define("TS_beta"    , TS_Mu0/6);
define("TS_epsilon" , 1.0);
define("PointsPerWin_DEFAULT" , 3);
define("PointsPerDraw_DEFAULT" , 1);
define("PointsPerLoss_DEFAULT" , 0);

// Match report userclass
define("eb_UC_MODERATOR", 255);
define("eb_UC_EVENT_OWNER", 254);
define("eb_UC_EVENT_MODERATOR", 253);
define("eb_UC_EVENT_PLAYER", 252);

?>