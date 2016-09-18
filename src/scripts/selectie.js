function show_namen(){
    document.getElementById('huisgenoten').style.display = 'block';
}

function hide_namen(){
    document.getElementById('huisgenoten').style.display = 'none';
}

function tekenAf(id, schoonmaker, categorie, controleur){
    var action = window.confirm(controleur+", wil jij "+schoonmaker+" aftekenen?");
    if(action == 1){
        // Voor de categorieen C en D is er nog geen taak gekozen, omdat dit is vastgelegd
        if(categorie =='C'){
            post_actie.taak = 'Gang'
        }
        else if(categorie =='D'){
            post_actie.taak = 'Vloer'
        }
        post_actie(id, schoonmaker, categorie, controleur);

    }
}

function select_taak(elem){
    if(elem.geselecteerd == 'true'){
        //window.alert('gedeselecteerd');
        elem.geselecteerd = 'false';
        select_taak.taak = ''; //ID is 'func1' of 'func2'
        elem.style.fontWeight = 'inherit';
        elem.style.color = 'black';
        hide_namen();
    }
    else{
        if(select_taak.selected != true){//als de andere taak al is geselecteerd

            var id = '';
            if(elem.id == 'taak1'){
                id = 'taak2';
            }
            else{
                id = 'taak1';
            }
            document.getElementById(id).removeAttribute('style');
            document.getElementById(id).geselecteerd = 'false';
            select_taak.selected = false
        }
        //window.alert('geselecteerd');
        elem.geselecteerd = 'true';
        select_taak.selected = true; // nodig voor wisseling
        elem.style.fontWeight = 'bold';
        elem.style.color = 'rgb(106,211,207)';
        //document.getElementById(schoonmaker).style.display = 'none';
        show_namen();
        post_actie.taak = elem.innerHTML; // nodig voor dataoverdracht
        //alert(elem.innerHTML);
    }
}

function post_actie(id, schoonmaker, categorie, controleur){
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', '../aftekenen.php');
    form.style.display = 'none';
    document.body.appendChild(form);

    var input1 = document.createElement("input");
    input1.setAttribute("name", "id");
    input1.setAttribute("value", id);
    form.appendChild(input1);

    var input2 = document.createElement("input");
    input2.setAttribute("name", "schoonmaker");
    input2.setAttribute("value", schoonmaker);
    form.appendChild(input2);

    var input3 = document.createElement("input");
    input3.setAttribute("name", "taak");
    input3.setAttribute("value", post_actie.taak); // TODO: dit moet niet meer via post_actie.taak gaan, maar netter
    form.appendChild(input3);

    var input4 = document.createElement("input");
    input4.setAttribute("name", "controleur");
    input4.setAttribute("value", controleur);
    form.appendChild(input4);

    form.submit();
}