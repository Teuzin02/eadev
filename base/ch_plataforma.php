<?php
    if(isset($_GET['page'] )){
        switch($_GET['page']){
            case 'play_curso':
                include "play-content/play_cur.php";           
                break;
            case 'play_video':
                include "play-content/play_aula.php";           
                break;
            case 'rel_certificado':
            include "relatorios/cert.php";           
                break;
            case 'validacao_certificado':
                include "validacao_cert.php";
                break;
        }
    } 
    //Se não houver valor no page, ele inclui a tela inicial.
    else {
        include "dashboard.php";
    }
?>