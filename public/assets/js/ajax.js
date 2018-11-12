
var tmp_data = null;

function tmpSave(data) {
    tmp_data = data;
}
function getTmp() {
    return tmp_data;
}

/**
 * methode permettant de recuperer les images des departs et des agences en faisant des requetes asynchrone
 * pour recuperer les url en format json , puis a l'aide d'une autre methode secondaire les charger.
 *
 * @param id
 * @param type
 * @param destination_id
 */
function getImage(id, type, destination_id) {
    if (type === "agence") {
        get("/web/app/Rest/getImage.php", {
            type: 'agence',
            id: id
        }, destination_id);
    } else if (type === "depart") {
        get("/web/app/Rest/getImage.php", {
            type: 'depart',
            id: id
        }, destination_id);
    }
}

function get(uri, param, destination_id) {
    var xhr = new XMLHttpRequest();
    var param_array = [];
    for (p in param) {
        param_array.push(p + "=" + param[p]);
    }
    uri += "?" + param_array.join("&");
    xhr.open("GET", uri, true);
    // true comme dernier paramettre pour des traitements asynchrones.

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // status 200 pour avoir trouvee une correspondance exacte.
            var result = JSON.parse(xhr.responseText);
            console.log(xhr.responseText);
            succes(result["image"], destination_id);
        }
    };

    xhr.send(null);
}

function succes(image_uri, destination_id) {
    // cette fonction est appele quand la recuperaiton de l'image donne en
    // retour une uri
    // ce qui reste a faire c'est de display au bon endroit l'image recupere
    // on recupere l url de la requete afin de display ensuite l'image
    var loaded_img = new Image();
    loaded_img.onload = function () {
        // function anonyme qui sera appele lorsque l'image sera completement
        // telecharge
        var destination_img = document.querySelector(destination_id);
        destination_img.src = loaded_img.src;
    };
    // console.log(image_uri);
    if (image_uri != null)
        loaded_img.src = image_uri;

}

function searchAgence(needle, uri, id) {
    // needle correspond au mots entre pout faire la recherche
    var xhr = new XMLHttpRequest();
    xhr.open("GET", uri, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // status 200 pour avoir trouvee une correspondance exacte.
            var result = JSON.parse(xhr.responseText);
            console.log(xhr.responseText);
            searchHandler(result, id);
        }
    };
    xhr.send(null);

}

function clickEvent(select_type, focus) {


        if (select_type == "agence") {

            document.forms["select_modif_agence_4"].submit();
        } else {

            document.forms["select_modif_depart_4"].submit();
        }


}

function searchHandler(result, id) {

    for (ag in result) {
        var info_agence = document.querySelector(id).innerHTML;

    }

    document.querySelector(id).get

}
