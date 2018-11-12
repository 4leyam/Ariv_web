var database = firebase.database();
var agence_ref = database.ref('Agences');
var depart_ref = database.ref('Departs');


var formulaire = document.forms["agenceAdmin1"];
if (formulaire) {
    var first_name = formulaire.elements["agenceAdmin1_nomAgence"].value;
}

// var champ_nom = document.querySelector('#nom');
// if (champ_nom && champ_nom.value)
// var nom_agence = champ_nom.value;

function add_agence() {
    // qu est ce qui faut faire a la creation d une agence?
    // la cle objet (nom de l etablissement) qui sera suivit des capteur ainsi
    // que de la cote de toutes les agences.
    if(document.forms["agenceAdmin0"].checkValidity()) {

        var nom_agence = document.forms["agenceAdmin0"].elements["agenceAdmin0_nomAgence"].value;
        // console.log("firebase: new agence " + nom_agence);
        var new_agence = {};
        new_agence["Agences/" + nom_agence + "/trigger"] = 0
        new_agence["Agences/" + nom_agence + "/avis"] = 0
        new_agence["Agences/" + nom_agence + "/total_avis"] = 0
        new_agence["Comments/" + nom_agence + "/nbr_vote"] = 0
        database.ref().update(new_agence);
        if (nom_agence) {
            //database.ref().update(new_agence);
            document.forms["agenceAdmin0"].submit();
        }

    } else {
        alert("Fomulaire Non valide");
    }

}



/**
 * methode qui doit etre appelee apres la creation d'un depart.
 * @param id_depart
 * @param place_dispo
 */
function newDepart(id_depart , place_dispo) {
    console.log("on est dans la function");
    var new_depart = {};
    new_depart[id_depart + "/trigger"] = 0;
    new_depart[id_depart + "/place_dispo"] = place_dispo;
    depart_ref.update(new_depart);
}

function agence_trigger() {
    if (document.forms["agenceAdmin1"].checkValidity()) {
        // console.log("declanchement de l'agence numero: " + nom_agence);
        var name_agence = document.forms["agenceAdmin1"].elements["agenceAdmin1_nomAgence"].value;
        var trigger_ref = agence_ref.child(first_name.trim() + "/trigger");
        trigger_ref.once('value').then(function (data_snapshot) {
            // console.log(champ_nom.);
            modif = data_snapshot.val();
            trigger_ref.set(++modif);
            // console.log("valeur de modif: " + modif);
            // var update_agence = {};
            // console.log(!modif);
            // update_agence[nom_agence] = modif + 1;
            // agence_ref.update(update_agence);
            document.forms["agenceAdmin1"].submit();
        });
    } else {
        alert("Fomulaire Non valide");
    }

}

function delete_dep(id_depart) {

        var current_form = document.forms["departAdmin2"];
        //on recupere le nombre de places restante
        var place_dispo = current_form.elements["departAdmin2[placeInit]"].value;

        depart_ref.child(id_depart.trim()+ "/place_dispo").once('value').then(function (snapshot) {

            var current_place = snapshot.val();
            alert(place_dispo + "" +current_place);
            if(place_dispo.toString() ===  current_place.toString()) {
                depart_ref.child(id_depart.trim()).set(null);
                document.forms["departAdmin2"].submit();
            } else {
                alert('Impossible de supprimer ce depart car des transactions on deja ete effectuee sur ce dernier')
            }

        });
        console.log("Suppression de l'agence Numero" + id_depart);

}

/**
 * comme son nom l'indique cette methode permet de supprimmer une agence
 * dab sur firebase et ensuite lance le traitement du formulaire qui lui est destine a suprimmer sur la BDR
 * @param nom_agence
 */
function deleteAgence(nom_agence) {
        console.log("Suppression de l'agence " + nom_agence);

        database.ref("Agences/"+nom_agence.trim()).set(null);
        database.ref("Comments/"+nom_agence.trim()).set(null);
        document.forms["agenceAdmin2"].submit();
}
/**
 * fonction permettant de soumettre les formulaire de changement de donnees de departs
 * tout en notifiant firebase
 * @param id_depart
 * @param lst_lcl_dispo nombre de place disponible lors de la modification.
 */
