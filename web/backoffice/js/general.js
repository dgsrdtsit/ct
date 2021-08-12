/**
 * Javascript général
 */

$(function() {
    // Plugins select2
    $(".select2").select2();

    // Datetimepicker
    $(".datetimepicker").datetimepicker({
        locale: 'fr',
        format: 'DD/MM/YYYY'
    });

    // Datepicker
    $(".datepicker").datetimepicker({
        locale: 'fr',
        viewMode: 'years',
        format: 'DD/MM/YYYY',
        maxDate: moment()
    });

    // Datepicker
    $(".datepicker-edit").datetimepicker({
        locale: 'fr',
        viewMode: 'years',
        format: 'DD/MM/YYYY'
    });

    // Yearpicker
    $(".yearpicker").datetimepicker({
        locale: 'fr',
        viewMode: 'years',
        format: 'YYYY',
        maxDate: moment()
    });

    $('.datepicker-change').on('dp.change', function(e){
        var date_value = '01/01/'+ $(this).val();
        $(this).val(date_value);
    })

    // Confirmation suppression
    $('.delete-btn, .delete-btn-custom, .remove-elt').click(function(event) {
        if( !confirm('Etes vous sûr de vouloir supprimer ?') )
            event.preventDefault();
    });

    // Supprimer la classe Error séléctionnée
    $("input").focus(function() {
        $(this).parents('.form-group').removeClass('has-error');
    });
    $("select").focus(function() {
        $(this).parents('.form-group').removeClass('has-error');
    });
    $("textarea").focus(function() {
        $(this).parents('.form-group').removeClass('has-error');
    });

    if (_is_show_notification == 1) {
        // Load notification on page load
        ajaxGetNotification();

        // Request notification by interval of 60 seconds
        setInterval(function() {
            ajaxGetNotification();
        }, 1000 * 60);

        $('body').delegate('#id-view-all-notif', 'click', function () {
            ajaxGetDetailOfAllNotification();
            return false;
        });


    }


});

/*
 * Mettre une erreur sur le champ spécifique
 */
function setErrorClass($this){
    $this.parents('.form-group').addClass('has-error');
}

/**
 * Ajax pour lister les notifications
 */
function ajaxGetNotification() {
    $.ajax({
        type: "POST",
        url: _historique_get_notification_ajax_uri,
        cache: false,
        data: { 'user_id' : _user_id},
        success: function(data) {
            if (data.is_notif == true) {
                var _total_notif = data.total_notif;
                var _notifs = data.notif;
                $('#id-counter-notif').html(_total_notif);
                if (_total_notif == 1) $('#id-counter-notif-li').html("Vous avez " + _total_notif + " notification");
                else $('#id-counter-notif-li').html("Vous avez " + _total_notif + " notifications");
                var _li = "";
                for (var i = 0; i < _notifs.length; i++) {
                    _li += "<li id='id-notif-li-" + _notifs[i].notif_id + "'><a href='#' data-id='" + _notifs[i].notif_id + "'>";
                    _li += "<span class='kl_notif_type pull-left'><i class='fa fa-users text-aqua'></i> " + _notifs[i].notif_type + "</span> <span class='kl_notif_date pull-right'>"
                        + _notifs[i].notif_date + "</span>";
                    _li += "</a></li>";
                }
                $('#id-list-notif-ul').html(_li);
                for (var i = 0; i < _notifs.length; i++) { // Add event listener for each li
                    $("#id-notif-li-"+_notifs[i].notif_id+" a").click(function(){
                        ajaxGetDetailOfNotification($(this).data("id"));
                        return false;
                    });
                }
                if (_notifs.length > 3) {
                    $('#id-view-all-notif').removeClass("hide");
                }
            } else {
                $('#id-counter-notif').html("");
                $('#id-counter-notif-li').html("Vous avez 0 notification");
                $('#id-list-notif-ul').html("");
            }

            $('.notifications-menu').removeClass("open");
        }
    });
}

/**
 * Ajax pour detail d'une notification
 */
function ajaxGetDetailOfNotification(_hist_id) {
    $.ajax({
        type: "POST",
        url: _historique_get_detail_notification_ajax_uri,
        cache: false,
        data: { 'historique_id' : _hist_id},
        success: function(data) {
            if (data) {
                var _tr = "";
                _tr += "<tr><td>"+data.province+"</td><td>"+data.centre+"</td><td>"+data.desc+"</td>";
                _tr += "<td>"+data.type+"</td><td>"+data.date+"</td></tr>";
                $("#detail-notif-table-container").find('table').find('tbody').html(_tr);
                var container = $('#detail-notif-table-container').clone();
                container.find('table').attr('id', 'example');

                var box = bootbox.dialog({
                    show: false,
                    size: 'large',
                    message: container.html(),
                    title: "Details notification",
                    buttons: {
                        ok: {
                            label: "OK",
                            className: "btn-primary",
                            callback: function() {
                                console.log('OK Button');
                            }
                        }
                    }
                });

                box.on("shown.bs.modal", function() {
                    $('#example').DataTable({
                        columnDefs: [
                            { width: 70, targets: 0 },
                            { width: 90, targets: 1 },
                            { width: 90, targets: 3 },
                            { width: 110, targets: 4 }
                        ],
                        }
                    );
                });

                box.modal('show');

                ajaxGetNotification();
            }
        }
    });
}

/**
 * Ajax pour detail d'une notification
 */
function ajaxGetDetailOfAllNotification() {
    $.ajax({
        type: "POST",
        url: _historique_get_detail_all_notifications_ajax_uri,
        cache: false,
        success: function(data) {
            if (data) {
                var _tr = "";
                for (var _i = 0; _i < data.length; _i++) {
                    _tr += "<tr><td>"+data[_i].province+"</td><td>"+data[_i].centre+"</td><td>"+data[_i].desc+"</td>";
                    _tr += "<td>"+data[_i].type+"</td><td>"+data[_i].date+"</td></tr>";
                }
                $("#detail-notif-table-container").find('table').find('tbody').html(_tr);
                var container = $('#detail-notif-table-container').clone();
                container.find('table').attr('id', 'example');

                var box = bootbox.dialog({
                    show: false,
                    size: 'large',
                    message: container.html(),
                    title: "Details notification",
                    buttons: {
                        ok: {
                            label: "OK",
                            className: "btn-primary",
                            callback: function() {
                                console.log('OK Button');
                            }
                        }
                    }
                });

                box.on("shown.bs.modal", function() {
                    $('#example').DataTable({
                            columnDefs: [
                                { width: 70, targets: 0 },
                                { width: 90, targets: 1 },
                                { width: 90, targets: 3 },
                                { width: 110, targets: 4 }
                            ],
                        }
                    );
                });

                box.modal('show');

                ajaxGetNotification();
            }
        }
    });
}