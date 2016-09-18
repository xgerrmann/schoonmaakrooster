/**
 * Created by XAnder on 15-7-2015.
 */

function deel_in() {
    window.location = '../inst_indeling.php';
}

function reset() {
    //alert("Wil je echt resetten?");

    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', '../inst_reset.php');
    form.style.display = 'none';
    document.body.appendChild(form);

    form.submit();
}

function show_element(elemid) {
    document.getElementById(elemid).style.display = 'inherit';
}
function hide_element(elemid) {
    document.getElementById(elemid).style.display = 'none';
}

function delete_huisgenoot(id, naam) {
    var ans = confirm('Weet je zeker dat je ' + naam + ' wilt verwijderen?');
    if (ans == true) {
        //alert('ID: '+id);
        var form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '../inst_huisgenoten.php');
        form.style.display = 'none';
        document.body.appendChild(form);

        var input1 = document.createElement("input");
        input1.setAttribute("name", "delete");
        input1.setAttribute("value", 1);
        form.appendChild(input1);

        var input3 = document.createElement("input");
        input3.setAttribute("name", "add");
        input3.setAttribute("value", 0);
        form.appendChild(input3);

        var input2 = document.createElement("input");
        input2.setAttribute("name", "id");
        input2.setAttribute("value", id);
        form.appendChild(input2);
        //alert('submit now');
        form.submit();
    }
}

function delete_afwezige(id, naam) {
    var ans = confirm('Weet je zeker dat je de afwezigheid van ' + naam + ' wilt verwijderen?');
    if (ans == true) {
        //alert('ID: '+id);
        var form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '../inst_afwezigheid.php');
        form.style.display = 'none';
        document.body.appendChild(form);

        var input1 = document.createElement("input");
        input1.setAttribute("name", "delete");
        input1.setAttribute("value", 1);
        form.appendChild(input1);

        var input3 = document.createElement("input");
        input3.setAttribute("name", "add");
        input3.setAttribute("value", 0);
        form.appendChild(input3);

        var input2 = document.createElement("input");
        input2.setAttribute("name", "id");
        input2.setAttribute("value", id);
        form.appendChild(input2);
        //alert('submit now');
        form.submit();
    }
}

function delete_vakantie(id, naam) {
    var ans = confirm('Weet je zeker dat je de vakantie: "' + naam + '" wilt verwijderen?');
    if (ans == true) {
        //alert('ID: '+id);
        var form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '../inst_vakanties.php');
        form.style.display = 'none';
        document.body.appendChild(form);

        var input1 = document.createElement("input");
        input1.setAttribute("name", "delete");
        input1.setAttribute("value", 1);
        form.appendChild(input1);

        var input3 = document.createElement("input");
        input3.setAttribute("name", "add");
        input3.setAttribute("value", 0);
        form.appendChild(input3);

        var input2 = document.createElement("input");
        input2.setAttribute("name", "id");
        input2.setAttribute("value", id);
        form.appendChild(input2);
        //alert('submit now');
        form.submit();
    }
}