function depart_trigger(id_depart , lst_lcl_dispo ) {
    //dans la suite penser a aussi ecouter les changement des nombres de places.
    if (!(id_depart === 0)) {

            var current_form = document.forms["departAdmin1"];
            //on recupere le nombre de places restante
            var place_dispo = current_form.elements["departAdmin1[placeInit]"].value;
            var lld = parseInt(lst_lcl_dispo, 10);

            depart_ref.child(id_depart.trim() + "/place_dispo").once('value').then(function (ol_place_dispo) {
                var dispo = parseInt(ol_place_dispo.val(), 10);
                if(dispo) {
                    var local_dispo = parseInt(place_dispo , 10);
                    var reserve = lld - dispo;
                    if(local_dispo >= reserve) {
                        var n_place_dispo = local_dispo - reserve;
                        depart_ref.child(id_depart.trim() + "/place_dispo").set(n_place_dispo);
                        current_form.elements["departAdmin1[placeRestante]"].value = n_place_dispo;
                        current_form.submit();
                    } else {
                        alert("Cette modification n'est pas permise: le nombre de transaction"+reserve+" en cours est superieur au nombre de place precisé"+local_dispo);
                    }

                } else {
                    alert("erreur inconnue code :YP2018001009")
                }
            });
            console.log("declanchement du depart numero: " + id_depart);
    } else {
        alert("identifiant incorrecte.")
    }

}

/**
 * methode permettant de revoquer a l'administrateur la posibiliter de modifier les:
 * origines , destinations , tarif , date de depart
 * au cas ou des transactions ont deja ete effectuees sur le depart.
 * @param depart_id
 * @param tab
 */
function lockIfIsUsed(depart_id , tab) {

        var formName = "departAdmin"+tab
        var current_form = document.forms[formName];
        //on recupere le nombre de places restante
        var place_dispo = current_form.elements[formName+"[placeInit]"].value;
        var disponible = parseInt(place_dispo, 10);

        //ameliorer ce verou en l'appliquant aussi sur la base de donnee via un declancheur

        depart_ref.child(depart_id+ "/place_dispo").on('value' , function (ol_place_dispo) {
            var dispo = parseInt(ol_place_dispo.val(), 10);
            if(disponible === dispo) {
                //donc si aucune transaction n'a encore ete lancee alors on active la modification
                $("#"+formName+" input[name='"+formName+"[origine]']").attr('disabled' , false);
                $("#"+formName+" input[name='"+formName+"[destination]']").attr('disabled' , false);
                $("#"+formName+" input[name='"+formName+"[tarifAdult]']").attr('disabled' , false);
                $("#"+formName+" input[name='"+formName+"[tarifEnfant]']").attr('disabled' , false);
                $("#"+formName+" input[name='"+formName+"[dateDepart][month]']").attr('disabled' , false);
                $("#"+formName+" input[name='"+formName+"[dateDepart][day]']").attr('disabled' , false);
                $("#"+formName+" input[name='"+formName+"[dateDepart][year]']").attr('disabled' , false);
            } else {
                $("#"+formName+" input[name='"+formName+"[origine]']").attr('disabled' , true);
                $("#"+formName+" input[name='"+formName+"[destination]']").attr('disabled' , true);
                $("#"+formName+" input[name='"+formName+"[tarifAdult]']").attr('disabled' , true);
                $("#"+formName+" input[name='"+formName+"[tarifEnfant]']").attr('disabled' , true);
                $("#"+formName+" input[name='"+formName+"[dateDepart][month]']").attr('disabled' , true);
                $("#"+formName+" input[name='"+formName+"[dateDepart][day]']").attr('disabled' , true);
                $("#"+formName+" input[name='"+formName+"[dateDepart][year]']").attr('disabled' , true);
            }
        });
}
