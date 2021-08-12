$(function () {

    // Génération bordereau de livraison
    $('#btn-generate-utilisation-it').click(function(event) {
        ajaxGenerateFeuilleUsedIT();

        return false;
    });
})

$(function (){
    $('#btn-generate-stock-it').click(function (event){
        ajaxGenerateFeuilleStockIT();

        return false;
    });
})

/**
 * Ajax pour générer le bordereau de livraison
 */
function ajaxGenerateFeuilleUsedIT() {
    var _loading        = "<i class='fa-li fa fa-spinner fa-spin kl-spin'></i>";
    var _ct_fitu_date   = $('#ct_fitu_date').val();
    var _ct_fitu_centre = $('#ct_fitu_centre').val();

    $('#link-download-utilisation-it').show();
    $('#link-download-utilisation-it').html(_loading);
    $.ajax({
        type: "POST",
        url: _generate_used_it_ajax_uri,
        cache: false,
        data: { 'ct_centre_id' : _ct_fitu_centre, 'ct_fitu_date' : _ct_fitu_date},
        success: function(data) {

            var _link_download = "<a href='" + data.url_path + "' title='Télécharger' target='_blank' class='btn btn-success kl-download'>Télécharger</a>"

            $('#link-download-utilisation-it').html(_link_download);
        }
    });
}

function ajaxGenerateFeuilleStockIT()
{
    var _loading        = "<i class='fa-li fa fa-spinner fa-spin kl-spin'></i>";
    var _ct_situ_date   = $('#ct_situ_date').val();
    var _ct_situ_centre = $('#ct_situ_centre').val();


    $('#link-download-stock-it').show();
    $('#link-download-stock-it').html(_loading);
    $.ajax({
        type: "POST",
        url: _generate_stock_it_ajax_uri,
        cache: false,
        data: { 'ct_centre_id' : _ct_situ_centre, 'ct_situ_date' : _ct_situ_date},
        success: function(data) {

            var _link_download = "<a href='" + data.url_path + "' title='Télécharger' target='_blank' class='btn btn-success kl-download'>Télécharger</a>"

            $('#link-download-stock-it').html(_link_download);
        }
    });
}