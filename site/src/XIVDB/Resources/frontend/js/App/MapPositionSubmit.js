var MapPositionSubmit =
{
    send: function()
    {
        $('.submit-response').html('');

        if ($('#pos_xyz').val().length == 0) {
            $('.submit-response').html('<div class="alert alert-error mt10"><h5>Error</h5> Please enter an X/Y position</div>');
            return;
        }

        if ($('#pos_map').val().length == 0) {
            $('.submit-response').html('<div class="alert alert-error mt10"><h5>Error</h5> Please select a map</div>');
            return;
        }

        var xy = 'XY: `'+ $('#pos_xyz').val() +'`',
            map = 'Placename: ' + $('#pos_map').val(),
            info = $('#pos_info').val();

        var string = "# Map position submitted: \n\n\n" + [info, map, xy].join("\n\n");
        var admin = $('#pos_admin');

        // if admin or not
        if (admin && admin.val()) {
            var data = {
                position: $('#pos_xyz').val().trim().replace('/', ','),
                placename: $('#pos_map').val(),
                type: $('#pos_type').val(),
                id: $('#pos_id').val(),
            }

            console.log(data);

            $.ajax({
                url: '/update/position',
                data: data,
                method: 'POST',
                success: function(data) {
                    if (data) {
                        location.reload();
                    }
                },
                error: function(data, status, code) {
                    console.error(data);
                    console.error(status, code);
                }
            });
        } else {
            var data =
            {
                url: location.href,
                section: 'Data',
                category: 'Map Position',
                title:  info,
                message: string,
                folder: 'Map Position',
                info: {}
            }

            $.ajax({
                url: '/feedback/submit',
                data: data,
                method: 'POST',
                success: function(data) {
                    $('.submit-response').html('<div class="alert alert-success mt10"><h5>Position submitted, thank you.</h5> This page will update once it has been reviewed</div>');
                },
                error: function(data, status, code) {
                    console.error(data);
                    console.error(status, code);
                }
            });
        }
    }
}