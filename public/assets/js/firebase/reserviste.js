var firebaseRef = firebase.database().ref();
var i = 1;
function getReserviste(id_depart) {

    if (id_depart) {


        firebaseRef.child('Reservistes/' + id_depart).once("value", function (snapshot) {
            if (!(snapshot.val())) {
                displayMessage("Pour l'instant aucune transaction n'a ete effectuée sur le depart selectionné");

            }
        });

        firebaseRef.child('Reservistes/' + id_depart).on("child_added",
            function (dataSnapshot) {
               
                var reservation = dataSnapshot.val().reservation;
                var userPNumber = dataSnapshot.val().user;
                var state = dataSnapshot.val().state;
                var reservationTime = dataSnapshot.key;

                getUserInfo(userPNumber, reservation, reservationTime , state);



        });
        firebaseRef.child('Reservistes/' + id_depart).on("child_changed",
            function (dataSnapshot) {
                var reservationTime = dataSnapshot.key;
                var reservation = dataSnapshot.val().reservation;
                var state = dataSnapshot.val().state;
                var text = reservation ? "reservation "+state : "achat "+state;
                $("#"+reservationTime).find('.7').text(text);
                i--;
        });
        firebaseRef.child('Reservistes/' + id_depart).on("child_removed",
            function (dataSnapshot) {
                var reservationTime = dataSnapshot.key;
                $( "#"+reservationTime ).remove();
                i--;
        });


    }

}

function displayMessage(message) {
    //on change le message a la place de chargement
    var div_message = document.getElementById("message");
    var bold = div_message.firstElementChild;
    bold.innerText = message;
    //div_message.innerHTML = bold;

}

function getUserInfo(pNumber, reservation, timestamp , state) {


    firebaseRef.child("users/" + pNumber).once("value").then(
        function (snapshot) {
            // on recupere les informations de l'user
            var user_info = snapshot.val();
            var pop = timestamp;
            timestamp = timestamp.substr(0, timestamp.length - 3);
            var time = new Date(timestamp * 1000);
            user_info['date'] = time.toLocaleDateString();
            appendReserviste(user_info, pNumber, reservation, i , pop , state);
            found = true;
            i++;

        });


}

function appendReserviste(user, pNumber, reservation, index , timestamp , state) {

    var titre_table = document.getElementById('liste_transaction');
    var text_indicator = document.getElementById('message');
    var indicateur = text_indicator.cloneNode(true);

    if (user) {
        // il y a belle et bien des reservistes. donc on ajoute des ligne au
        // tableau
        indicateur.firstElementChild.textContent = "";
        titre_table.removeAttribute('hidden');
        text_indicator.parentNode.insertBefore(indicateur, titre_table);
        text_indicator.setAttribute("hidden", true);
        var reserviste_body = document.getElementById("reserviste_rows");
        addLine(user, reserviste_body, pNumber, reservation, index , timestamp , state);

    }

}

function addLine(user, reserviste_body, PNumber, reservation, index , timestamp , state) {
    // on cree la ligne;
    var line = document.createElement('tr');

    var cellules = [];
    var data = [index, user.nom, user.prenom, PNumber, user.contact_proche,
        user.sexe, user.date, reservation ? "reservation "+state : "achat "+state];
        
    line.setAttribute('id' , timestamp);
    for (var i = 0; i < 8; i++) {// donc on creer

        cellules.push(document.createElement('th'));
        cellules[i].setAttribute("scope", 'col');
        var cellH = document.createElement('h6');
        cellH.setAttribute('class', i);
        cellules[i].appendChild(cellH).textContent = data[i];
        line.appendChild(cellules[i]);

    }
    reserviste_body.appendChild(line);

}
