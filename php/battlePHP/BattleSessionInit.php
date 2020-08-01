<?php
//session_start();
function InitBattleSessionData(){
    unset($_SESSION["enemyStMst"]);
    unset($_SESSION["enemySt"]);
    unset($_SESSION["partySt"]);
    unset($_SESSION["notesdata"]);
    unset($_SESSION["judgedatas"]);
    unset($_SESSION["pointCnt"]);
    unset($_SESSION["Score"]);
    unset($_SESSION["maxComb"]);
    unset($_SESSION["Comb"]);
}
?>