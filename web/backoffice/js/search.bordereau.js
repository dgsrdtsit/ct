$(function () {

    // Génération bordereau de livraison
    $('#btn-generate-bordereau-livraison').click(function(event) {
        ajaxGenerateBordereauLivraison();

        return false;
    });
})

/**
 * Ajax pour générer le bordereau de livraison
 */
function ajaxGenerateBordereauLivraison() {
    var _loading        = "<i class='fa-li fa fa-spinner fa-spin kl-spin'></i>";
    var _bl_numero      = $('#bl_numero').val();
    var _ct_centre_id   = $('#ct_centre_id').val();

    $('#link-download-bordereau-livraison').show();
    $('#link-download-bordereau-livraison').html(_loading);
    $.ajax({
        type: "POST",
        url: _bordereau_generer_bl_uri,
        cache: false,
        data: { 'ct_centre_id' : _ct_centre_id, 'bl_numero' : _bl_numero},
        success: function(data) {

            var _link_download = "<a href='" + data.url_path + "' title='Télécharger' target='_blank' class='btn btn-success kl-download'>Télécharger</a>"

            $('#link-download-bordereau-livraison').html(_link_download);
        }
    });
}