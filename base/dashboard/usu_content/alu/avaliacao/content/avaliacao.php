<?php
    date_default_timezone_set ("America/Sao_Paulo");
        
    $nivel_necessario = 2;
    include "../../../../../testa_nivel.php";
    include "../../../../../config.php";

    $queryCur = mysqli_query($con, "SELECT c.* FROM curso as c where c.id_curso = ".$_GET['curso'].";");
    $infoCur = mysqli_fetch_array($queryCur);
?>
<h5 class='label-av-form-1'> <i class="bi bi-pencil-square"></i> Questionário avaliativo</h5>
<div class="all-exam1">
<button id="btn-back-av"> <i class="bi bi-reply-fill"></i> Voltar </button>
    <div class="all-exam2 ">
        <div class="start_btn"><button>Iniciar a avaliação</button></div>
            <div class="back-av">
                <!-- Info Box -->
                <div class="info_box">
                    <div class="info-title"><span>Condições e regras do questionário avaliativo.</span></div>
                        <div class="info-list">
                            <div class="info">1. Você terá o tempo máximo de <span> 150 segundos</span> (2min30s) por questão.</div>
                            <div class="info">2. A resposta da opção só pode ser marcada uma única vez. </div>
                            <div class="info">3. Você não poderá selecionar outra opção após o término do prazo.</div>
                            <div class="info">4. Você não poderá sair do questionário enquanto ele estiver em andamento.</div>
                            <div class="info">5. Ao clicar em prosseguir, gastará uma tentativa restante das que possui.</div>
                            <div class="info">6. O questionário possui 18 questões, totalizando <span> 45min</span> de avaliação.</div>
                            <div class="info">7. Sua média será calculada de acordo com as respostas corretas, relativo ao grau de dificuldade.</div>
                        </div>
                    <div class="buttons">
                        <button class="quit">Cancelar</button>
                        <button class="restart">Prosseguir</button>
                    </div>
                </div>
                <!-- Quiz Box -->
                <div class="quiz_box">
                    <header>
                        <div class="title">Quetionário - Github</div>
                        <div class="timer">
                            <div class="time_left_txt">Tempo restante (segundos)</div>
                            <div class="timer_sec">150</div>
                        </div>
                        <div class="time_line"></div>
                    </header>
                    <section>
                        <div class="que_text">
                            <!-- Here I've inserted question from JavaScript -->
                        </div>
                        <div class="option_list">
                            <!-- Here I've inserted options from JavaScript -->
                        </div>
                    </section>
                    <!-- footer of Quiz Box -->
                    <footer>
                        <div class="total_que">
                            <!-- Here I've inserted Question Count Number from JavaScript -->
                        </div>
                        <button class="next_btn">Avançar</button>
                    </footer>
                </div>
                <!-- Result Box -->
                <div class="result_box">
                    <div class="icon">
                    <i class="bi bi-clipboard-check-fill"></i>
                    </div>
                    <div class="complete_text">Fim do questionário!</div>
                    <div class="score_text">
                        <!-- Here I've inserted Score Result from JavaScript -->
                    </div>
                    <div class="buttons">
                        <button class="btn btn-primary" id='reach-cert' data-cur='<?php echo $_GET['curso'];?>' data-alu='<?php echo $_GET['alu'];?>' disabled>Obter o certificado <i class="bi bi-award-fill"></i></button>
                        <button class="quit" id='closeCert'>Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Inside this JavaScript file I've inserted Questions and Options only -->
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script>

    let questions = [];
    $.ajax({
            url: '/eadev/selects/questionario/query_quest.php',
            method: 'POST',
            data: {curso: <?php echo $_GET['curso'];?>},
            dataType: 'json'
        }).done(function(dados){
            for (let i = 0; i < dados.length; i++) {
                const forDados = dados[i];

                function changePosition(arr, from, to) {
                    arr.splice(to, 0, arr.splice(from, 1)[0]);
                        return arr;
                };

                let opcoes = [forDados.opc_certa, forDados.opc_errada1, forDados.opc_errada2];
                opcoes = changePosition(opcoes, 0, Math.floor(Math.random() * 3 + 0));
                
                questions.push({
                        numb: forDados.id_quest,
                        question: forDados.enunciado_quest,
                        answer: forDados.opc_certa,
                        options: opcoes,
                        valueQuestion: forDados.pont_quest 
                    });
            }
        }) 

        //selecting all required elements
    const start_btn = document.querySelector(".start_btn button");
    const info_box = document.querySelector(".info_box");
    const exit_btn = info_box.querySelector(".buttons .quit");
    const continue_btn = info_box.querySelector(".buttons .restart");
    const quiz_box = document.querySelector(".quiz_box");
    const result_box = document.querySelector(".result_box");
    const option_list = document.querySelector(".option_list");
    const time_line = document.querySelector("header .time_line");
    const timeText = document.querySelector(".timer .time_left_txt");
    const timeCount = document.querySelector(".timer .timer_sec");
    const backAv = document.querySelector(".back-av");
    const btnBackAv = document.querySelector("#btn-back-av");
    const reach_btn = document.querySelector("#reach-cert");
    const curso = reach_btn.getAttribute('data-cur');
    const alu = reach_btn.getAttribute('data-alu');
    
    var idTent;
    
    // if startQuiz button clicked
    start_btn.onclick = ()=>{
        info_box.classList.add("activeInfo"); //show info box
        backAv.classList.add("fade-av");
    }
    // if exitQuiz button clicked
    exit_btn.onclick = ()=>{
        info_box.classList.remove("activeInfo"); //hide info box
        backAv.classList.remove("fade-av");
    }
    // if continueQuiz button clicked
    continue_btn.onclick = ()=>{
        info_box.classList.remove("activeInfo"); //hide info box
        quiz_box.classList.add("activeQuiz"); //show quiz box
        $.ajax({
            url: '/eadev/base/dashboard/usu_content/alu/avaliacao/content/update_tent.php',
            method: 'POST',
            data: {curso: curso, aluno: alu},
            dataType: 'json'
        }).done(function(dados){
            idTent = dados.tentativa;
            console.log("Você acaba de utilizar uma de suas tentativas, agora restam: "+ dados.tent_restantes +" tentativas.")
        }) 
        showQuetions(0); //calling showQestions function
        queCounter(1); //passing 1 parameter to queCounter
        startTimer(150); //calling startTimer function
        startTimerLine(0); //calling startTimerLine function
    }
    let timeValue =  150;
    let que_count = 0;
    let que_numb = 1;
    let userScore = 0;
    let counter;
    let counterLine;
    let widthValue = 0;
    const restart_quiz = result_box.querySelector(".buttons .restart");
    const quit_quiz = result_box.querySelector(".buttons .quit");

    // if quitQuiz button clicked
    quit_quiz.onclick = function sairQuiz (){
        window.location.reload(); //reload the current window
    }
    btnBackAv.onclick = ()=>{
        window.location.reload(); //reload the current window
    }
    const next_btn = document.querySelector("footer .next_btn");
    const bottom_ques_counter = document.querySelector("footer .total_que");
    // if Next Que button clicked
    next_btn.onclick = ()=>{
        if(que_count < questions.length - 1){ //if question count is less than total question length
            que_count++; //increment the que_count value
            que_numb++; //increment the que_numb value
            showQuetions(que_count); //calling showQestions function
            queCounter(que_numb); //passing que_numb value to queCounter
            clearInterval(counter); //clear counter
            clearInterval(counterLine); //clear counterLine
            startTimer(timeValue); //calling startTimer function
            startTimerLine(widthValue); //calling startTimerLine function
            timeText.textContent = "Tempo restante (segundos)"; //change the timeText to Time Left
            next_btn.classList.remove("show"); //hide the next button
        }else{
            clearInterval(counter); //clear counter
            clearInterval(counterLine); //clear counterLine
            showResult(); //calling showResult function
        }
    }
    // getting questions and options from array
    function showQuetions(index){
        const que_text = document.querySelector(".que_text");
        //creating a new span and div tag for question and option and passing the value using array index
        let que_tag = '<i class="bi bi-link-45deg"></i> - '+ questions[index].question +'</span>';
        let option_tag = '<div class="option"><span>'+ questions[index].options[0] +'</span></div>'
        + '<div class="option"><span>'+ questions[index].options[1] +'</span></div>'
        + '<div class="option"><span>'+ questions[index].options[2] +'</span></div>';
        que_text.innerHTML = que_tag; //adding new span tag inside que_tag
        option_list.innerHTML = option_tag; //adding new div tag inside option_tag
        
        const option = option_list.querySelectorAll(".option");
        // set onclick attribute to all available options
        for(i=0; i < option.length; i++){
            option[i].setAttribute("onclick", "optionSelected(this)");
        }
    }
    // creating the new div tags which for icons
    let tickIconTag = '<div class="icon tick"><i class="fas fa-check"></i></div>';
    let crossIconTag = '<div class="icon cross"><i class="fas fa-times"></i></div>';
    //if user clicked on option
    function optionSelected(answer){
        /*Regex para encontrar os simbolos*/
        const regJacare1 = /(<)+/gm;
        const regJacare2 = /(>)+/gm;
        
        clearInterval(counter); //clear counter
        clearInterval(counterLine); //clear counterLine
        let userAns = answer.textContent; //getting user selected option
      
        userAns = userAns.replace(regJacare1, '&lt;'); //substitui o simbolo
        userAns = userAns.replace(regJacare2, '&gt;'); //substitui o simbolo

        let correcAns = questions[que_count].answer; //getting correct answer from array
        const allOptions = option_list.children.length; //getting all option items
        
        if(userAns == correcAns){ //if user selected option is equal to array's correct answer
            //console.log(questions[que_count].valueQuestion);
            userScore += parseFloat(questions[que_count].valueQuestion); //upgrading score value with 1

            answer.classList.add("correct"); //adding green color to correct selected option
            answer.insertAdjacentHTML("beforeend", tickIconTag); //adding tick icon to correct selected option
            //console.log("Correct Answer");
            //console.log("Your correct answers = " + userScore);
        }else{
            answer.classList.add("incorrect"); //adding red color to correct selected option
            answer.insertAdjacentHTML("beforeend", crossIconTag); //adding cross icon to correct selected option
            //console.log("Wrong Answer");
            for(i=0; i < allOptions; i++){
                if(option_list.children[i].textContent == correcAns){ //if there is an option which is matched to an array answer 
                    option_list.children[i].setAttribute("class", "option correct"); //adding green color to matched option
                    option_list.children[i].insertAdjacentHTML("beforeend", tickIconTag); //adding tick icon to matched option
                    //console.log("Auto selected correct answer.");
                }
            }
        }
        for(i=0; i < allOptions; i++){
            option_list.children[i].classList.add("disabled"); //once user select an option then disabled all options
        }
        next_btn.classList.add("show"); //show the next button if user selected any option
    }
    function showResult(){
        info_box.classList.remove("activeInfo"); //hide info box
        quiz_box.classList.remove("activeQuiz"); //hide quiz box
        result_box.classList.add("activeResult"); //show result box
        const scoreText = result_box.querySelector(".score_text");

        let media = userScore.toFixed(1); //Define a casa decimal arredondando para (x.x)
        media = media * 10; //Altera a formatacao da media
        
            if (media >= 100) {
                media = 100; 
            }else{
                media = media;
            }
        
            if (media >= 70){ // if user scored more than 3
                //creating a new span tag and passing the user score number and total question number
                let scoreTag = '<p class="info-cert-av">Parabéns, você desbloqueou o certificado do curso <span><?php echo $infoCur['sigla_curso'];?></span>! Sua nota foi <span>'+media+'</span> de <span>100</span> pontos.</p>';
                scoreText.innerHTML = scoreTag;  //adding new span tag inside score_Text

                reach_btn.removeAttribute("disabled");

                sairCert = document.querySelector("#closeCert");
                sairCert.removeAttribute("class");

                var certCur; //id do curso que obteve o certificado
                //Ajax para desbloquear o certificado e deixar a avaliacao como concluida
                $.ajax({
                    url: '/eadev/base/dashboard/usu_content/alu/avaliacao/content/alter_cert/cert.php',
                    method: 'POST',
                    data: {curso: curso, aluno: alu},
                    dataType: 'json'
                }).done(function(dados){
                    console.log("Certificado desbloqueado!");
                    certCur = dados.idCurso;
                }) 
                sairCert.onclick = () => {
                    window.location = "/eadev/plataforma.php?content_alu=avaliacoes&msgs=3";
                }
                reach_btn.onclick = () => {
                    window.location = "/eadev/plataforma.php?content_alu=emissao_certificado&msgs=4&data="+certCur;
                }
            
            }
            else if(media >= 50){ // if user scored more than 1
                let scoreTag = '<span>Quase lá! Sua nota foi <p>'+media+'</p> de <p>100</p> pontos.</span>';
                scoreText.innerHTML = scoreTag;
            }
            else{ // if user scored less than 1
                let scoreTag = '<span>Ops, sua nota foi <p>'+media+'</p> de <p>100</p> pontos.</span>';
                scoreText.innerHTML = scoreTag;
            }

        //Envia a media para a pag que faz a interação com o Banco de dados
        $.ajax({
            url: '/eadev/base/dashboard/usu_content/alu/avaliacao/content/update_tent.php',
            method: 'POST',
            data: {tentativa: idTent, media: media},
            dataType: 'json'
        }).done(function(){
            console.log("Sua nota ("+ media +") foi computada!");
        }) 
    }
     
    

    function startTimer(time){
        counter = setInterval(timer, 1000);
        function timer(){
            timeCount.textContent = time; //changing the value of timeCount with time value
            time--; //decrement the time value
            if(time < 9){ //if timer is less than 9
                let addZero = timeCount.textContent; 
                timeCount.textContent = "0" + addZero; //add a 0 before time value
            }
            if(time < 0){ //if timer is less than 0
                clearInterval(counter); //clear counter
                timeText.textContent = "Tempo esgotado"; //change the time text to time off
                const allOptions = option_list.children.length; //getting all option items
                let correcAns = questions[que_count].answer; //getting correct answer from array
                for(i=0; i < allOptions; i++){
                    if(option_list.children[i].textContent == correcAns){ //if there is an option which is matched to an array answer
                        option_list.children[i].setAttribute("class", "option correct"); //adding green color to matched option
                        option_list.children[i].insertAdjacentHTML("beforeend", tickIconTag); //adding tick icon to matched option
                        //console.log("Time Off: Auto selected correct answer.");
                    }
                }
                for(i=0; i < allOptions; i++){
                    option_list.children[i].classList.add("disabled"); //once user select an option then disabled all options
                }
                next_btn.classList.add("show"); //show the next button if user selected any option
            }
        }
    }
    function startTimerLine(time){
        counterLine = setInterval(timer, 273);
        function timer(){
            time += 1; //upgrading time value with 1
            time_line.style.width = time + "px"; //increasing width of time_line with px by time value
            if(time > 549){ //if time value is greater than 549
                clearInterval(counterLine); //clear counterLine
            }
        }
    }
    function queCounter(index){
        //creating a new span tag and passing the question number and total question
        let totalQueCounTag = '<span><p>'+ index +'</p> de <p>'+ questions.length +'</p> Questões</span>';
        bottom_ques_counter.innerHTML = totalQueCounTag;  //adding new span tag inside bottom_ques_counter
    }
</script>
