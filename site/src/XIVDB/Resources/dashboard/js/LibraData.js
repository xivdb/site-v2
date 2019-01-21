//
// Game data functionality
//
var durations = [];
class LibraDataClass
{
    constructor()
    {
        this.list = [];
        this.start = null;
    }

    //
    // Process all tables
    //
    processAll()
    {
        $('.status1').html('Waiting ...');

        $('.table tr[data-name]').each((i, element) => {
            var table = $(element).attr('data-name');
            this.list.push(table);
        });

        this.processAllAction();
    }

    //
    // Process all action, allows recurrsion
    //
    processAllAction()
    {
        // if still tables
        if (this.list.length > 0) {
            // get first and shift it off
            var table = this.list[0];
            this.list.shift();

            // log
            console.log('Processing:', table, 'Remaining:', this.list.length);

            // run
            this.processSingle(table, () => {
                this.processAllAction();
            });

            return;
        }

        console.log('Complete!');
    }

    //
    // Ajax request a single process
    //
    processSingle(name, callback)
    {
        var $row = $(`table tr.row-${name}`);
        $row.find('.status1').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing Game Files...');

        // scroll to it
        var rowpos = $row.position().top-400;
        $('body').scrollTop(rowpos);

        $.ajax({
            url: `/libra/import/${name}/process`,
            cache: false,
            dataType: 'json',
            method: 'GET',
            success: function(log)
            {
                // set log
                log = log.length > 0 ? log.join('<br>') : 'Nothing to insert';

                // add status
                $row.find('td').addClass('highlight-success');
                $row.find('.status1').html('<i class="fa fa-check"></i> Complete!');
                $row.after(`<tr><td colspan="5" class="import-log">${log}</td></tr>`);

                // if call back
                if (callback) {
                    callback();
                }
            },
            error: function(data, status, error)
            {
                $row.find('td').addClass('highlight-error');
                $row.find('.status1').html('<i class="fa fa-times"></i> Failed');
                console.error(data, status, error);
            }
        });
    }
}

var LibraData = new LibraDataClass();
