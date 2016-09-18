function toon_taken(elem, id, naam, categorie) {
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', '../selectie.php');
    form.style.display = 'none';
    document.body.appendChild(form);

    var input1 = document.createElement("input");
    input1.setAttribute("name", "id");
    input1.setAttribute("value", id);
    form.appendChild(input1);

    var input2 = document.createElement("input");
    input2.setAttribute("name", "schoonmaker");
    input2.setAttribute("value", naam);
    form.appendChild(input2);

    var input3 = document.createElement("input");
    input3.setAttribute("name", "categorie");
    input3.setAttribute("value", categorie);
    form.appendChild(input3);

    form.submit();
}

function show_week($week, $jaar){
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', '../overzicht.php');
    form.style.display = 'none';
    document.body.appendChild(form);

    var input1 = document.createElement("input");
    input1.setAttribute("name", "week");
    input1.setAttribute("value", $week);
    form.appendChild(input1);

    var input2 = document.createElement("input");
    input2.setAttribute("name", "jaar");
    input2.setAttribute("value", $jaar);
    form.appendChild(input2);

    form.submit();

}