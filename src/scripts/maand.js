function toon_agenda(naam_selected, bool_wissel, id_wissel, pers_wissel) {


    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', '../overzicht_persoonlijk.php');
    form.style.display = 'none';
    document.body.appendChild(form);

    var input1 = document.createElement("input");
    input1.setAttribute("name", "naam_selected");
    input1.setAttribute("value", naam_selected);
    form.appendChild(input1);

    var input2 = document.createElement("input");
    input2.setAttribute("name", "bool_wissel");
    input2.setAttribute("value", bool_wissel);
    form.appendChild(input2);

    var input3 = document.createElement("input");
    input3.setAttribute("name", "id_wissel");
    input3.setAttribute("value", id_wissel);
    form.appendChild(input3);

    var input4 = document.createElement("input");
    input4.setAttribute("name", "pers_wissel");
    input4.setAttribute("value", pers_wissel);
    form.appendChild(input4);

    form.submit();

}

function show_info(elem) {
    if (elem.className == 'active') {
        var count_left = 0;
        var count_right = 0;
        var colspan = 0;
        var text = 0;
        var length = 0;

        // Eerst aantal 'active' elementen tellen links en rechts
        // vervolgens al deze elementen verbergen en dan de ruimte opnemen door colspan van dit element
        if (elem.previousElementSibling) {
            count_left = hide_left(elem.previousElementSibling);
        }
        if (elem.nextElementSibling) {
            count_right = hide_right(elem.nextElementSibling);

        }
        colspan = count_left + count_right + 1;
        elem.colSpan = colspan;
        //alert(colspan);
        change_style(elem);
        elem.style.fontSize = '8pt';
        //elem.innerHTML = info;

        //categorien = arguments.slice(1);
        //alert(categorien[1]);
        var text = '';
        var length = arguments.length;
        //alert(length);
        for (p = 1; p < length; p++) {
            if (p > 1) {
                text += ' & ';
            }
            switch (arguments[p]) {
                case 'A1':  //fall through
                case 'A2':
                    text += 'Keuken / GR';
                    break;
                case 'B1':  //fall through
                case 'B2':
                    text += 'Douche / toiletten';
                    break;
                case 'C':
                    text += 'Gang';
                    break;
                case 'D':
                    text += 'Vloer';
                    break;
                default:
                    text += 'ERROR';
                    break;
            }
            //alert(text);
        }

        // 1e argument is het element dat de funtie aanroept,
        // de overige argumenten zijn de bijbehorende categorien.
        //alert(text);
        elem.innerHTML = text;
    }
}

function change_style(elem) {
    elem.style.backgroundColor = 'rgb(249,102,84)';
    elem.style.borderColor = 'rgb(249,102,84)';
}

function hide_left(elem) {
    var count = 0;
    if ((elem.className == 'active') && (elem.dataset.cat == elem.nextElementSibling.dataset.cat)) {
        // alleen als het huidige elemen de juiste classname heeft
        // en alleen als de categorie exact hetzelfde is
        //alert(elem.dataset.cat);
        elem.style.display = 'none';
        count = count + 1;
        //change_style(elem);
        if (elem.previousElementSibling) { //check of de volgende een sibling is !belangrijk!
            count = count + hide_left(elem.previousElementSibling);
        }
        elem.innerHTML = count;
    }
    return count;
}

function hide_right(elem) {
    var count = 0;
    if ((elem.className == 'active') && (elem.dataset.cat == elem.previousElementSibling.dataset.cat)) { // alleen als het huidige elemen de juiste classname heeft
        elem.style.display = 'none';
        count = count + 1;
        //change_style(elem);
        if (elem.nextElementSibling) { //check of de voorgangenr een sibling is !belangrijk!
            count = count + hide_right(elem.nextElementSibling);
        }
        elem.innerHTML = count;
    }
    return count;
}

function hide_info(elem, day) {
    if (elem.className == 'active') {
        reset_style(elem);
        elem.colSpan = 1;
        if (elem.previousElementSibling) {
            show_left(elem.previousElementSibling);
        }
        if (elem.nextElementSibling) {
            show_right(elem.nextElementSibling);
        }
    }


}

function show_right(elem) {
    if (elem.className == 'active') { // alleen als het huidige elemen de juiste classname heeft
        reset_style(elem);
        if (elem.nextElementSibling) { //check of de volgende een sibling is !belangrijk!
            show_right(elem.nextElementSibling);
        }
    }
}

function show_left(elem) {
    if (elem.className == 'active') { // alleen als het huidige elemen de juiste classname heeft
        reset_style(elem);
        if (elem.previousElementSibling) { //check of de volgende een sibling is !belangrijk!
            show_left(elem.previousElementSibling);
        }
    }
}

function reset_style(elem) {
    elem.innerHTML = elem.dataset.day;
    elem.removeAttribute('style');
    //elem.removeAttribute('style');
}

function wissel(elem, bool_wissel, id_wissel1, pers_wissel, pers_selected) {//$pers_selected is de laatst geselecteerde persoon
    var id_cur_selected = elem.dataset.id;
    if (bool_wissel != 'true') { // Checkt of er reeds een wissel gaande is, of meot worden gestart
        ans = confirm(pers_selected+", wil jij wisselen?");
        if (ans == true) {
            var form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', '../maand.php');
            form.style.display = 'none';
            document.body.appendChild(form);

            var input1 = document.createElement("input");
            input1.setAttribute("name", "bool_wissel");
            input1.setAttribute("value", true); // werkt
            form.appendChild(input1);

            var input2 = document.createElement("input");
            input2.setAttribute("name", "id_selected");
            input2.setAttribute("value", id_cur_selected); // id van zojuist geselecteerde element
            form.appendChild(input2);

            var input3 = document.createElement("input");
            input3.setAttribute("name", "pers_selected");
            input3.setAttribute("value", pers_selected);// naam van zojuist geselecteerde persoon
            form.appendChild(input3);

            form.submit();
        }
    }
    else {
        ans = confirm(pers_wissel + ' wisselen met ' + pers_selected + ' ?');
        if (ans == true) {
            var form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', '../wissel.php');
            form.style.display = 'none';
            document.body.appendChild(form);

            var input1 = document.createElement("input");
            input1.setAttribute("name", "id1");
            input1.setAttribute("value", id_wissel1);
            form.appendChild(input1);

            var input2 = document.createElement("input");
            input2.setAttribute("name", "id2");
            input2.setAttribute("value", id_cur_selected);
            form.appendChild(input2);

            var input3 = document.createElement("input");
            input3.setAttribute("name", "pers_wissel1");
            input3.setAttribute("value", pers_wissel);
            form.appendChild(input3);

            var input4 = document.createElement("input");
            input4.setAttribute("name", "pers_wissel2");
            input4.setAttribute("value", pers_selected);
            form.appendChild(input4);

            form.submit();
        }
    }


}