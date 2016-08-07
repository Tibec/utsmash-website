<?php

if ( ! function_exists('tournament_contain_event')) {

    function tournament_contain_event($tdata, $eid)
    {

        foreach($eid as $e)
        {
            $find=false;
            foreach($tdata['events'] as $t)
            {
                if($t['id'] == $e) {
                    $find = true;
                    break;
                }
            }
            if(!$find)
                return false;
        }

        return true;
    }
}
if ( ! function_exists('tournament_contain_bring')) {

    function tournament_contain_bring($tdata, $bid)
    {

        if(!empty($bid))
        foreach($bid as $e)
        {
            $find=false;
            foreach($tdata['bringable'] as $t)
            {
                if($t['id'] == $e) {
                    $find = true;
                    break;
                }
            }
            if(!$find)
                return false;
        }

        return true;
    }
}

if ( ! function_exists('parse_reg')) {

    function parse_reg($rdata)
    {
        $regs = array();
        $last_t = "";
        $last_e = "";
        foreach($rdata as $entry)
        {
            if($entry->tname == $last_t and $last_e == $entry->ename)
            { // add bring
                $id = count($regs);
                $regs[$id-1]['brings'][] = array (
                    'id' => $entry->bringId,
                    'name' => $entry->bname
                );
            }
            else if($entry->tname == $last_t) // Add Event
            {
                $last_e = $entry->ename;
                $id = count($regs);
                $regs[$id-1]['events'][] = array (
                    'id' => $entry->eventId,
                    'name' => $entry->ename
                );
                $regs[$id-1]['brings'][] = array (
                    'id' => $entry->bringId,
                    'name' => $entry->bname
                );
            } else { // new event
                $last_t = $entry->tname;
                $last_e = $entry->ename;
                $regs[] = array(
                    'regId' => $entry->regId,
                    'tournamentId' => $entry->tournamentId,
                    'tournamentName' => $entry->tname,
                    'date' => $entry->date,
                    'events' => array(array(
                        'id' => $entry->eventId,
                        'name' => $entry->ename
                    )));
                if($entry->bringId) {
                    $id = count($regs);
                    $regs[$id - 1]['brings'][] = array(
                        'id' => $entry->bringId,
                        'name' => $entry->bname
                    );
                }

            }
        }
        return $regs;
    }
}


if ( ! function_exists('generate_mail')) {

    function generate_mail($pseudo,$mail,$name,$date, $heure, $events, $password="")
    {
        $s = "Bonjour, $pseudo. <br />Votre inscription pour au tournoi $name a bien était enregistrée.<br/><br/>";
        if($password) {
            $s .= "Etant donnée qu'il s'agissait de votre première participation à l'un de nos tournois, ";
            $s .= "nous vous avons automatiquement créé un compte. Les identifiants sont les suivants: <br/><br/>";
            $s .= "-----------------------------------<br/>";
            $s .= " Mail : $mail<br/>";
            $s .= " Mot de passe  : $password <br/>";
            $s .= "-----------------------------------<br/><br/>";
        }

        $s .= "Pour rappel, le tournoi aura lieu le $date à $heure.<br/>";
        $s .= "Vous êtes inscrits aux évenements suivants : $events.<br/><br/>";
        $s .= "Cordialement, <br/> L'équipe UT'Smash";

        return $s;
    }
}